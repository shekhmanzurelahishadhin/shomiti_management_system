<?php

namespace App\Http\Controllers;

use App\Models\Election;
use App\Models\ElectionPosition;
use App\Models\ElectionCandidate;
use App\Models\ElectionVote;
use App\Models\ElectionResult;
use App\Models\Member;
use App\Models\ActivityLog;
use Illuminate\Http\Request;

class ElectionController extends Controller
{
    /* ─── List all elections ─── */
    public function index()
    {
        $elections = Election::withCount(['candidates','votes','positions'])
                             ->latest()->paginate(10);
        return view('elections.index', compact('elections'));
    }

    /* ─── Create election form ─── */
    public function create()
    {
        $positions = ['সভাপতি','সহ-সভাপতি','সাধারণ সম্পাদক','সহ-সাধারণ সম্পাদক',
                      'কোষাধ্যক্ষ','দপ্তর সম্পাদক','সদস্য'];
        return view('elections.create', compact('positions'));
    }

    /* ─── Store new election ─── */
    public function store(Request $request)
    {
        $data = $request->validate([
            'title'            => 'required|string|max:255',
            'description'      => 'nullable|string',
            'election_year'    => 'required|integer|min:2020|max:2100',
            'nomination_start' => 'required|date',
            'nomination_end'   => 'required|date|after_or_equal:nomination_start',
            'voting_start'     => 'required|date|after_or_equal:nomination_end',
            'voting_end'       => 'required|date|after_or_equal:voting_start',
            'positions'        => 'required|array|min:1',
            'positions.*.name' => 'required|string|max:100',
            'positions.*.seats'=> 'required|integer|min:1',
        ]);

        $data['created_by'] = auth()->id();
        $election = Election::create($data);

        foreach ($request->positions as $pos) {
            $election->positions()->create([
                'position_name' => $pos['name'],
                'seats'         => $pos['seats'],
            ]);
        }

        ActivityLog::log('create', "নির্বাচন তৈরি: {$election->title} ({$election->election_year})", $election);
        return redirect()->route('elections.show', $election)
                         ->with('success', 'নির্বাচন সফলভাবে তৈরি হয়েছে।');
    }

    /* ─── Show election detail ─── */
    public function show(Election $election)
    {
        $election->load(['positions.candidates.member','results.member','results.position']);
        $activeMembers = Member::where('status','active')->orderBy('name')->get();

        // For voting: find current auth user's member record (if any)
        $voterMember = null;
        if (auth()->user()->hasRole('Member')) {
            $voterMember = Member::where('phone', auth()->user()->phone)
                                 ->orWhere('name', auth()->user()->name)
                                 ->first();
        }

        return view('elections.show', compact('election','activeMembers','voterMember'));
    }

    /* ─── Update election status ─── */
    public function updateStatus(Request $request, Election $election)
    {
        $request->validate(['status' => 'required|in:upcoming,nomination,voting,counting,completed,cancelled']);
        $election->update(['status' => $request->status]);
        ActivityLog::log('update', "নির্বাচনের অবস্থা পরিবর্তন: {$election->title} → {$request->status}", $election);
        return back()->with('success', 'নির্বাচনের অবস্থা আপডেট হয়েছে।');
    }

    /* ─── Add candidate ─── */
    public function addCandidate(Request $request, Election $election)
    {
        $data = $request->validate([
            'election_position_id' => 'required|exists:election_positions,id',
            'member_id'            => 'required|exists:members,id',
            'manifesto'            => 'nullable|string|max:1000',
        ]);
        $data['election_id'] = $election->id;
        $data['status']      = 'approved';

        // Prevent duplicate
        $exists = ElectionCandidate::where('election_position_id', $data['election_position_id'])
                                   ->where('member_id', $data['member_id'])->exists();
        if ($exists) {
            return back()->with('error', 'এই প্রার্থী এই পদে ইতিমধ্যে মনোনীত আছেন।');
        }

        ElectionCandidate::create($data);
        ActivityLog::log('create', "প্রার্থী যোগ: election #{$election->id}", null);
        return back()->with('success', 'প্রার্থী সফলভাবে যোগ হয়েছে।');
    }

    /* ─── Remove candidate ─── */
    public function removeCandidate(Election $election, ElectionCandidate $candidate)
    {
        $candidate->delete();
        return back()->with('success', 'প্রার্থী বাদ দেওয়া হয়েছে।');
    }

    /* ─── Cast vote ─── */
    public function castVote(Request $request, Election $election)
    {
        if (!$election->isVotingOpen()) {
            return back()->with('error', 'এই মুহূর্তে ভোটগ্রহণ খোলা নেই।');
        }

        $request->validate([
            'votes'   => 'required|array',
            'votes.*' => 'required|exists:election_candidates,id',
            'voter_member_id' => 'required|exists:members,id',
        ]);

        $voterId = $request->voter_member_id;

        foreach ($request->votes as $positionId => $candidateId) {
            // Check already voted for this position
            $alreadyVoted = ElectionVote::where('election_position_id', $positionId)
                                        ->where('voter_member_id', $voterId)
                                        ->exists();
            if ($alreadyVoted) continue;

            ElectionVote::create([
                'election_id'            => $election->id,
                'election_position_id'   => $positionId,
                'election_candidate_id'  => $candidateId,
                'voter_member_id'        => $voterId,
            ]);
        }

        ActivityLog::log('vote', "ভোট প্রদান: member #{$voterId} — election #{$election->id}", null);
        return back()->with('success', 'আপনার ভোট সফলভাবে গ্রহণ করা হয়েছে।');
    }

    /* ─── Count votes & publish results ─── */
    public function countVotes(Election $election)
    {
        // Delete old results
        $election->results()->delete();

        foreach ($election->positions as $position) {
            $candidates = $position->candidates()
                                   ->where('status','approved')
                                   ->withCount('votes')
                                   ->orderByDesc('votes_count')
                                   ->get();

            foreach ($candidates as $idx => $candidate) {
                ElectionResult::create([
                    'election_id'            => $election->id,
                    'election_position_id'   => $position->id,
                    'election_candidate_id'  => $candidate->id,
                    'member_id'              => $candidate->member_id,
                    'vote_count'             => $candidate->votes_count,
                    'is_elected'             => $idx < $position->seats,
                ]);
            }
        }

        $election->update(['status' => 'completed']);
        ActivityLog::log('update', "নির্বাচন ফলাফল প্রকাশ: {$election->title}", $election);
        return back()->with('success', 'ভোট গণনা সম্পন্ন। ফলাফল প্রকাশিত হয়েছে।');
    }

    /* ─── Public results page ─── */
    public function results(Election $election)
    {
        $election->load(['results' => fn($q) => $q->with(['member','position'])->orderBy('election_position_id')->orderByDesc('vote_count')]);
        $positionResults = $election->results->groupBy('election_position_id');
        return view('elections.results', compact('election','positionResults'));
    }

    /* ─── Delete election ─── */
    public function destroy(Election $election)
    {
        ActivityLog::log('delete', "নির্বাচন মুছে ফেলা: {$election->title}", $election);
        $election->delete();
        return redirect()->route('elections.index')->with('success', 'নির্বাচন মুছে ফেলা হয়েছে।');
    }
}
