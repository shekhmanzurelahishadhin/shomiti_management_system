<?php

namespace App\Http\Controllers;

use App\Models\Payment;
use App\Models\Bill;
use App\Models\Member;
use App\Models\ActivityLog;
use Illuminate\Http\Request;
use Carbon\Carbon;
use Barryvdh\DomPDF\Facade\Pdf;

class PaymentController extends Controller
{
    public function index(Request $request)
    {
        $query = Payment::with(['member', 'bill', 'collector']);

        if ($request->filled('date_from')) $query->where('payment_date', '>=', $request->date_from);
        if ($request->filled('date_to'))   $query->where('payment_date', '<=', $request->date_to);
        if ($request->filled('method'))    $query->where('payment_method', $request->method);
        if ($request->filled('search')) {
            $s = $request->search;
            $query->whereHas('member', fn($q) => $q->where('name', 'like', "%$s%")
                                                    ->orWhere('member_id', 'like', "%$s%"));
        }

        $payments     = $query->latest()->paginate(20)->withQueryString();
        $todayTotal   = Payment::whereDate('payment_date', today())->sum('amount');
        $monthTotal   = Payment::whereMonth('payment_date', now()->month)
                                ->whereYear('payment_date', now()->year)->sum('amount');

        return view('payments.index', compact('payments', 'todayTotal', 'monthTotal'));
    }

    public function create(Request $request)
    {
        $members = Member::where('status', 'active')->orderBy('name')->get();
        $selectedMember = null;
        $pendingBills   = collect();

        if ($request->filled('member_id')) {
            $selectedMember = Member::find($request->member_id);
            if ($selectedMember) {
                $pendingBills = Bill::where('member_id', $selectedMember->id)
                                    ->whereIn('status', ['pending', 'partial', 'overdue'])
                                    ->orderBy('bill_year')->orderBy('bill_month')
                                    ->get();
            }
        }

        return view('payments.create', compact('members', 'selectedMember', 'pendingBills'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'member_id'      => 'required|exists:members,id',
            'bill_id'        => 'required|exists:bills,id',
            'amount'         => 'required|numeric|min:0.01',
            'payment_method' => 'required|in:cash,bank,bkash,nagad,other',
            'reference'      => 'nullable|string|max:100',
            'notes'          => 'nullable|string',
            'payment_date'   => 'required|date',
        ]);

        $bill = Bill::findOrFail($data['bill_id']);
        $maxPayable = $bill->amount + $bill->fine - $bill->discount - $bill->paid_amount;

        if ($data['amount'] > $maxPayable) {
            return back()->withErrors(['amount' => "Amount cannot exceed outstanding due of ৳{$maxPayable}"])->withInput();
        }

        $data['collected_by'] = auth()->id();
        $payment = Payment::create($data);

        // Update bill paid amount and status
        $bill->increment('paid_amount', $data['amount']);
        $bill->refresh()->updateStatus();

        ActivityLog::log('create', "Recorded payment ৳{$data['amount']} for member ID {$data['member_id']}", $payment);

        return redirect()->route('payments.receipt', $payment)
                         ->with('success', 'Payment recorded successfully.');
    }

    public function show(Payment $payment)
    {
        $payment->load(['member', 'bill', 'collector']);
        return view('payments.show', compact('payment'));
    }

    public function receipt(Payment $payment)
    {
        $payment->load(['member', 'bill', 'collector']);
        return view('payments.receipt', compact('payment'));
    }

    public function receiptPdf(Payment $payment)
    {
        $payment->load(['member', 'bill', 'collector']);
        $pdf = Pdf::loadView('payments.receipt_pdf', compact('payment'));
        return $pdf->download("receipt-{$payment->id}.pdf");
    }

    public function destroy(Payment $payment)
    {
        $bill = $payment->bill;
        $bill->decrement('paid_amount', $payment->amount);
        $bill->refresh()->updateStatus();

        ActivityLog::log('delete', "Deleted payment #{$payment->id}", $payment);
        $payment->delete();

        return redirect()->route('payments.index')->with('success', 'Payment deleted.');
    }
}
