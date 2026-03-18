<?php

namespace App\Http\Controllers;

use App\Models\InvestmentRequest;
use App\Models\InvestmentMeeting;
use App\Models\InvestmentMeetingItem;
use App\Models\InvestmentPayment;
use App\Models\InvestmentSettlement;
use App\Models\Member;
use App\Models\ActivityLog;
use Illuminate\Http\Request;
use Carbon\Carbon;
use Barryvdh\DomPDF\Facade\Pdf;

class InvestmentController extends Controller
{
    // ══════════════════════════════════════════════════════════════════
    //  INVESTMENT REQUESTS
    // ══════════════════════════════════════════════════════════════════

    public function index(Request $request)
    {
        $query = InvestmentRequest::with('member');

        // Member role: scope to own investments only
        if (auth()->user()->isMemberRole()) {
            $myMember = auth()->user()->getLinkedMember();
            if (!$myMember) {
                return redirect()->route('dashboard')->with('error', 'প্রোফাইল লিঙ্ক করা নেই।');
            }
            $query->where('member_id', $myMember->id);
        }

        if ($request->filled('status'))  $query->where('status', $request->status);
        if ($request->filled('search')) {
            $s = $request->search;
            $query->where(function($q) use ($s) {
                $q->where('project_name','like',"%$s%")
                  ->orWhereHas('member', fn($q2) =>
                      $q2->where('name','like',"%$s%")->orWhere('member_id','like',"%$s%")
                  );
            });
        }
        if ($request->filled('date_from')) $query->where('submitted_date','>=',$request->date_from);
        if ($request->filled('date_to'))   $query->where('submitted_date','<=',$request->date_to);

        $requests = $query->latest()->paginate(20)->withQueryString();

        $stats = [
            'pending'  => InvestmentRequest::where('status','pending')->count(),
            'approved' => InvestmentRequest::where('status','approved')->count(),
            'active'   => InvestmentRequest::where('status','active')->count(),
            'matured'  => InvestmentRequest::where('status','matured')->count(),
            'total_active_amount' => InvestmentRequest::where('status','active')->sum('approved_amount'),
        ];

        return view('investments.index', compact('requests','stats'));
    }

    public function create()
    {
        $members = Member::where('status','active')->orderBy('name')->get();
        return view('investments.create', compact('members'));
    }

    public function store(Request $request)
    {
        // Member role: use their own linked member
        if (auth()->user()->isMemberRole()) {
            $myMember = auth()->user()->getLinkedMember();
            if (!$myMember) {
                return back()->with('error', 'প্রোফাইল লিঙ্ক করা নেই। Admin এর সাথে যোগাযোগ করুন।');
            }
            $request->merge(['member_id' => $myMember->id]);
        }

        $data = $request->validate([
            'member_id'              => 'required|exists:members,id',
            'project_name'           => 'required|string|max:255',
            'project_description'    => 'nullable|string',
            'requested_amount'       => 'required|numeric|min:1',
            'duration_months'        => 'required|integer|min:1|max:120',
            'expected_profit_ratio'  => 'required|numeric|min:0|max:100',
            'expected_return_date'   => 'required|date',
            'submitted_date'         => 'required|date',
        ]);

        $data['created_by'] = auth()->id();
        $inv = InvestmentRequest::create($data);

        ActivityLog::log('create', "বিনিয়োগ আবেদন জমা: {$inv->project_name} — {$inv->member->name}", $inv);

        return redirect()->route('investments.show', $inv)
                         ->with('success', 'বিনিয়োগ আবেদন সফলভাবে জমা হয়েছে।');
    }

    public function show(InvestmentRequest $investment)
    {
        $investment->load(['member','payment.paidBy','settlement.settledBy','approvedBy','meetingItems.meeting']);
        return view('investments.show', compact('investment'));
    }

    public function edit(InvestmentRequest $investment)
    {
        if (!in_array($investment->status, ['pending','modification_needed'])) {
            return back()->with('error', 'এই আবেদন সম্পাদনা করা যাবে না।');
        }
        $members = Member::where('status','active')->orderBy('name')->get();
        return view('investments.edit', compact('investment','members'));
    }

