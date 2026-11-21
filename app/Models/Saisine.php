<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Saisine extends Model
{
    use HasFactory;

    protected $fillable = [
        'reference_number',
        'title',
        'description',
        'request_type_id',
        'creator_id',
        'current_stage_id',
        'urgency_level',
        'custom_fields',
        'stage_entered_at',
    ];

    protected $casts = [
        'custom_fields' => 'array',
        'stage_entered_at' => 'datetime',
    ];

    // Relationships

    /**
     * The user who created this saisine.
     */
    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'creator_id');
    }

    /**
     * The request type of this saisine.
     */
    public function requestType(): BelongsTo
    {
        return $this->belongsTo(RequestType::class);
    }

    /**
     * The current workflow stage of this saisine.
     */
    public function currentStage(): BelongsTo
    {
        return $this->belongsTo(WorkflowStage::class, 'current_stage_id');
    }

    /**
     * The versions of this saisine.
     */
    public function versions(): HasMany
    {
        return $this->hasMany(Version::class);
    }

    /**
     * The contributors to this saisine.
     */
    public function contributors(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'saisine_contributors')
            ->wherePivot('contributor_type', 'contributor')
            ->withPivot('contributor_type', 'added_by')
            ->withTimestamps();
    }

    /**
     * The observers of this saisine.
     */
    public function observers(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'saisine_contributors')
            ->wherePivot('contributor_type', 'observer')
            ->withPivot('contributor_type', 'added_by')
            ->withTimestamps();
    }

    /**
     * The validations performed on this saisine.
     */
    public function validations(): HasMany
    {
        return $this->hasMany(Validation::class);
    }

    /**
     * The attachments of this saisine.
     */
    public function attachments(): HasMany
    {
        return $this->hasMany(SaisineAttachment::class);
    }


    // Scopes

    /**
     * Scope to filter saisines for a specific user based on their roles.
     */
    public function scopeForUser(Builder $query, User $user): Builder
    {
        return $query->where(function ($q) use ($user) {
            // Creator
            $q->where('creator_id', $user->id)
                // Contributor
                ->orWhereHas('contributors', fn($q) => $q->where('users.id', $user->id))
                // Observer
                ->orWhereHas('observers', fn($q) => $q->where('users.id', $user->id))
                // Role-based access
                ->orWhereHas('currentStage', function ($q) use ($user) {
                    $userRoles = $user->roles()->pluck('name')->toArray();
                    $q->whereRaw('JSON_OVERLAPS(required_roles, ?)', [json_encode($userRoles)]);
                });
        });
    }

    /**
     * Scope to filter saisines at a specific workflow stage.
     */
    public function scopeAtStage(Builder $query, string $stage): Builder
    {
        return $query->whereHas('currentStage', fn($q) => $q->where('name', $stage));
    }

    /**
     * Scope to filter saisines by urgency level.
     */
    public function scopeByUrgency(Builder $query, string $urgency): Builder
    {
        return $query->where('urgency_level', $urgency);
    }

    // Business Logic Methods

    /**
     * Check if this saisine can be validated by the given user.
     */
    public function canBeValidatedBy(User $user): bool
    {
        if (!$this->currentStage) {
            return false;
        }

        $requiredRoles = $this->currentStage->getRequiredRoles();
        
        return $user->hasAnyRole($requiredRoles);
    }

    /**
     * Advance this saisine to the next workflow stage.
     */
    public function advanceToNextStage(User $validator, string $comment): void
    {
        $nextStage = $this->currentStage->getNextStage();
        
        if (!$nextStage) {
            throw new \Exception('No next stage available');
        }

        $this->current_stage_id = $nextStage->id;
        $this->stage_entered_at = now();
        $this->save();
    }

    /**
     * Request completion and return to previous stage.
     */
    public function requestCompletion(User $validator, string $reason): void
    {
        $previousStage = $this->currentStage->getPreviousStage();
        
        if (!$previousStage) {
            throw new \Exception('No previous stage available');
        }

        $this->current_stage_id = $previousStage->id;
        $this->stage_entered_at = now();
        $this->save();
    }

    /**
     * Check if this saisine is delayed at the current stage.
     */
    public function isDelayed(): bool
    {
        if (!$this->currentStage || !$this->stage_entered_at) {
            return false;
        }

        $expectedDuration = $this->currentStage->getExpectedDuration();
        $daysAtStage = $this->stage_entered_at->diffInDays(now());
        
        return $daysAtStage > $expectedDuration;
    }
}
