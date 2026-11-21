<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Version extends Model
{
    use HasFactory;

    protected $fillable = [
        'saisine_id',
        'author_id',
        'version_number',
        'content_snapshot',
        'change_description',
    ];

    protected $casts = [
        'content_snapshot' => 'array',
        'created_at' => 'datetime',
    ];

    public $timestamps = false;

    protected static function boot()
    {
        parent::boot();
        
        static::creating(function ($version) {
            $version->created_at = now();
        });
    }

    // Relationships

    /**
     * The saisine this version belongs to.
     */
    public function saisine(): BelongsTo
    {
        return $this->belongsTo(Saisine::class);
    }

    /**
     * The user who authored this version.
     */
    public function author(): BelongsTo
    {
        return $this->belongsTo(User::class, 'author_id');
    }

    // Methods

    /**
     * Get the differences between this version and another version.
     */
    public function getDiff(Version $otherVersion): array
    {
        $thisSnapshot = $this->content_snapshot ?? [];
        $otherSnapshot = $otherVersion->content_snapshot ?? [];
        
        $diff = [];
        
        // Find changed and new fields
        foreach ($thisSnapshot as $key => $value) {
            if (!isset($otherSnapshot[$key]) || $otherSnapshot[$key] !== $value) {
                $diff[$key] = [
                    'old' => $otherSnapshot[$key] ?? null,
                    'new' => $value,
                ];
            }
        }
        
        // Find removed fields
        foreach ($otherSnapshot as $key => $value) {
            if (!isset($thisSnapshot[$key])) {
                $diff[$key] = [
                    'old' => $value,
                    'new' => null,
                ];
            }
        }
        
        return $diff;
    }
}
