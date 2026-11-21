<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class WorkflowStage extends Model
{
    use HasFactory;

    // Stage name constants
    public const BUSINESS_SUBMISSION = 'business_submission';
    public const RJG_QUALIFICATION = 'rjg_qualification';
    public const COMPLIANCE_VALIDATION = 'compliance_validation';
    public const DRJ_STUDY = 'drj_study';
    public const BUSINESS_PARTNER_VALIDATION = 'business_partner_validation';
    public const FINAL_GO_CODIFICATION = 'final_go_codification';
    public const PRINTING_RECOVERY = 'printing_recovery';

    protected $fillable = [
        'name',
        'display_name',
        'sequence_order',
        'expected_duration_days',
        'required_roles',
    ];

    protected $casts = [
        'required_roles' => 'array',
        'sequence_order' => 'integer',
        'expected_duration_days' => 'integer',
    ];

    // Relationships

    /**
     * The saisines at this workflow stage.
     */
    public function saisines(): HasMany
    {
        return $this->hasMany(Saisine::class, 'current_stage_id');
    }

    // Methods

    /**
     * Get the next workflow stage in sequence.
     */
    public function getNextStage(): ?WorkflowStage
    {
        return self::where('sequence_order', $this->sequence_order + 1)->first();
    }

    /**
     * Get the previous workflow stage in sequence.
     */
    public function getPreviousStage(): ?WorkflowStage
    {
        return self::where('sequence_order', $this->sequence_order - 1)->first();
    }

    /**
     * Get the required roles for this stage.
     */
    public function getRequiredRoles(): array
    {
        return $this->required_roles ?? [];
    }

    /**
     * Get the expected duration in days for this stage.
     */
    public function getExpectedDuration(): int
    {
        return $this->expected_duration_days;
    }
}
