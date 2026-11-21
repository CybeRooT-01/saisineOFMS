<?php

namespace App\Http\Controllers;

use App\Models\Saisine;
use App\Models\RequestType;
use App\Models\WorkflowStage;
use App\Services\AuthorizationService;
use App\Services\VersioningService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class SaisineController extends Controller
{
    public function __construct(
        protected AuthorizationService $authorizationService,
        protected VersioningService $versioningService
    ) {}

    /**
     * Display a listing of saisines for the current user.
     */
    public function index()
    {
        // "Mes demandes" shows only saisines created by the current user
        $saisines = Saisine::where('creator_id', Auth::id())
            ->with(['requestType', 'currentStage', 'creator'])
            ->orderBy('created_at', 'desc')
            ->paginate(10);

        return view('saisines.index', compact('saisines'));
    }
    /**
     * Display all saisines (not filtered by user).
     */
    public function all()
    {
        $saisines = Saisine::with(['requestType', 'currentStage', 'creator', 'contributors', 'observers', 'validations'])
            ->orderBy('created_at', 'desc')
            ->paginate(10);

        return view('saisines.all', compact('saisines'));
    }


    /**
     * Show the form for creating a new saisine.
     * Requirements: 2.1-2.6
     */
    public function create()
    {
        $requestTypes = RequestType::all();
        
        return view('saisines.create', compact('requestTypes'));
    }

    /**
     * Store a newly created saisine in storage.
     * Requirements: 2.1-2.6, 6.1, 6.6
     */
    public function store(Request $request)
    {
        // Validate basic fields
        $validated = $request->validate([
            'request_type_id' => 'required|exists:request_types,id',
            'title' => 'required|string|max:255',
            'description' => 'required|string',
            'urgency_level' => 'required|in:critical,priority,standard',
        ]);

        // Get request type and validate mandatory fields
        $requestType = RequestType::findOrFail($validated['request_type_id']);
        $mandatoryFields = $requestType->getMandatoryFieldsArray();
        
        // Build validation rules for mandatory fields
        $mandatoryFieldRules = [];
        foreach ($mandatoryFields as $field) {
            $fieldName = 'custom_fields.' . $field['name'];
            $rules = $field['rules'] ?? 'required';
            $mandatoryFieldRules[$fieldName] = $rules;
        }

        // Validate mandatory fields if any exist
        if (!empty($mandatoryFieldRules)) {
            $request->validate($mandatoryFieldRules);
        }

        try {
            DB::transaction(function () use ($request, $validated, &$saisine) {
                // Get the Business Submission stage
                $initialStage = WorkflowStage::where('name', WorkflowStage::BUSINESS_SUBMISSION)->firstOrFail();

                // Generate reference number
                $referenceNumber = 'SAI-' . date('Y') . '-' . str_pad(Saisine::count() + 1, 5, '0', STR_PAD_LEFT);

                // Create the saisine
                $saisine = Saisine::create([
                    'reference_number' => $referenceNumber,
                    'title' => $validated['title'],
                    'description' => $validated['description'],
                    'request_type_id' => $validated['request_type_id'],
                    'creator_id' => Auth::id(),
                    'current_stage_id' => $initialStage->id,
                    'urgency_level' => $validated['urgency_level'],
                    'custom_fields' => $request->input('custom_fields', []),
                    'stage_entered_at' => now(),
                ]);

                // Create initial version
                $this->versioningService->createVersion($saisine, Auth::user(), [
                    'action' => 'created',
                    'initial_stage' => $initialStage->display_name,
                ]);
            });

            return redirect()->route('saisines.show', $saisine)
                ->with('success', 'Request created successfully');
        } catch (\Exception $e) {
            return back()
                ->withInput()
                ->with('error', 'Failed to create request: ' . $e->getMessage());
        }
    }

    /**
     * Display the specified saisine.
     * Requirements: 6.3-6.5, 7.2, 7.3
     */
    public function show(Saisine $saisine)
    {
        // Check authorization
        if (!$this->authorizationService->canViewSaisine(Auth::user(), $saisine)) {
            abort(403, 'You do not have permission to view this request');
        }

        // Load relationships
        $saisine->load([
            'creator',
            'requestType',
            'currentStage',
            'contributors',
            'observers',
            'validations.validator',
            'validations.stage',
            'versions.author',
            'attachments.uploader'
        ]);

        // Get version history
        $versions = $this->versioningService->getVersionHistory($saisine);

        return view('saisines.show', compact('saisine', 'versions'));
    }

    /**
     * Show the form for editing the specified saisine.
     * Requirements: 2.1-2.6, 6.1, 6.6
     */
    public function edit(Saisine $saisine)
    {
        // Check authorization
        if (!$this->authorizationService->canEditSaisine(Auth::user(), $saisine)) {
            abort(403, 'You do not have permission to edit this request');
        }

        $requestTypes = RequestType::all();
        $saisine->load('requestType');

        return view('saisines.edit', compact('saisine', 'requestTypes'));
    }

    /**
     * Update the specified saisine in storage.
     * Requirements: 2.1-2.6, 6.1, 6.6
     */
    public function update(Request $request, Saisine $saisine)
    {
        // Check authorization
        if (!$this->authorizationService->canEditSaisine(Auth::user(), $saisine)) {
            abort(403, 'You do not have permission to edit this request');
        }

        // Validate basic fields
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'required|string',
            'urgency_level' => 'required|in:critical,priority,standard',
        ]);

        // Get mandatory fields for the request type
        $mandatoryFields = $saisine->requestType->getMandatoryFieldsArray();
        
        // Build validation rules for mandatory fields
        $mandatoryFieldRules = [];
        foreach ($mandatoryFields as $field) {
            $fieldName = 'custom_fields.' . $field['name'];
            $rules = $field['rules'] ?? 'required';
            $mandatoryFieldRules[$fieldName] = $rules;
        }

        // Validate mandatory fields if any exist
        if (!empty($mandatoryFieldRules)) {
            $request->validate($mandatoryFieldRules);
        }

        try {
            DB::transaction(function () use ($request, $validated, $saisine) {
                // Track changes
                $changes = [];
                
                if ($saisine->title !== $validated['title']) {
                    $changes['title'] = ['from' => $saisine->title, 'to' => $validated['title']];
                }
                
                if ($saisine->description !== $validated['description']) {
                    $changes['description'] = ['from' => $saisine->description, 'to' => $validated['description']];
                }
                
                if ($saisine->urgency_level !== $validated['urgency_level']) {
                    $changes['urgency_level'] = ['from' => $saisine->urgency_level, 'to' => $validated['urgency_level']];
                }

                $newCustomFields = $request->input('custom_fields', []);
                if ($saisine->custom_fields !== $newCustomFields) {
                    $changes['custom_fields'] = ['from' => $saisine->custom_fields, 'to' => $newCustomFields];
                }

                // Update the saisine
                $saisine->update([
                    'title' => $validated['title'],
                    'description' => $validated['description'],
                    'urgency_level' => $validated['urgency_level'],
                    'custom_fields' => $newCustomFields,
                ]);

                // Create new version if there were changes
                if (!empty($changes)) {
                    $this->versioningService->createVersion($saisine, Auth::user(), [
                        'action' => 'updated',
                        'changes' => $changes,
                    ]);
                }
            });

            return redirect()->route('saisines.show', $saisine)
                ->with('success', 'Request updated successfully');
        } catch (\Exception $e) {
            return back()
                ->withInput()
                ->with('error', 'Failed to update request: ' . $e->getMessage());
        }
    }

    /**
     * Remove the specified saisine from storage.
     * Only the creator can delete their own saisine.
     */
    public function destroy(Saisine $saisine)
    {
        // Check authorization - only creator can delete
        if ($saisine->creator_id !== Auth::id()) {
            abort(403, 'You do not have permission to delete this request');
        }

        try {
            DB::transaction(function () use ($saisine) {
                // Delete related records
                $saisine->contributors()->delete();
                $saisine->validations()->delete();
                $saisine->versions()->delete();
                
                // Delete the saisine
                $saisine->delete();
            });

            return redirect()->route('saisines.index')
                ->with('success', 'Request deleted successfully');
        } catch (\Exception $e) {
            return back()
                ->with('error', 'Failed to delete request: ' . $e->getMessage());
        }
    }

    /**
     * Export saisine as PDF.
     */
    public function export(Saisine $saisine)
    {
        // Check authorization
        if (!$this->authorizationService->canViewSaisine(Auth::user(), $saisine)) {
            abort(403, 'You do not have permission to export this request');
        }

        // Load relationships
        $saisine->load([
            'creator',
            'requestType',
            'currentStage',
            'contributors',
            'observers',
            'validations.validator',
            'validations.stage',
            'attachments.uploader'
        ]);

        $pdf = \PDF::loadView('saisines.pdf', compact('saisine'));
        
        return $pdf->download($saisine->reference_number . '.pdf');
    }
}
