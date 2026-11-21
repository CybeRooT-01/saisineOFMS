<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Validation extends Model
{
    use HasFactory;

    // Action constants
    public const ACTION_VALIDATE = 'validate';
    public const ACTION_REQUEST_COMPLETION = 'request_completion';
    public const ACTION_PARTIAL_VALIDATION = 'partial_validation';
    public const ACTION_TOTAL_VALIDATION = 'total_validation';
    public const ACTION_REJECT = 'reject';

    protected $fillable = [
        'saisine_id',
        'validator_id',
        'stage_id',
        'action',
        'comment',
    ];

    protected $casts = [
        'created_at' => 'datetime',
    ];

    public $timestamps = false;

    protected static function boot()
    {
        parent::boot();
        
        static::creating(function ($validation) {
            $validation->created_at = now();
        });
    }

    // Relationships

    /**
     * The saisine this validation belongs to.
     */
    public function saisine(): BelongsTo
    {
        return $this->belongsTo(Saisine::class);
    }

    /**
     * The user who performed this validation.
     */
    public function validator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'validator_id');
    }

    /**
     * The workflow stage where this validation was performed.
     */
    public function stage(): BelongsTo
    {
        return $this->belongsTo(WorkflowStage::class);
    }
}
