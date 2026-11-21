<?php

namespace App\Http\Controllers;

use App\Models\Saisine;
use App\Models\Validation;
use App\Models\WorkflowStage;
use App\Services\AuthorizationService;
use App\Services\WorkflowService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ValidationController extends Controller
{
    public function __construct(
        protected AuthorizationService $authorizationService,
        protected WorkflowService $workflowService
    ) {}

    /**
     * Process validation action (validate, partial_validation, total_validation).
     * 
     * Requirements: 4.1-4.6
     */
    public function validate(Request $request, Saisine $saisine)
    {
        $user = Auth::user();

        // Check authorization
        if (!$this->authorizationService->canValidateSaisine($user, $saisine)) {
            return back()->with('error', 'You do not have permission to validate this request.');
        }

        // Validate input
        $validated = $request->validate([
            'action' => 'required|in:validate,partial_validation,total_validation',
            'comment' => 'required|string|max:1000',
        ]);

        // Check if DRJ-specific actions are only used at DRJ Study stage
        if (in_array($validated['action'], ['partial_validation', 'total_validation'])) {
            if ($saisine->currentStage->name !== WorkflowStage::DRJ_STUDY) {
                return back()->with('error', 'This validation action is only available at DRJ Study stage.');
            }
        }

        try {
            $this->workflowService->validateAndAdvance(
                $saisine,
                $user,
                $validated['action'],
                $validated['comment']
            );

            return redirect()->route('saisines.show', $saisine)
                ->with('success', 'Request validated successfully and advanced to the next stage.');
        } catch (\Exception $e) {
            return back()->with('error', $e->getMessage());
        }
    }

    /**
     * Handle completion request with mandatory comment.
     * 
     * Requirements: 4.1-4.6
     */
    public function requestCompletion(Request $request, Saisine $saisine)
    {
        $user = Auth::user();

        // Check authorization
        if (!$this->authorizationService->canValidateSaisine($user, $saisine)) {
            return back()->with('error', 'You do not have permission to request completion for this request.');
        }

        // Validate input - comment is mandatory
        $validated = $request->validate([
            'reason' => 'required|string|max:1000',
        ], [
            'reason.required' => 'A comment is required when requesting completion.',
        ]);

        try {
            $this->workflowService->requestCompletion(
                $saisine,
                $user,
                $validated['reason']
            );

            return redirect()->route('saisines.show', $saisine)
                ->with('success', 'Completion requested. The request has been returned to the previous stage.');
        } catch (\Exception $e) {
            return back()->with('error', $e->getMessage());
        }
    }

    /**
     * Reject the saisine and return it to RJG Qualification stage.
     * Can be done from any stage in the workflow.
     */
    public function reject(Request $request, Saisine $saisine)
    {
        $user = Auth::user();

        // Check authorization
        if (!$this->authorizationService->canValidateSaisine($user, $saisine)) {
            return back()->with('error', 'You do not have permission to reject this request.');
        }

        // Validate input - comment is mandatory
        $validated = $request->validate([
            'reason' => 'required|string|max:1000',
        ], [
            'reason.required' => 'A comment is required when rejecting a request.',
        ]);

        try {
            $this->workflowService->rejectToRJG(
                $saisine,
                $user,
                $validated['reason']
            );

            return redirect()->route('saisines.show', $saisine)
                ->with('success', 'Request rejected and returned to RJG Qualification stage.');
        } catch (\Exception $e) {
            return back()->with('error', $e->getMessage());
        }
    }
}
