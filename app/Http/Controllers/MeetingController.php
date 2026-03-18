<?php

namespace App\Http\Controllers;

use App\Models\Meeting;
use App\Models\InvestmentRequest;
use App\Models\ActivityLog;
use Illuminate\Http\Request;

class MeetingController extends Controller
{
    public function index()
    {
        $meetings = Meeting::withCount('investmentRequests')->latest('meeting_date')->paginate(15);
        return view('meetings.index', compact('meetings'));
    }

    public function create()
    {
        $pendingRequests = InvestmentRequest::with('member')
            ->whereIn('status', ['pending', 'modification_required'])
            ->latest()->get();
        return view('meetings.create', compact('pendingRequests'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'title'        => 'required|string|max:255',
            'meeting_date' => 'required|date',
            'meeting_time' => 'nullable',
            'venue'        => 'nullable|string|max:255',
            'description'  => 'nullable|string',
            'agenda_items' => 'nullable|array',  // investment request IDs to add to agenda
        ]);

        $data['created_by'] = auth()->id();
        $meeting = Meeting::create($data);

        // Add selected investment requests to this meeting agenda
        if (!empty($request->agenda_items)) {
            InvestmentRequest::whereIn('id', $request->agenda_items)
                ->whereIn('status', ['pending','modification_required'])
                ->update(['meeting_id' => $meeting->id, 'status' => 'agenda_added']);
            ActivityLog::log('update', "সভার এজেন্ডায় ".count($request->agenda_items)." টি বিনিয়োগ আবেদন যোগ হয়েছে", $meeting);
        }

        ActivityLog::log('create', "সভা তৈরি: {$meeting->title}", $meeting);
        return redirect()->route('meetings.show', $meeting)->with('success', 'সভা তৈরি হয়েছে।');
    }

    public function show(Meeting $meeting)
    {
        $meeting->load(['investmentRequests.member','creator']);
        $pendingRequests = InvestmentRequest::with('member')
            ->whereIn('status', ['pending','modification_required'])
            ->whereNull('meeting_id')
            ->latest()->get();
        return view('meetings.show', compact('meeting', 'pendingRequests'));
    }

    public function edit(Meeting $meeting)
    {
        return view('meetings.edit', compact('meeting'));
    }

    public function update(Request $request, Meeting $meeting)
    {
        $data = $request->validate([
            'title'        => 'required|string|max:255',
            'meeting_date' => 'required|date',
            'meeting_time' => 'nullable',
            'venue'        => 'nullable|string|max:255',
            'description'  => 'nullable|string',
            'status'       => 'required|in:scheduled,ongoing,completed,cancelled',
            'minutes'      => 'nullable|string',
        ]);
        $meeting->update($data);
        ActivityLog::log('update', "সভা আপডেট: {$meeting->title}", $meeting);
        return redirect()->route('meetings.show', $meeting)->with('success', 'সভা আপডেট হয়েছে।');
    }

    public function addAgendaItem(Request $request, Meeting $meeting)
    {
        $request->validate(['investment_request_id' => 'required|exists:investment_requests,id']);
        $inv = InvestmentRequest::findOrFail($request->investment_request_id);
        if (!in_array($inv->status, ['pending','modification_required'])) {
            return back()->with('error', 'এই আবেদন এজেন্ডায় যোগ করা যাবে না।');
        }
        $inv->update(['meeting_id' => $meeting->id, 'status' => 'agenda_added']);
        return back()->with('success', 'এজেন্ডায় যোগ হয়েছে।');
    }

    public function removeAgendaItem(Meeting $meeting, InvestmentRequest $investment)
    {
        $investment->update(['meeting_id' => null, 'status' => 'pending']);
        return back()->with('success', 'এজেন্ডা থেকে সরানো হয়েছে।');
    }
}
