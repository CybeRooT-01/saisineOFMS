<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    /** @use HasFactory<UserFactory> */
    use HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'login',
        'password',
        'structure_id',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    // Relationships

    /**
     * The structure this user belongs to.
     */
    public function structure(): BelongsTo
    {
        return $this->belongsTo(Structure::class);
    }

    /**
     * The roles that belong to the user.
     */
    public function roles(): BelongsToMany
    {
        return $this->belongsToMany(Role::class, 'role_user')
            ->withTimestamps();
    }

    /**
     * The saisines created by this user.
     */
    public function createdSaisines(): HasMany
    {
        return $this->hasMany(Saisine::class, 'creator_id');
    }

    /**
     * The saisines where this user is a contributor.
     */
    public function contributingSaisines(): BelongsToMany
    {
        return $this->belongsToMany(Saisine::class, 'saisine_contributors')
            ->wherePivot('contributor_type', 'contributor')
            ->withPivot('contributor_type', 'added_by')
            ->withTimestamps();
    }

    /**
     * The saisines where this user is an observer.
     */
    public function observingSaisines(): BelongsToMany
    {
        return $this->belongsToMany(Saisine::class, 'saisine_contributors')
            ->wherePivot('contributor_type', 'observer')
            ->withPivot('contributor_type', 'added_by')
            ->withTimestamps();
    }

    // Helper Methods

    /**
     * Get the name of the unique identifier for the user.
     */
    public function username(): string
    {
        return 'login';
    }

    /**
     * Check if the user has a specific role.
     */
    public function hasRole(string $role): bool
    {
        return $this->roles()->where('name', $role)->exists();
    }

    /**
     * Check if the user has any of the given roles.
     */
    public function hasAnyRole(array $roles): bool
    {
        return $this->roles()->whereIn('name', $roles)->exists();
    }

    /**
     * Check if the user can validate at a specific workflow stage.
     */
    public function canValidateStage(string $stage): bool
    {
        $workflowStage = WorkflowStage::where('name', $stage)->first();
        
        if (!$workflowStage) {
            return false;
        }

        $requiredRoles = $workflowStage->required_roles ?? [];
        
        return $this->hasAnyRole($requiredRoles);
    }
}
