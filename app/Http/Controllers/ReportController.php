<?php

namespace App\Http\Controllers;

use App\Models\Member;
use App\Models\Bill;
use App\Models\Payment;
use App\Models\Expense;
use Illuminate\Http\Request;
use Carbon\Carbon;
use Barryvdh\DomPDF\Facade\Pdf;

class ReportController extends Controller
{
    /** Resolve the member scope.
     *  - Admin/Treasurer/SuperAdmin → null (can see all)
     *  - Member role               → their linked member (locked to own data)
     */
    private function getMemberScope(): ?Member
    {
        $user = auth()->user();
        if ($user->isMemberRole()) {
            return $user->getLinkedMember();
        }
        return null;
    }

    public function index()
    {
        $isMember = auth()->user()->isMemberRole();
        return view('reports.index', compact('isMember'));
    }

    // ── Monthly Collection ─────────────────────────────────────────────────
    public function monthlyCollection(Request $request)
    {
        $scope = $this->getMemberScope();

        // Member role: redirect to their own deposit report instead
        if ($scope) {
            return redirect()->route('reports.member-deposit', [
                'member_id' => $scope->id,
                'year'      => $request->get('year', now()->year),
            ])->with('info', 'আপনি শুধুমাত্র আপনার নিজের তথ্য দেখতে পারবেন।');
        }

        $month = $request->get('month', now()->month);
        $year  = $request->get('year',  now()->year);

        $payments = Payment::with(['member','bill'])
                           ->whereMonth('payment_date', $month)
                           ->whereYear('payment_date',  $year)
                           ->latest()->get();
        $total = $payments->sum('amount');

        if ($request->get('export') === 'pdf') {
            $pdf = Pdf::loadView('reports.monthly_collection_pdf', compact('payments','total','month','year'))
                      ->setPaper('a4');
            return $pdf->download("monthly-collection-{$year}-{$month}.pdf");
        }

        return view('reports.monthly_collection', compact('payments','total','month','year'));
    }

    // ── Defaulters ────────────────────────────────────────────────────────
    public function defaulters(Request $request)
    {
        $scope = $this->getMemberScope();

        // Member role: show only their own overdue bills
        if ($scope) {
            $month = $request->get('month', now()->month);
            $year  = $request->get('year',  now()->year);

            $myBills = Bill::with('member')
                           ->where('member_id', $scope->id)
                           ->whereIn('status', ['pending','overdue'])
                           ->get();

            return view('reports.member_own_bills', compact('myBills','scope'));
        }

        $month = $request->get('month', now()->month);
        $year  = $request->get('year',  now()->year);

        $defaulters = Bill::with('member')
                          ->whereIn('status', ['pending','overdue'])
                          ->where('bill_month', $month)
                          ->where('bill_year',  $year)
                          ->get();

        if ($request->get('export') === 'pdf') {
            $pdf = Pdf::loadView('reports.defaulters_pdf', compact('defaulters','month','year'))
                      ->setPaper('a4');
            return $pdf->download("defaulters-{$year}-{$month}.pdf");
        }

        return view('reports.defaulters', compact('defaulters','month','year'));
    }

    // ── Member Deposit ────────────────────────────────────────────────────
    public function memberDeposit(Request $request)
    {
        $scope  = $this->getMemberScope();
        $year   = $request->get('year', now()->year);
        $member = null;
        $bills  = collect();

        if ($scope) {
            // Member role: always locked to own data, cannot change member_id
            $member = $scope;
            $bills  = Bill::where('member_id', $member->id)
                          ->where('bill_year', $year)
                          ->orderBy('bill_month')->get();

            if ($request->get('export') === 'pdf') {
                $pdf = Pdf::loadView('reports.member_deposit_pdf', compact('member','bills','year'))
                          ->setPaper('a4');
                return $pdf->download("my-deposit-{$year}.pdf");
            }

            // Pass empty $members since member can't switch
            $members = collect([$member]);
            return view('reports.member_deposit', compact('members','member','bills','year'));
        }

        // Admin: can choose any member
        if ($request->filled('member_id')) {
            $member = Member::findOrFail($request->member_id);
            $bills  = Bill::where('member_id', $member->id)
                          ->where('bill_year', $year)
                          ->orderBy('bill_month')->get();
        }

        $members = Member::orderBy('name')->get();

        if ($request->get('export') === 'pdf' && $member) {
            $pdf = Pdf::loadView('reports.member_deposit_pdf', compact('member','bills','year'))
                      ->setPaper('a4');
            return $pdf->download("member-deposit-{$member->member_id}-{$year}.pdf");
        }

        return view('reports.member_deposit', compact('members','member','bills','year'));
    }

