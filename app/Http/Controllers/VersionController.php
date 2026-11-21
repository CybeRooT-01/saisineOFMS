<?php

namespace App\Http\Controllers;

use App\Models\Saisine;
use App\Models\Version;
use App\Services\AuthorizationService;
use App\Services\VersioningService;
use Illuminate\Http\Request;

class VersionController extends Controller
{
    protected VersioningService $versioningService;
    protected AuthorizationService $authorizationService;

    public function __construct(
        VersioningService $versioningService,
        AuthorizationService $authorizationService
    ) {
        $this->versioningService = $versioningService;
        $this->authorizationService = $authorizationService;
    }

    /**
     * Display a listing of all versions for a saisine.
     * 
     * Requirements: 6.2-6.5
     */
    public function index(Saisine $saisine)
    {
        // Check authorization
        if (!$this->authorizationService->canViewSaisine(auth()->user(), $saisine)) {
            abort(403, 'You do not have permission to view this request.');
        }

        // Get version history in reverse chronological order
        $versions = $this->versioningService->getVersionHistory($saisine);

        return view('versions.index', compact('saisine', 'versions'));
    }

    /**
     * Display a specific version snapshot.
     * 
     * Requirements: 6.2-6.5
     */
    public function show(Saisine $saisine, Version $version)
    {
        // Check authorization
        if (!$this->authorizationService->canViewSaisine(auth()->user(), $saisine)) {
            abort(403, 'You do not have permission to view this request.');
        }

        // Ensure version belongs to the saisine
        if ($version->saisine_id !== $saisine->id) {
            abort(404, 'Version not found for this request.');
        }

        return view('versions.show', compact('saisine', 'version'));
    }

    /**
     * Compare two versions and show differences.
     * 
     * Requirements: 6.2-6.5
     */
    public function compare(Request $request, Saisine $saisine)
    {
        // Check authorization
        if (!$this->authorizationService->canViewSaisine(auth()->user(), $saisine)) {
            abort(403, 'You do not have permission to view this request.');
        }

        // Validate request
        $request->validate([
            'version1' => 'required|exists:versions,id',
            'version2' => 'required|exists:versions,id',
        ]);

        // Get versions
        $version1 = Version::findOrFail($request->version1);
        $version2 = Version::findOrFail($request->version2);

        // Ensure both versions belong to the saisine
        if ($version1->saisine_id !== $saisine->id || $version2->saisine_id !== $saisine->id) {
            abort(404, 'One or both versions do not belong to this request.');
        }

        // Compare versions
        try {
            $differences = $this->versioningService->compareVersions($version1, $version2);
        } catch (\Exception $e) {
            return back()->with('error', $e->getMessage());
        }

        return view('versions.compare', compact('saisine', 'version1', 'version2', 'differences'));
    }
}
