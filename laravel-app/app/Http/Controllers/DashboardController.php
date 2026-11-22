<?php

namespace App\Http\Controllers;

use App\Models\Project;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    /**
     * Display role-specific dashboard.
     */
    public function index()
    {
        $user = auth()->user();

        $projects = match ($user->role) {
            'client' => $user->projects()->withCount('bids')->latest()->get(),
            'admin' => Project::with('client')->withCount('bids')->latest()->get(),
            default => collect(),
        };

        $bids = match ($user->role) {
            'developer' => $user->bids()->with('project')->latest()->get(),
            default => collect(),
        };

        return view('dashboard', compact('projects', 'bids'));
    }
}
