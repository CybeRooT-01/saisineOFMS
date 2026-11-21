<?php

namespace App\Http\Controllers;

use App\Models\Saisine;
use App\Models\User;
use App\Services\AuthorizationService;
use App\Services\NotificationService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class ContributorController extends Controller
{
    public function __construct(
        protected AuthorizationService $authorizationService,
        protected NotificationService $notificationService
    ) {}

    /**
     * Add contributor or observer with notification.
     * Available at all workflow stages.
     * 
     * Requirements: 5.1-5.6
     */
    public function store(Request $request, Saisine $saisine)
    {
        $user = Auth::user();

        // Check authorization
        if (!$this->authorizationService->canAddContributors($user, $saisine)) {
            return back()->with('error', 'You do not have permission to add contributors to this request.');
        }

        // Validate input
        $validated = $request->validate([
            'user_id' => 'required|exists:users,id',
            'contributor_type' => 'required|in:contributor,observer',
        ]);

        // Check if user is already a contributor or observer
        $existingContributor = DB::table('saisine_contributors')
            ->where('saisine_id', $saisine->id)
            ->where('user_id', $validated['user_id'])
            ->first();

        if ($existingContributor) {
            return back()->with('error', 'This user is already added to this request.');
        }

        try {
            // Add contributor/observer
            DB::table('saisine_contributors')->insert([
                'saisine_id' => $saisine->id,
                'user_id' => $validated['user_id'],
                'contributor_type' => $validated['contributor_type'],
                'added_by' => $user->id,
                'created_at' => now(),
                'updated_at' => now(),
            ]);

            // Send notification
            $addedUser = User::find($validated['user_id']);
            $this->notificationService->notifyContributorAdded($saisine, $addedUser);

            $type = $validated['contributor_type'] === 'contributor' ? 'Contributor' : 'Observer';
            return redirect()->route('saisines.show', $saisine)
                ->with('success', "{$type} added successfully.");
        } catch (\Exception $e) {
            return back()->with('error', 'Failed to add contributor. Please try again.');
        }
    }

    /**
     * Remove contributor/observer access.
     * 
     * Requirements: 5.1-5.6
     */
    public function destroy(Saisine $saisine, User $contributorUser)
    {
        $user = Auth::user();

        // Check authorization
        if (!$this->authorizationService->canAddContributors($user, $saisine)) {
            return back()->with('error', 'You do not have permission to remove contributors from this request.');
        }

        try {
            // Remove contributor/observer
            $deleted = DB::table('saisine_contributors')
                ->where('saisine_id', $saisine->id)
                ->where('user_id', $contributorUser->id)
                ->delete();

            if ($deleted) {
                return redirect()->route('saisines.show', $saisine)
                    ->with('success', 'Contributor removed successfully.');
            } else {
                return back()->with('error', 'Contributor not found.');
            }
        } catch (\Exception $e) {
            return back()->with('error', 'Failed to remove contributor. Please try again.');
        }
    }
}
