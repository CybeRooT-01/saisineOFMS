<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class MandatoryField extends Model
{
    use HasFactory;

    protected $fillable = [
        'request_type_id',
        'field_name',
        'field_type',
        'field_label',
        'validation_rules',
    ];

    // Relationships

    /**
     * The request type this field belongs to.
     */
    public function requestType(): BelongsTo
    {
        return $this->belongsTo(RequestType::class);
    }
}