    public function update(Request $request, InvestmentRequest $investment)
    {
        if (!in_array($investment->status, ['pending','modification_needed'])) {
            return back()->with('error', 'এই আবেদন আপডেট করা যাবে না।');
        }

        $data = $request->validate([
            'project_name'          => 'required|string|max:255',
            'project_description'   => 'nullable|string',
            'requested_amount'      => 'required|numeric|min:1',
            'duration_months'       => 'required|integer|min:1|max:120',
            'expected_profit_ratio' => 'required|numeric|min:0|max:100',
            'expected_return_date'  => 'required|date',
            'submitted_date'        => 'required|date',
        ]);

        // Re-submit sets back to pending
        $data['status'] = 'pending';
        $investment->update($data);

        ActivityLog::log('update', "বিনিয়োগ আবেদন আপডেট: {$investment->project_name}", $investment);
        return redirect()->route('investments.show', $investment)
                         ->with('success', 'আবেদন আপডেট হয়েছে।');
    }

    public function destroy(InvestmentRequest $investment)
    {
        if (!in_array($investment->status, ['pending','rejected'])) {
            return back()->with('error', 'শুধুমাত্র বিবেচনাধীন বা প্রত্যাখ্যাত আবেদন মুছে ফেলা যাবে।');
        }
        ActivityLog::log('delete', "বিনিয়োগ আবেদন মুছে ফেলা: {$investment->project_name}", $investment);
        $investment->delete();
        return redirect()->route('investments.index')->with('success', 'আবেদন মুছে ফেলা হয়েছে।');
    }

    // ══════════════════════════════════════════════════════════════════
    //  MEETING AGENDA
    // ══════════════════════════════════════════════════════════════════

    public function meetings()
    {
        $meetings = InvestmentMeeting::withCount('items')->latest('meeting_date')->paginate(15);
        $pendingRequests = InvestmentRequest::where('status','pending')->with('member')->get();
        return view('investments.meetings', compact('meetings','pendingRequests'));
    }

    public function storeMeeting(Request $request)
    {
        $data = $request->validate([
            'title'        => 'required|string|max:255',
            'meeting_date' => 'required|date',
            'venue'        => 'nullable|string|max:255',
            'notes'        => 'nullable|string',
            'request_ids'  => 'nullable|array',
            'request_ids.*'=> 'exists:investment_requests,id',
        ]);

        $data['created_by'] = auth()->id();
        $meeting = InvestmentMeeting::create($data);

        if (!empty($data['request_ids'])) {
            foreach ($data['request_ids'] as $i => $rid) {
                InvestmentMeetingItem::create([
                    'investment_meeting_id'   => $meeting->id,
                    'investment_request_id'   => $rid,
                    'agenda_order'            => $i + 1,
                ]);
                InvestmentRequest::find($rid)?->update(['status' => 'in_agenda']);
            }
        }

        ActivityLog::log('create', "সভা তৈরি: {$meeting->title}", $meeting);
        return redirect()->route('investments.meeting.show', $meeting)
                         ->with('success', 'সভার এজেন্ডা তৈরি হয়েছে।');
    }

    public function showMeeting(InvestmentMeeting $meeting)
    {
        $meeting->load(['items.investmentRequest.member']);
        return view('investments.meeting_show', compact('meeting'));
    }

    public function addToMeeting(Request $request, InvestmentMeeting $meeting)
    {
        $request->validate(['request_id' => 'required|exists:investment_requests,id']);

        $exists = InvestmentMeetingItem::where('investment_meeting_id', $meeting->id)
                                       ->where('investment_request_id', $request->request_id)->exists();
        if ($exists) return back()->with('error', 'এই আবেদন ইতিমধ্যে এজেন্ডায় আছে।');

        $order = $meeting->items()->count() + 1;
        InvestmentMeetingItem::create([
            'investment_meeting_id'  => $meeting->id,
            'investment_request_id'  => $request->request_id,
            'agenda_order'           => $order,
        ]);
        InvestmentRequest::find($request->request_id)?->update(['status' => 'in_agenda']);

        return back()->with('success', 'আবেদন এজেন্ডায় যোগ করা হয়েছে।');
    }

