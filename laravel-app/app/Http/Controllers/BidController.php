<?php

namespace App\Http\Controllers;

use App\Models\Bid;
use App\Models\Project;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class BidController extends Controller
{
    /**
     * Store a newly created bid in storage.
     */
    public function store(Request $request, Project $project)
    {
        if ($project->status !== 'open') {
            return back()->withErrors(['proposal' => 'Lelang untuk proyek ini sudah ditutup.']);
        }

        $validated = $request->validate([
            'price' => 'required|numeric|min:1',
            'duration_days' => 'required|integer|min:1',
            'proposal' => 'required|string',
        ]);

        $alreadyBid = $project->bids()->where('developer_id', auth()->id())->exists();
        if ($alreadyBid) {
            return back()->withErrors(['proposal' => 'Anda sudah mengajukan penawaran untuk proyek ini.']);
        }

        $project->bids()->create([
            'developer_id' => auth()->id(),
            'price' => $validated['price'],
            'duration_days' => $validated['duration_days'],
            'proposal' => $validated['proposal'],
            'status' => 'pending',
        ]);

        return redirect()->route('projects.show', $project)->with('success', 'Penawaran berhasil dikirim.');
    }

    /**
     * Update the specified bid in storage.
     */
    public function update(Request $request, Bid $bid)
    {
        abort_unless($bid->developer_id === auth()->id(), 403);

        $validated = $request->validate([
            'price' => 'required|numeric|min:1',
            'duration_days' => 'required|integer|min:1',
            'proposal' => 'required|string',
        ]);

        $bid->update($validated);

        return redirect()->route('projects.show', $bid->project_id)->with('success', 'Penawaran berhasil diperbarui.');
    }

    /**
     * Remove the specified bid from storage.
     */
    public function destroy(Bid $bid)
    {
        abort_unless($bid->developer_id === auth()->id(), 403);

        $projectId = $bid->project_id;
        $bid->delete();

        return redirect()->route('projects.show', $projectId)->with('success', 'Penawaran berhasil dibatalkan.');
    }

    /**
     * Accept a winning bid.
     */
    public function accept(Bid $bid)
    {
        $project = $bid->project;

        abort_unless($project->client_id === auth()->id(), 403);

        DB::transaction(function () use ($bid, $project) {
            $project->bids()->where('id', '!=', $bid->id)->update(['status' => 'rejected']);
            $bid->update(['status' => 'won']);
            $project->update(['status' => 'awarded']);
        });

        return redirect()->route('projects.show', $project)
            ->with('success', 'Pemenang lelang berhasil dipilih.');
    }
}