    // ── Annual Summary ────────────────────────────────────────────────────
    public function annualSummary(Request $request)
    {
        $scope = $this->getMemberScope();

        // Member role: show their own annual payment summary
        if ($scope) {
            $year   = $request->get('year', now()->year);
            $member = $scope;

            $monthlyData = [];
            for ($m = 1; $m <= 12; $m++) {
                $billed  = Bill::where('member_id', $member->id)
                               ->where('bill_month', $m)->where('bill_year', $year)->sum('amount');
                $paid    = Payment::where('member_id', $member->id)
                                  ->whereMonth('payment_date', $m)->whereYear('payment_date', $year)->sum('amount');
                $monthlyData[] = [
                    'month'   => date('F', mktime(0,0,0,$m,1)),
                    'billed'  => $billed,
                    'paid'    => $paid,
                    'balance' => $billed - $paid,
                ];
            }
            $totalBilled = array_sum(array_column($monthlyData,'billed'));
            $totalPaid   = array_sum(array_column($monthlyData,'paid'));

            return view('reports.member_annual', compact('member','monthlyData','totalBilled','totalPaid','year'));
        }

        // Admin: full org summary
        $year = $request->get('year', now()->year);

        $monthlyData = [];
        for ($m = 1; $m <= 12; $m++) {
            $collected = Payment::whereMonth('payment_date',$m)->whereYear('payment_date',$year)->sum('amount');
            $billed    = Bill::where('bill_month',$m)->where('bill_year',$year)->sum('amount');
            $expenses  = Expense::whereMonth('expense_date',$m)->whereYear('expense_date',$year)->sum('amount');
            $monthlyData[] = [
                'month'     => date('F', mktime(0,0,0,$m,1)),
                'billed'    => $billed,
                'collected' => $collected,
                'expenses'  => $expenses,
                'balance'   => $collected - $expenses,
            ];
        }
        $totalCollected = array_sum(array_column($monthlyData,'collected'));
        $totalExpenses  = array_sum(array_column($monthlyData,'expenses'));

        if ($request->get('export') === 'pdf') {
            $pdf = Pdf::loadView('reports.annual_summary_pdf', compact('monthlyData','totalCollected','totalExpenses','year'))
                      ->setPaper('a4','landscape');
            return $pdf->download("annual-summary-{$year}.pdf");
        }

        return view('reports.annual_summary', compact('monthlyData','totalCollected','totalExpenses','year'));
    }

    // ── Expense Report ─────────────────────────────────────────────────────
    public function expenseReport(Request $request)
    {
        // Expenses are organisational - redirect member to their own payments
        $scope = $this->getMemberScope();
        if ($scope) {
            return redirect()->route('reports.member-deposit', ['member_id' => $scope->id])
                             ->with('info', 'খরচের রিপোর্ট শুধুমাত্র Admin দেখতে পারবেন।');
        }

        $query = Expense::query();
        if ($request->filled('date_from')) $query->where('expense_date','>=',$request->date_from);
        if ($request->filled('date_to'))   $query->where('expense_date','<=',$request->date_to);
        if ($request->filled('category'))  $query->where('category',$request->category);

        $expenses   = $query->latest('expense_date')->get();
        $total      = $expenses->sum('amount');
        $byCategory = $expenses->groupBy('category')->map->sum('amount');

        if ($request->get('export') === 'pdf') {
            $pdf = Pdf::loadView('reports.expense_report_pdf', compact('expenses','total','byCategory'))
                      ->setPaper('a4');
            return $pdf->download("expense-report.pdf");
        }

        return view('reports.expense_report', compact('expenses','total','byCategory'));
    }

    // ── Daily Transaction ──────────────────────────────────────────────────
    public function dailyTransaction(Request $request)
    {
        $scope = $this->getMemberScope();
        $date  = $request->get('date', today()->toDateString());

        if ($scope) {
            // Member: only their own payments on that day
            $payments = Payment::with(['member','bill'])
                                ->where('member_id', $scope->id)
                                ->whereDate('payment_date', $date)->get();
            $expenses = collect(); // members can't see expenses
            $totalIn  = $payments->sum('amount');
            $totalOut = 0;
            return view('reports.daily_transaction', compact('payments','expenses','totalIn','totalOut','date'));
        }

        $payments = Payment::with(['member','bill'])->whereDate('payment_date',$date)->get();
        $expenses = Expense::whereDate('expense_date',$date)->get();
        $totalIn  = $payments->sum('amount');
        $totalOut = $expenses->sum('amount');

        return view('reports.daily_transaction', compact('payments','expenses','totalIn','totalOut','date'));
    }
}