    public function updateMeetingStatus(Request $request, InvestmentMeeting $meeting)
    {
        $request->validate(['status' => 'required|in:scheduled,held,cancelled']);
        $meeting->update(['status' => $request->status]);
        return back()->with('success', 'সভার অবস্থা আপডেট হয়েছে।');
    }

    // ══════════════════════════════════════════════════════════════════
    //  APPROVAL / REJECTION
    // ══════════════════════════════════════════════════════════════════

    public function approve(Request $request, InvestmentRequest $investment)
    {
        $data = $request->validate([
            'approved_amount'           => 'required|numeric|min:1',
            'approved_duration_months'  => 'required|integer|min:1|max:120',
            'approved_profit_ratio'     => 'required|numeric|min:0|max:100',
            'approved_start_date'       => 'required|date',
            'approved_return_date'      => 'required|date|after:approved_start_date',
            'approval_note'             => 'nullable|string|max:500',
        ]);

        $investment->update(array_merge($data, [
            'status'      => 'approved',
            'approved_by' => auth()->id(),
            'approved_at' => now(),
        ]));

        // Update meeting item decision if exists
        InvestmentMeetingItem::where('investment_request_id', $investment->id)
                             ->latest()->first()
                             ?->update(['decision' => 'approved', 'decision_note' => $data['approval_note']]);

        ActivityLog::log('update', "বিনিয়োগ অনুমোদিত: {$investment->project_name} — ৳".number_format($data['approved_amount'],2), $investment);
        return back()->with('success', 'বিনিয়োগ প্রস্তাব অনুমোদিত হয়েছে।');
    }

    public function reject(Request $request, InvestmentRequest $investment)
    {
        $request->validate(['rejection_note' => 'required|string|max:500']);

        $investment->update([
            'status'         => 'rejected',
            'rejection_note' => $request->rejection_note,
            'approved_by'    => auth()->id(),
            'approved_at'    => now(),
        ]);

        InvestmentMeetingItem::where('investment_request_id', $investment->id)
                             ->latest()->first()
                             ?->update(['decision' => 'rejected', 'decision_note' => $request->rejection_note]);

        ActivityLog::log('update', "বিনিয়োগ প্রত্যাখ্যাত: {$investment->project_name}", $investment);
        return back()->with('success', 'বিনিয়োগ প্রস্তাব প্রত্যাখ্যাত হয়েছে।');
    }

    public function requestModification(Request $request, InvestmentRequest $investment)
    {
        $request->validate(['modification_note' => 'required|string|max:500']);

        $investment->update([
            'status'            => 'modification_needed',
            'modification_note' => $request->modification_note,
        ]);

        InvestmentMeetingItem::where('investment_request_id', $investment->id)
                             ->latest()->first()
                             ?->update(['decision' => 'modification_needed', 'decision_note' => $request->modification_note]);

        ActivityLog::log('update', "বিনিয়োগ সংশোধন প্রয়োজন: {$investment->project_name}", $investment);
        return back()->with('success', 'সংশোধনের জন্য ফেরত পাঠানো হয়েছে।');
    }

    // ══════════════════════════════════════════════════════════════════
    //  PAYMENT DISBURSEMENT
    // ══════════════════════════════════════════════════════════════════

    public function paymentForm(InvestmentRequest $investment)
    {
        if ($investment->status !== 'approved') {
            return back()->with('error', 'শুধুমাত্র অনুমোদিত বিনিয়োগের পেমেন্ট করা যাবে।');
        }
        if ($investment->payment) {
            return back()->with('error', 'এই বিনিয়োগের পেমেন্ট ইতিমধ্যে করা হয়েছে।');
        }
        return view('investments.payment', compact('investment'));
    }

