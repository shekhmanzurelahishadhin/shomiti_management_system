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
    public function index()
    {
        $elections = Election::withCount(['candidates','votes','positions'])->latest()->paginate(10);
        return view('elections.index', compact('elections'));
    }

    public function create()
    {
        $this->authorizeAdmin();
        $positions = ['সভাপতি','সহ-সভাপতি','সাধারণ সম্পাদক','সহ-সাধারণ সম্পাদক',
                      'কোষাধ্যক্ষ','দপ্তর সম্পাদক','সদস্য'];
        return view('elections.create', compact('positions'));
    }

    public function store(Request $request)
    {
        $this->authorizeAdmin();
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

        ActivityLog::log('create', "নির্বাচন তৈরি: {$election->title}", $election);
        return redirect()->route('elections.show', $election)->with('success', 'নির্বাচন তৈরি হয়েছে।');
    }

    public function show(Election $election)
    {
        $election->load(['positions.candidates.member','results.member','results.position']);
        $activeMembers = Member::where('status','active')->orderBy('name')->get();

        // Get current user's linked member record
        $voterMember = auth()->user()->getLinkedMember();

        // Check if this member has already voted (per position)
        $votedPositions = [];
        if ($voterMember) {
            $votedPositions = ElectionVote::where('election_id', $election->id)
                                          ->where('voter_member_id', $voterMember->id)
                                          ->pluck('election_position_id')
                                          ->toArray();
        }

        $hasVotedAll = false;
        if ($voterMember && $election->isVotingOpen()) {
            $posCount    = $election->positions->count();
            $hasVotedAll = count($votedPositions) >= $posCount;
        }

        return view('elections.show', compact('election','activeMembers','voterMember','votedPositions','hasVotedAll'));
    }

    public function updateStatus(Request $request, Election $election)
    {
        $this->authorizeAdmin();
        $request->validate(['status' => 'required|in:upcoming,nomination,voting,counting,completed,cancelled']);
        $election->update(['status' => $request->status]);
        ActivityLog::log('update', "নির্বাচনের অবস্থা: {$election->title} → {$request->status}", $election);
        return back()->with('success', 'নির্বাচনের অবস্থা আপডেট হয়েছে।');
    }

    public function addCandidate(Request $request, Election $election)
    {
        $this->authorizeAdmin();
        $data = $request->validate([
            'election_position_id' => 'required|exists:election_positions,id',
            'member_id'            => 'required|exists:members,id',
            'manifesto'            => 'nullable|string|max:1000',
        ]);
        $data['election_id'] = $election->id;
        $data['status']      = 'approved';

        $exists = ElectionCandidate::where('election_position_id', $data['election_position_id'])
                                   ->where('member_id', $data['member_id'])->exists();
        if ($exists) return back()->with('error', 'এই প্রার্থী ইতিমধ্যে মনোনীত আছেন।');

        ElectionCandidate::create($data);
        return back()->with('success', 'প্রার্থী যোগ হয়েছে।');
    }

    public function removeCandidate(Election $election, ElectionCandidate $candidate)
    {
        $this->authorizeAdmin();
        $candidate->delete();
        return back()->with('success', 'প্রার্থী বাদ দেওয়া হয়েছে।');
    }

    /** Cast vote — uses auth user's linked member record */
    public function castVote(Request $request, Election $election)
    {
        if (!$election->isVotingOpen()) {
            return back()->with('error', 'এই মুহূর্তে ভোটগ্রহণ খোলা নেই।');
        }

        // Get voter from logged-in user's linked member
        $voterMember = auth()->user()->getLinkedMember();

        if (!$voterMember) {
            return back()->with('error', 'ভোট দিতে হলে আপনার অ্যাকাউন্টের সাথে সদস্য প্রোফাইল লিঙ্ক থাকতে হবে।');
        }

        if ($voterMember->status !== 'active') {
            return back()->with('error', 'শুধুমাত্র সক্রিয় সদস্য ভোট দিতে পারবেন।');
        }

        $request->validate([
            'votes'   => 'required|array|min:1',
            'votes.*' => 'required|integer|exists:election_candidates,id',
        ]);

        $votedCount = 0;
        $skipped    = 0;

        foreach ($request->votes as $positionId => $candidateId) {
            // Verify candidate belongs to this election and position
            $candidateOk = ElectionCandidate::where('id', $candidateId)
                                            ->where('election_id', $election->id)
                                            ->where('election_position_id', $positionId)
                                            ->where('status','approved')
                                            ->exists();
            if (!$candidateOk) { $skipped++; continue; }

            // One vote per position per member
            $alreadyVoted = ElectionVote::where('election_id', $election->id)
                                        ->where('election_position_id', $positionId)
                                        ->where('voter_member_id', $voterMember->id)
                                        ->exists();
            if ($alreadyVoted) { $skipped++; continue; }

            ElectionVote::create([
                'election_id'           => $election->id,
                'election_position_id'  => $positionId,
                'election_candidate_id' => $candidateId,
                'voter_member_id'       => $voterMember->id,
            ]);
            $votedCount++;
        }

        ActivityLog::log('vote', "ভোট: {$voterMember->name} — {$election->title} ({$votedCount} পদে)", null);

        if ($votedCount > 0) {
            return back()->with('success', "আপনার ভোট সফলভাবে গ্রহণ হয়েছে ({$votedCount}টি পদে)।");
        }

        return back()->with('error', 'আপনি ইতিমধ্যে সব পদে ভোট দিয়েছেন।');
    }

    public function countVotes(Election $election)
    {
        $this->authorizeAdmin();
        $election->results()->delete();

        foreach ($election->positions as $position) {
            $candidates = $position->candidates()
                                   ->where('status','approved')
                                   ->withCount('votes')
                                   ->orderByDesc('votes_count')
                                   ->get();

            foreach ($candidates as $idx => $candidate) {
                ElectionResult::create([
                    'election_id'           => $election->id,
                    'election_position_id'  => $position->id,
                    'election_candidate_id' => $candidate->id,
                    'member_id'             => $candidate->member_id,
                    'vote_count'            => $candidate->votes_count,
                    'is_elected'            => $idx < $position->seats,
                ]);
            }
        }

        $election->update(['status' => 'completed']);
        ActivityLog::log('update', "নির্বাচন ফলাফল প্রকাশ: {$election->title}", $election);
        return back()->with('success', 'ভোট গণনা সম্পন্ন। ফলাফল প্রকাশিত হয়েছে।');
    }

    public function results(Election $election)
    {
        $election->load(['results' => fn($q) => $q->with(['member','position'])
                        ->orderBy('election_position_id')->orderByDesc('vote_count')]);
        $positionResults = $election->results->groupBy('election_position_id');
        return view('elections.results', compact('election','positionResults'));
    }

    public function destroy(Election $election)
    {
        $this->authorizeAdmin();
        ActivityLog::log('delete', "নির্বাচন মুছে ফেলা: {$election->title}", $election);
        $election->delete();
        return redirect()->route('elections.index')->with('success', 'নির্বাচন মুছে ফেলা হয়েছে।');
    }

    private function authorizeAdmin(): void
    {
        if (!auth()->user()->can('manage committees')) abort(403);
    }
}
