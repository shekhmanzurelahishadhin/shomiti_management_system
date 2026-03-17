<?php

namespace App\Http\Controllers;

use App\Models\Committee;
use App\Models\CommitteeMember;
use App\Models\CommitteeDraw;
use App\Models\Member;
use App\Models\ActivityLog;
use Illuminate\Http\Request;

class CommitteeController extends Controller
{
    public function index()
    {
        $committees = Committee::withCount('committeeMembers')->latest()->paginate(15);
        return view('committees.index', compact('committees'));
    }

    public function create()
    {
        return view('committees.create');
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'name'        => 'required|string|max:255',
            'description' => 'nullable|string',
            'status'      => 'required|in:active,completed,inactive',
        ]);

        $committee = Committee::create($data);
        ActivityLog::log('create', "Created committee: {$committee->name}", $committee);

        return redirect()->route('committees.show', $committee)->with('success', 'Committee created.');
    }

    public function show(Committee $committee)
    {
        $committee->load(['committeeMembers.member', 'draws.member']);
        $availableMembers = Member::where('status', 'active')
            ->whereNotIn('id', $committee->committeeMembers->pluck('member_id'))
            ->orderBy('name')->get();

        return view('committees.show', compact('committee', 'availableMembers'));
    }

    public function edit(Committee $committee)
    {
        return view('committees.edit', compact('committee'));
    }

    public function update(Request $request, Committee $committee)
    {
        $data = $request->validate([
            'name'        => 'required|string|max:255',
            'description' => 'nullable|string',
            'status'      => 'required|in:active,completed,inactive',
        ]);

        $committee->update($data);
        ActivityLog::log('update', "Updated committee: {$committee->name}", $committee);

        return redirect()->route('committees.show', $committee)->with('success', 'Committee updated.');
    }

    public function destroy(Committee $committee)
    {
        ActivityLog::log('delete', "Deleted committee: {$committee->name}", $committee);
        $committee->delete();
        return redirect()->route('committees.index')->with('success', 'Committee deleted.');
    }

    public function addMember(Request $request, Committee $committee)
    {
        $data = $request->validate([
            'member_id'         => 'required|exists:members,id',
            'contribution_type' => 'required|in:full,half,quarter',
            'draw_order'        => 'nullable|integer|min:1',
            'joined_at'         => 'nullable|date',
        ]);
        $data['committee_id'] = $committee->id;

        CommitteeMember::create($data);
        return redirect()->route('committees.show', $committee)->with('success', 'Member added to committee.');
    }

    public function removeMember(Committee $committee, CommitteeMember $committeeMember)
    {
        $committeeMember->delete();
        return redirect()->route('committees.show', $committee)->with('success', 'Member removed.');
    }

    public function recordDraw(Request $request, Committee $committee)
    {
        $data = $request->validate([
            'member_id'     => 'required|exists:members,id',
            'draw_order'    => 'required|integer|min:1',
            'draw_date'     => 'required|date',
            'payout_amount' => 'required|numeric|min:0',
            'notes'         => 'nullable|string',
        ]);
        $data['committee_id'] = $committee->id;
        $data['status'] = 'completed';

        CommitteeDraw::create($data);

        // Update committee fund
        $committee->decrement('total_fund', $data['payout_amount']);

        return redirect()->route('committees.show', $committee)->with('success', 'Draw recorded.');
    }
}