    public function processPayment(Request $request, InvestmentRequest $investment)
    {
        if ($investment->status !== 'approved') {
            return back()->with('error', 'অনুমোদন ছাড়া পেমেন্ট সম্ভব নয়।');
        }
        if ($investment->payment) {
            return back()->with('error', 'পেমেন্ট ইতিমধ্যে সম্পন্ন হয়েছে।');
        }

        $data = $request->validate([
            'amount'         => 'required|numeric|min:1',
            'payment_method' => 'required|in:cash,bank,bkash,nagad,cheque',
            'reference'      => 'nullable|string|max:100',
            'bank_name'      => 'nullable|string|max:100',
            'account_number' => 'nullable|string|max:50',
            'payment_date'   => 'required|date',
            'note'           => 'nullable|string|max:500',
        ]);

        $data['investment_request_id'] = $investment->id;
        $data['member_id']             = $investment->member_id;
        $data['voucher_number']        = InvestmentPayment::generateVoucher();
        $data['paid_by']               = auth()->id();

        $payment = InvestmentPayment::create($data);

        // Activate investment
        $investment->update([
            'status'               => 'active',
            'approved_start_date'  => $data['payment_date'],
            'approved_return_date' => Carbon::parse($data['payment_date'])
                                            ->addMonths($investment->approved_duration_months),
        ]);

        ActivityLog::log('create', "বিনিয়োগ পেমেন্ট ({$payment->voucher_number}): {$investment->project_name} — ৳".number_format($data['amount'],2), $payment);

        return redirect()->route('investments.voucher.view', $payment)
                         ->with('success', 'পেমেন্ট সফল। ভাউচার তৈরি হয়েছে।');
    }

    public function voucher(InvestmentPayment $payment)
    {
        $payment->load(['investmentRequest.member','paidBy']);
        return view('investments.voucher', compact('payment'));
    }

    public function voucherPdf(InvestmentPayment $payment)
    {
        $payment->load(['investmentRequest.member','paidBy']);
        $pdf = Pdf::loadView('investments.voucher_pdf', compact('payment'))->setPaper('a5');
        return $pdf->download("voucher-{$payment->voucher_number}.pdf");
    }

    // ══════════════════════════════════════════════════════════════════
    //  MATURITY CHECK & SETTLEMENT
    // ══════════════════════════════════════════════════════════════════

    public function checkMaturities()
    {
        $matured = 0;
        InvestmentRequest::where('status','active')
            ->whereNotNull('approved_return_date')
            ->get()
            ->each(function($inv) use (&$matured) {
                if ($inv->is_matured) {
                    $inv->update(['status' => 'matured']);
                    $matured++;
                }
            });

        ActivityLog::log('update', "মেয়াদ পরীক্ষা: {$matured}টি বিনিয়োগ মেয়াদোত্তীর্ণ হয়েছে");
        return back()->with('success', "{$matured}টি বিনিয়োগ মেয়াদোত্তীর্ণ হিসেবে চিহ্নিত হয়েছে।");
    }

    public function settlementForm(InvestmentRequest $investment)
    {
        if (!in_array($investment->status, ['matured','active'])) {
            return back()->with('error', 'শুধুমাত্র সক্রিয় বা মেয়াদোত্তীর্ণ বিনিয়োগের নিষ্পত্তি করা যাবে।');
        }
        if ($investment->settlement) {
            return back()->with('error', 'এই বিনিয়োগের নিষ্পত্তি ইতিমধ্যে হয়েছে।');
        }
        return view('investments.settlement', compact('investment'));
    }

