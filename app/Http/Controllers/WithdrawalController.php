<?php

namespace App\Http\Controllers;

use App\Models\Member;
use App\Models\WithdrawalRequest;
use App\Models\WithdrawalRepayment;
use App\Models\ActivityLog;
use Illuminate\Http\Request;
use Carbon\Carbon;

class WithdrawalController extends Controller
{
    /* ─── List all withdrawal requests ─── */
    public function index(Request $request)
    {
        $query = WithdrawalRequest::with('member')->latest();

        if ($request->filled('status')) $query->where('status', $request->status);
        if ($request->filled('search')) {
            $s = $request->search;
            $query->whereHas('member', fn($q) =>
                $q->where('name','like',"%$s%")->orWhere('member_id','like',"%$s%")
            );
        }

        $requests = $query->paginate(20)->withQueryString();

        $stats = [
            'pending'          => WithdrawalRequest::where('status','pending')->count(),
            'on_hold'          => WithdrawalRequest::where('status','on_hold')->count(),
            'partially_repaid' => WithdrawalRequest::where('status','partially_repaid')->count(),
            'total_pending_amount' => WithdrawalRequest::whereIn('status',['on_hold','partially_repaid'])
                                        ->selectRaw('SUM(total_amount - repaid_amount) as t')->value('t') ?? 0,
        ];

        return view('withdrawals.index', compact('requests','stats'));
    }

    /* ─── Form: new withdrawal request ─── */
    public function create()
    {
        $members = Member::whereIn('status',['active','suspended'])
                          ->orderBy('name')->get();
        return view('withdrawals.create', compact('members'));
    }

    /* ─── Store new request ─── */
    public function store(Request $request)
    {
        $data = $request->validate([
            'member_id'            => 'required|exists:members,id',
            'share_amount'         => 'required|numeric|min:0',
            'savings_amount'       => 'required|numeric|min:0',
            'profit_amount'        => 'nullable|numeric|min:0',
            'reason'               => 'required|string|max:1000',
            'requested_date'       => 'required|date',
            'scheduled_repay_date' => 'nullable|date|after_or_equal:requested_date',
        ]);

        // Check for existing active withdrawal
        $existing = WithdrawalRequest::where('member_id', $data['member_id'])
                                     ->whereIn('status',['pending','on_hold','partially_repaid'])
                                     ->exists();
        if ($existing) {
            return back()->withErrors(['member_id'=>'এই সদস্যের একটি সক্রিয় উত্তোলন আবেদন ইতিমধ্যে রয়েছে।'])->withInput();
        }

        $data['profit_amount'] = $data['profit_amount'] ?? 0;
        $data['total_amount']  = $data['share_amount'] + $data['savings_amount'] + $data['profit_amount'];

        $withdrawal = WithdrawalRequest::create($data);

        ActivityLog::log('create', "উত্তোলন আবেদন জমা: {$withdrawal->member->name} — ৳".number_format($data['total_amount'],2), $withdrawal);

        return redirect()->route('withdrawals.show', $withdrawal)
                         ->with('success', 'উত্তোলন আবেদন সফলভাবে জমা হয়েছে।');
    }

    /* ─── Show one request ─── */
    public function show(WithdrawalRequest $withdrawal)
    {
        $withdrawal->load(['member','repayments.paidBy','approvedBy']);
        return view('withdrawals.show', compact('withdrawal'));
    }

    /* ─── Approve: put member on_hold ─── */
    public function approve(Request $request, WithdrawalRequest $withdrawal)
    {
        $request->validate([
            'scheduled_repay_date' => 'required|date',
            'admin_note'           => 'nullable|string|max:500',
        ]);

        if ($withdrawal->status !== 'pending') {
            return back()->with('error', 'শুধুমাত্র বিবেচনাধীন আবেদন অনুমোদন করা যাবে।');
        }

        $withdrawal->update([
            'status'               => 'on_hold',
            'scheduled_repay_date' => $request->scheduled_repay_date,
            'admin_note'           => $request->admin_note,
            'approved_by'          => auth()->id(),
            'approved_at'          => now(),
        ]);

        // Put member on hold
        $withdrawal->member->update(['status' => 'on_hold']);

        ActivityLog::log('update', "উত্তোলন অনুমোদন: {$withdrawal->member->name} — সদস্যপদ স্থগিত", $withdrawal);

        return back()->with('success', 'আবেদন অনুমোদিত হয়েছে। সদস্যের সদস্যপদ স্থগিত করা হয়েছে।');
    }

