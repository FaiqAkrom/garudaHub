<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class BidController extends Controller
{
    //
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
            ->with('success', 'Pemenang berhasil dipilih.');
    }
}