    public function processSettlement(Request $request, InvestmentRequest $investment)
    {
        if ($investment->settlement) {
            return back()->with('error', 'নিষ্পত্তি ইতিমধ্যে সম্পন্ন হয়েছে।');
        }

        $data = $request->validate([
            'actual_profit_loss' => 'required|numeric',   // can be negative (loss)
            'payment_method'     => 'required|in:cash,bank,bkash,nagad,cheque',
            'reference'          => 'nullable|string|max:100',
            'bank_name'          => 'nullable|string|max:100',
            'settlement_date'    => 'required|date',
            'note'               => 'nullable|string|max:500',
        ]);

        $investmentAmount = (float)$investment->approved_amount;
        $profitLoss       = (float)$data['actual_profit_loss'];
        $returnAmount     = $investmentAmount + $profitLoss;
        $outcome          = $profitLoss > 0 ? 'profit' : ($profitLoss < 0 ? 'loss' : 'breakeven');

        $settlement = InvestmentSettlement::create([
            'investment_request_id' => $investment->id,
            'member_id'             => $investment->member_id,
            'voucher_number'        => InvestmentSettlement::generateVoucher(),
            'investment_amount'     => $investmentAmount,
            'actual_profit_loss'    => $profitLoss,
            'outcome'               => $outcome,
            'return_amount'         => $returnAmount,
            'payment_method'        => $data['payment_method'],
            'reference'             => $data['reference'] ?? null,
            'bank_name'             => $data['bank_name'] ?? null,
            'settlement_date'       => $data['settlement_date'],
            'note'                  => $data['note'] ?? null,
            'settled_by'            => auth()->id(),
        ]);

        $investment->update(['status' => 'closed']);

        ActivityLog::log('create', "বিনিয়োগ নিষ্পত্তি ({$settlement->voucher_number}): ৳".number_format($returnAmount,2)." ({$outcome})", $settlement);

        return redirect()->route('investments.settlement.voucher', $settlement)
                         ->with('success', 'নিষ্পত্তি সম্পন্ন। ভাউচার তৈরি হয়েছে।');
    }

    public function settlementVoucher(InvestmentSettlement $settlement)
    {
        $settlement->load(['investmentRequest.member','settledBy']);
        return view('investments.settlement_voucher', compact('settlement'));
    }

    public function settlementVoucherPdf(InvestmentSettlement $settlement)
    {
        $settlement->load(['investmentRequest.member','settledBy']);
        $pdf = Pdf::loadView('investments.settlement_voucher_pdf', compact('settlement'))->setPaper('a5');
        return $pdf->download("settlement-{$settlement->voucher_number}.pdf");
    }

    // ══════════════════════════════════════════════════════════════════
    //  REPORTS
    // ══════════════════════════════════════════════════════════════════

    public function reports()
    {
        return view('investments.reports.index');
    }

    public function reportRequests(Request $request)
    {
        $query = InvestmentRequest::with('member');
        if ($request->filled('status'))    $query->where('status',$request->status);
        if ($request->filled('date_from')) $query->where('submitted_date','>=',$request->date_from);
        if ($request->filled('date_to'))   $query->where('submitted_date','<=',$request->date_to);

        $data     = $query->latest()->get();
        $export   = $request->get('export');
        if ($export === 'pdf') {
            $pdf = Pdf::loadView('investments.reports.requests_pdf', compact('data'))->setPaper('a4','landscape');
            return $pdf->download("investment-requests.pdf");
        }
        return view('investments.reports.requests', compact('data'));
    }

    public function reportActive(Request $request)
    {
        $investments = InvestmentRequest::with(['member','payment'])
                                        ->where('status','active')
                                        ->get()
                                        ->map(function($inv) {
                                            $inv->days_remaining = $inv->days_remaining;
                                            return $inv;
                                        });
        $totalActive = $investments->sum('approved_amount');

        if ($request->get('export') === 'pdf') {
            $pdf = Pdf::loadView('investments.reports.active_pdf', compact('investments','totalActive'))->setPaper('a4','landscape');
            return $pdf->download("active-investments.pdf");
        }
        return view('investments.reports.active', compact('investments','totalActive'));
    }

