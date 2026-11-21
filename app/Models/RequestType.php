<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class RequestType extends Model
{
    use HasFactory;

    // Request type constants
    public const CONTRACT = 'contract';
    public const AMENDMENT = 'amendment';
    public const LEGAL_OPINION = 'legal_opinion';
    public const COMPLIANCE_REVIEW = 'compliance_review';
    public const REGULATORY_INQUIRY = 'regulatory_inquiry';

    protected $fillable = [
        'name',
        'display_name',
        'description',
    ];

    // Relationships

    /**
     * The mandatory fields for this request type.
     */
    public function mandatoryFields(): HasMany
    {
        return $this->hasMany(MandatoryField::class);
    }

    /**
     * The saisines of this request type.
     */
    public function saisines(): HasMany
    {
        return $this->hasMany(Saisine::class);
    }

    // Methods

    /**
     * Get mandatory fields as an array.
     */
    public function getMandatoryFieldsArray(): array
    {
        return $this->mandatoryFields()
            ->get()
            ->map(fn($field) => [
                'name' => $field->field_name,
                'type' => $field->field_type,
                'label' => $field->field_label,
                'rules' => $field->validation_rules,
            ])
            ->toArray();
    }
}
