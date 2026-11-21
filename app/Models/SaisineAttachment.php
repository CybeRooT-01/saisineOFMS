<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class SaisineAttachment extends Model
{
    use HasFactory;

    protected $fillable = [
        'saisine_id',
        'uploaded_by',
        'filename',
        'original_filename',
        'file_path',
        'mime_type',
        'file_size',
    ];

    protected $casts = [
        'file_size' => 'integer',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    // Relationships

    /**
     * The saisine this attachment belongs to.
     */
    public function saisine(): BelongsTo
    {
        return $this->belongsTo(Saisine::class);
    }

    /**
     * The user who uploaded this attachment.
     */
    public function uploader(): BelongsTo
    {
        return $this->belongsTo(User::class, 'uploaded_by');
    }

    // Helper methods

    /**
     * Get human-readable file size.
     */
    public function getFormattedSize(): string
    {
        $bytes = $this->file_size;
        
        if ($bytes >= 1048576) {
            return number_format($bytes / 1048576, 2) . ' MB';
        } elseif ($bytes >= 1024) {
            return number_format($bytes / 1024, 2) . ' KB';
        }
        
        return $bytes . ' B';
    }

    /**
     * Get file extension.
     */
    public function getExtension(): string
    {
        return pathinfo($this->original_filename, PATHINFO_EXTENSION);
    }
}