    public function reportMaturity(Request $request)
    {
        $investments = InvestmentRequest::with(['member','payment'])
                                        ->whereIn('status',['active','matured'])
                                        ->orderBy('approved_return_date')
                                        ->get();

        if ($request->get('export') === 'pdf') {
            $pdf = Pdf::loadView('investments.reports.maturity_pdf', compact('investments'))->setPaper('a4','landscape');
            return $pdf->download("maturity-report.pdf");
        }
        return view('investments.reports.maturity', compact('investments'));
    }

    public function reportMemberLedger(Request $request)
    {
        $user   = auth()->user();
        $member = null;
        $ledger = collect();

        if ($user->isMemberRole()) {
            // Member role: always locked to own data
            $member = $user->getLinkedMember();
            if ($member) {
                $ledger = InvestmentRequest::with(['payment','settlement'])
                                           ->where('member_id', $member->id)
                                           ->latest()->get();
            }
            $members = collect($member ? [$member] : []);
        } else {
            $members = Member::orderBy('name')->get();
            if ($request->filled('member_id')) {
                $member = Member::findOrFail($request->member_id);
                $ledger = InvestmentRequest::with(['payment','settlement'])
                                           ->where('member_id', $member->id)
                                           ->latest()->get();
            }
        }

        if ($request->get('export') === 'pdf' && $member) {
            $pdf = Pdf::loadView('investments.reports.ledger_pdf', compact('member','ledger'))->setPaper('a4');
            return $pdf->download("ledger-{$member->member_id}.pdf");
        }
        return view('investments.reports.ledger', compact('members','member','ledger'));
    }

    public function reportProfitLoss(Request $request)
    {
        $query = InvestmentSettlement::with(['investmentRequest.member']);
        if ($request->filled('date_from')) $query->where('settlement_date','>=',$request->date_from);
        if ($request->filled('date_to'))   $query->where('settlement_date','<=',$request->date_to);
        if ($request->filled('outcome'))   $query->where('outcome',$request->outcome);

        $settlements    = $query->latest('settlement_date')->get();
        $totalProfit    = $settlements->where('outcome','profit')->sum('actual_profit_loss');
        $totalLoss      = $settlements->where('outcome','loss')->sum('actual_profit_loss');
        $netProfitLoss  = $settlements->sum('actual_profit_loss');

        if ($request->get('export') === 'pdf') {
            $pdf = Pdf::loadView('investments.reports.profitloss_pdf', compact('settlements','totalProfit','totalLoss','netProfitLoss'))->setPaper('a4','landscape');
            return $pdf->download("profit-loss-report.pdf");
        }
        return view('investments.reports.profitloss', compact('settlements','totalProfit','totalLoss','netProfitLoss'));
    }

    public function reportSettlements(Request $request)
    {
        $query = InvestmentSettlement::with(['investmentRequest.member','settledBy']);
        if ($request->filled('date_from')) $query->where('settlement_date','>=',$request->date_from);
        if ($request->filled('date_to'))   $query->where('settlement_date','<=',$request->date_to);

        $settlements  = $query->latest('settlement_date')->get();
        $totalReturned = $settlements->sum('return_amount');

        if ($request->get('export') === 'pdf') {
            $pdf = Pdf::loadView('investments.reports.settlements_pdf', compact('settlements','totalReturned'))->setPaper('a4','landscape');
            return $pdf->download("settlements-report.pdf");
        }
        return view('investments.reports.settlements', compact('settlements','totalReturned'));
    }

    public function reportPayments(Request $request)
    {
        $query = InvestmentPayment::with(['investmentRequest.member','paidBy']);
        if ($request->filled('date_from')) $query->where('payment_date','>=',$request->date_from);
        if ($request->filled('date_to'))   $query->where('payment_date','<=',$request->date_to);

        $payments  = $query->latest('payment_date')->get();
        $totalPaid = $payments->sum('amount');

        if ($request->get('export') === 'pdf') {
            $pdf = Pdf::loadView('investments.reports.payments_pdf', compact('payments','totalPaid'))->setPaper('a4','landscape');
            return $pdf->download("investment-payments-report.pdf");
        }
        return view('investments.reports.payments', compact('payments','totalPaid'));
    }
}