    /* ─── Reject request ─── */
    public function reject(Request $request, WithdrawalRequest $withdrawal)
    {
        $request->validate(['admin_note' => 'required|string|max:500']);

        if ($withdrawal->status !== 'pending') {
            return back()->with('error', 'শুধুমাত্র বিবেচনাধীন আবেদন প্রত্যাখ্যান করা যাবে।');
        }

        $withdrawal->update([
            'status'      => 'rejected',
            'admin_note'  => $request->admin_note,
            'approved_by' => auth()->id(),
            'approved_at' => now(),
        ]);

        ActivityLog::log('update', "উত্তোলন প্রত্যাখ্যান: {$withdrawal->member->name}", $withdrawal);

        return back()->with('success', 'আবেদন প্রত্যাখ্যান করা হয়েছে।');
    }

    /* ─── Record a repayment instalment ─── */
    public function recordRepayment(Request $request, WithdrawalRequest $withdrawal)
    {
        $data = $request->validate([
            'amount'     => 'required|numeric|min:0.01',
            'repay_date' => 'required|date',
            'method'     => 'required|in:cash,bank,bkash,nagad,other',
            'reference'  => 'nullable|string|max:100',
            'note'       => 'nullable|string|max:500',
        ]);

        $remaining = $withdrawal->remaining_amount;
        if ($data['amount'] > $remaining) {
            return back()->withErrors(['amount' => "পরিমাণ বকেয়া ৳".number_format($remaining,2)." এর বেশি হতে পারবে না।"])->withInput();
        }

        $data['withdrawal_request_id'] = $withdrawal->id;
        $data['member_id']             = $withdrawal->member_id;
        $data['paid_by']               = auth()->id();

        WithdrawalRepayment::create($data);

        // Update repaid amount
        $newRepaid = $withdrawal->repaid_amount + $data['amount'];
        $withdrawal->increment('repaid_amount', $data['amount']);

        // Check if fully repaid
        $withdrawal->refresh();
        if ($withdrawal->remaining_amount <= 0) {
            $withdrawal->update(['status' => 'repaid']);
            // Membership becomes disconnected (inactive)
            $withdrawal->member->update(['status' => 'disconnected']);
            $msg = 'সম্পূর্ণ পরিশোধ হয়েছে। সদস্যের সংযোগ বিচ্ছিন্ন করা হয়েছে।';
            ActivityLog::log('update', "উত্তোলন সম্পূর্ণ পরিশোধ: {$withdrawal->member->name} — সদস্যপদ বিচ্ছিন্ন", $withdrawal);
        } else {
            $withdrawal->update(['status' => 'partially_repaid']);
            $msg = '৳'.number_format($data['amount'],2).' পরিশোধ রেকর্ড করা হয়েছে।';
            ActivityLog::log('update', "আংশিক পরিশোধ: {$withdrawal->member->name} — ৳".number_format($data['amount'],2), $withdrawal);
        }

        return back()->with('success', $msg);
    }

    /* ─── Admin override: mark fully repaid manually ─── */
    public function markFullyRepaid(WithdrawalRequest $withdrawal)
    {
        $withdrawal->update([
            'status'        => 'repaid',
            'repaid_amount' => $withdrawal->total_amount,
        ]);
        $withdrawal->member->update(['status' => 'disconnected']);

        ActivityLog::log('update', "ম্যানুয়াল: উত্তোলন সম্পূর্ণ পরিশোধ — {$withdrawal->member->name}", $withdrawal);
        return back()->with('success', 'সম্পূর্ণ পরিশোধ চিহ্নিত। সদস্যের সংযোগ বিচ্ছিন্ন হয়েছে।');
    }
}
