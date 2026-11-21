<?php

namespace App\Http\Controllers;

use App\Models\Saisine;
use App\Models\SaisineAttachment;
use App\Services\AuthorizationService;
use App\Services\VersioningService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class AttachmentController extends Controller
{
    public function __construct(
        protected AuthorizationService $authorizationService,
        protected VersioningService $versioningService
    ) {}

    /**
     * Store a new attachment for a saisine.
     */
    public function store(Request $request, Saisine $saisine)
    {
        // Check authorization - user must be able to view the saisine
        if (!$this->authorizationService->canViewSaisine(Auth::user(), $saisine)) {
            abort(403, 'You do not have permission to add attachments to this request.');
        }

        // Validate file
        $request->validate([
            'file' => 'required|file|mimes:pdf,xlsx,xls,docx,doc|max:10240', // 10MB max
        ], [
            'file.required' => 'Please select a file to upload.',
            'file.mimes' => 'Only PDF, Excel, and Word documents are allowed.',
            'file.max' => 'File size must not exceed 10MB.',
        ]);

        try {
            $file = $request->file('file');
            $originalFilename = $file->getClientOriginalName();
            $filename = Str::uuid() . '.' . $file->getClientOriginalExtension();
            
            // Store file in storage/app/saisine-attachments
            $filePath = $file->storeAs('saisine-attachments', $filename);

            // Create attachment record
            $attachment = SaisineAttachment::create([
                'saisine_id' => $saisine->id,
                'uploaded_by' => Auth::id(),
                'filename' => $filename,
                'original_filename' => $originalFilename,
                'file_path' => $filePath,
                'mime_type' => $file->getMimeType(),
                'file_size' => $file->getSize(),
            ]);

            // Create version to track attachment addition
            $this->versioningService->createVersion($saisine, Auth::user(), [
                'action' => 'attachment_added',
                'filename' => $originalFilename,
                'file_size' => $file->getSize(),
            ]);

            return back()->with('success', 'File uploaded successfully.');
        } catch (\Exception $e) {
            return back()->with('error', 'Failed to upload file: ' . $e->getMessage());
        }
    }

    /**
     * Download an attachment.
     */
    public function download(Saisine $saisine, SaisineAttachment $attachment)
    {
        // Check authorization
        if (!$this->authorizationService->canViewSaisine(Auth::user(), $saisine)) {
            abort(403, 'You do not have permission to download this file.');
        }

        // Verify attachment belongs to saisine
        if ($attachment->saisine_id !== $saisine->id) {
            abort(404);
        }

        return Storage::download($attachment->file_path, $attachment->original_filename);
    }

    /**
     * Delete an attachment.
     */
    public function destroy(Saisine $saisine, SaisineAttachment $attachment)
    {
        // Check authorization - only uploader or creator can delete
        if ($attachment->uploaded_by !== Auth::id() && $saisine->creator_id !== Auth::id()) {
            abort(403, 'You do not have permission to delete this file.');
        }

        // Verify attachment belongs to saisine
        if ($attachment->saisine_id !== $saisine->id) {
            abort(404);
        }

        try {
            // Store attachment info before deletion for version tracking
            $attachmentInfo = [
                'filename' => $attachment->original_filename,
                'file_size' => $attachment->file_size,
            ];

            // Delete file from storage
            Storage::delete($attachment->file_path);
            
            // Delete record
            $attachment->delete();

            // Create version to track attachment deletion
            $this->versioningService->createVersion($saisine, Auth::user(), [
                'action' => 'attachment_deleted',
                'filename' => $attachmentInfo['filename'],
                'file_size' => $attachmentInfo['file_size'],
            ]);

            return back()->with('success', 'File deleted successfully.');
        } catch (\Exception $e) {
            return back()->with('error', 'Failed to delete file: ' . $e->getMessage());
        }
    }
}
