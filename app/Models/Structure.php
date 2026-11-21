<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Structure extends Model
{
    use HasFactory;

    protected $fillable = [
        'libelle',
        'description',
        'fullpath',
        'parent_structure_id',
    ];

    /**
     * Get the parent structure.
     */
    public function parent(): BelongsTo
    {
        return $this->belongsTo(Structure::class, 'parent_structure_id');
    }

    /**
     * Get the child structures.
     */
    public function children(): HasMany
    {
        return $this->hasMany(Structure::class, 'parent_structure_id');
    }

    /**
     * Get all users in this structure.
     */
    public function users(): HasMany
    {
        return $this->hasMany(User::class, 'structure_id');
    }

    /**
     * Calculate and update the fullpath based on parent.
     */
    public function updateFullpath(): void
    {
        if ($this->parent) {
            $this->fullpath = $this->parent->fullpath . '/' . $this->libelle;
        } else {
            $this->fullpath = $this->libelle;
        }
        $this->saveQuietly();

        // Update all children fullpaths recursively
        foreach ($this->children as $child) {
            $child->updateFullpath();
        }
    }

    /**
     * Boot method to handle model events.
     */
    protected static function boot()
    {
        parent::boot();

        static::creating(function ($structure) {
            if ($structure->parent) {
                $structure->fullpath = $structure->parent->fullpath . '/' . $structure->libelle;
            } else {
                $structure->fullpath = $structure->libelle;
            }
        });

        static::updating(function ($structure) {
            if ($structure->isDirty('libelle') || $structure->isDirty('parent_structure_id')) {
                if ($structure->parent) {
                    $structure->fullpath = $structure->parent->fullpath . '/' . $structure->libelle;
                } else {
                    $structure->fullpath = $structure->libelle;
                }
            }
        });

        static::updated(function ($structure) {
            // Update children fullpaths when parent changes
            if ($structure->wasChanged('fullpath')) {
                foreach ($structure->children as $child) {
                    $child->updateFullpath();
                }
            }
        });
    }
}
