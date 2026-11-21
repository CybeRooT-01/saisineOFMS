<?php

namespace App\Services;

use App\Models\Saisine;
use App\Models\User;
use App\Models\Version;
use Illuminate\Support\Collection;

class VersioningService
{
    /**
     * Create a new version for a saisine.
     * 
     * Requirements: 6.1-6.6
     */
    public function createVersion(Saisine $saisine, User $author, array $changes): Version
    {
        // Get the next version number
        $lastVersion = $saisine->versions()->latest('version_number')->first();
        $versionNumber = $lastVersion ? $lastVersion->version_number + 1 : 1;

        // Capture current state of the saisine
        $contentSnapshot = [
            'reference_number' => $saisine->reference_number,
            'title' => $saisine->title,
            'description' => $saisine->description,
            'request_type_id' => $saisine->request_type_id,
            'current_stage_id' => $saisine->current_stage_id,
            'urgency_level' => $saisine->urgency_level,
            'custom_fields' => $saisine->custom_fields,
            'stage_entered_at' => $saisine->stage_entered_at?->toDateTimeString(),
            'attachments' => $saisine->attachments->map(function($attachment) {
                return [
                    'id' => $attachment->id,
                    'original_filename' => $attachment->original_filename,
                    'file_size' => $attachment->file_size,
                    'uploaded_by' => $attachment->uploader->name,
                    'uploaded_at' => $attachment->created_at->toDateTimeString(),
                ];
            })->toArray(),
        ];

        // Create change description
        $changeDescription = $this->formatChangeDescription($changes);

        // Create version record
        $version = Version::create([
            'saisine_id' => $saisine->id,
            'author_id' => $author->id,
            'version_number' => $versionNumber,
            'content_snapshot' => $contentSnapshot,
            'change_description' => $changeDescription,
        ]);

        return $version;
    }

    /**
     * Get version history for a saisine in reverse chronological order.
     * 
     * Requirements: 6.1-6.6
     */
    public function getVersionHistory(Saisine $saisine): Collection
    {
        return $saisine->versions()
            ->with('author')
            ->orderBy('version_number', 'desc')
            ->get();
    }

    /**
     * Compare two versions and return the differences.
     * 
     * Requirements: 6.1-6.6
     */
    public function compareVersions(Version $v1, Version $v2): array
    {
        // Ensure both versions belong to the same saisine
        if ($v1->saisine_id !== $v2->saisine_id) {
            throw new \Exception('Cannot compare versions from different saisines');
        }

        return $v1->getDiff($v2);
    }

    /**
     * Format changes array into a human-readable description.
     */
    protected function formatChangeDescription(array $changes): string
    {
        if (empty($changes)) {
            return 'No changes recorded';
        }

        $descriptions = [];

        foreach ($changes as $key => $value) {
            if (is_array($value)) {
                $descriptions[] = ucfirst(str_replace('_', ' ', $key)) . ': ' . json_encode($value);
            } else {
                $descriptions[] = ucfirst(str_replace('_', ' ', $key)) . ': ' . $value;
            }
        }

        return implode('; ', $descriptions);
    }
}
