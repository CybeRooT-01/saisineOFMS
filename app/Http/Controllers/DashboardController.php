<?php

namespace App\Http\Controllers;

use App\Models\Saisine;
use App\Models\RequestType;
use App\Models\WorkflowStage;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    /**
     * Display the dashboard with role-based filtering.
     */
    public function index(Request $request)
    {
        $user = Auth::user();
        
        // Start with base query filtered by user roles
        $query = Saisine::with(['creator', 'currentStage', 'requestType'])
            ->forUser($user);
        
        // Apply additional filters if provided
        if ($request->filled('stage')) {
            $query->where('current_stage_id', $request->stage);
        }
        
        if ($request->filled('urgency')) {
            $query->byUrgency($request->urgency);
        }
        
        if ($request->filled('request_type')) {
            $query->where('request_type_id', $request->request_type);
        }
        
        // Get all saisines for statistics (without pagination)
        $allSaisines = (clone $query)->latest()->get();
        
        // Calculate delay status for statistics
        $allSaisines->each(function ($saisine) {
            $saisine->is_delayed = $saisine->isDelayed();
        });
        
        // Get paginated saisines for the table (10 per page)
        $saisines = $query->latest()->paginate(10)->withQueryString();
        
        // Calculate delay status for paginated items
        $saisines->each(function ($saisine) {
            $saisine->is_delayed = $saisine->isDelayed();
        });
        
        // Get filter options
        $stages = WorkflowStage::orderBy('sequence_order')->get();
        $requestTypes = RequestType::all();
        $urgencyLevels = ['critical', 'priority', 'standard'];
        
        return view('dashboard', compact('saisines', 'allSaisines', 'stages', 'requestTypes', 'urgencyLevels'));
    }
}
