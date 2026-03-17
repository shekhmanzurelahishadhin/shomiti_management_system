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
    public function index()
    {
        return view('reports.index');
    }

    public function monthlyCollection(Request $request)
    {
        $month = $request->get('month', now()->month);
        $year  = $request->get('year',  now()->year);

        $payments = Payment::with(['member', 'bill'])
                           ->whereMonth('payment_date', $month)
                           ->whereYear('payment_date',  $year)
                           ->latest()->get();

        $total = $payments->sum('amount');

        if ($request->get('export') === 'pdf') {
            $pdf = Pdf::loadView('reports.monthly_collection_pdf', compact('payments', 'total', 'month', 'year'))
                      ->setPaper('a4');
            return $pdf->download("monthly-collection-{$year}-{$month}.pdf");
        }

        return view('reports.monthly_collection', compact('payments', 'total', 'month', 'year'));
    }

    public function defaulters(Request $request)
    {
        $month = $request->get('month', now()->month);
        $year  = $request->get('year',  now()->year);

        $defaulters = Bill::with('member')
                          ->whereIn('status', ['pending', 'overdue'])
                          ->where('bill_month', $month)
                          ->where('bill_year', $year)
                          ->get();

        if ($request->get('export') === 'pdf') {
            $pdf = Pdf::loadView('reports.defaulters_pdf', compact('defaulters', 'month', 'year'))
                      ->setPaper('a4');
            return $pdf->download("defaulters-{$year}-{$month}.pdf");
        }

        return view('reports.defaulters', compact('defaulters', 'month', 'year'));
    }

    public function memberDeposit(Request $request)
    {
        $year   = $request->get('year', now()->year);
        $member = null;
        $bills  = collect();

        if ($request->filled('member_id')) {
            $member = Member::findOrFail($request->member_id);
            $bills  = Bill::where('member_id', $member->id)
                          ->where('bill_year', $year)
                          ->orderBy('bill_month')->get();
        }

        $members = Member::orderBy('name')->get();

        if ($request->get('export') === 'pdf' && $member) {
            $pdf = Pdf::loadView('reports.member_deposit_pdf', compact('member', 'bills', 'year'))
                      ->setPaper('a4');
            return $pdf->download("member-deposit-{$member->member_id}-{$year}.pdf");
        }

        return view('reports.member_deposit', compact('members', 'member', 'bills', 'year'));
    }

    public function annualSummary(Request $request)
    {
        $year = $request->get('year', now()->year);

        $monthlyData = [];
        for ($m = 1; $m <= 12; $m++) {
            $collected  = Payment::whereMonth('payment_date', $m)->whereYear('payment_date', $year)->sum('amount');
            $billed     = Bill::where('bill_month', $m)->where('bill_year', $year)->sum('amount');
            $expenses   = Expense::whereMonth('expense_date', $m)->whereYear('expense_date', $year)->sum('amount');
            $monthlyData[] = [
                'month'     => date('F', mktime(0,0,0,$m,1)),
                'billed'    => $billed,
                'collected' => $collected,
                'expenses'  => $expenses,
                'balance'   => $collected - $expenses,
            ];
        }

        $totalCollected = array_sum(array_column($monthlyData, 'collected'));
        $totalExpenses  = array_sum(array_column($monthlyData, 'expenses'));

        if ($request->get('export') === 'pdf') {
            $pdf = Pdf::loadView('reports.annual_summary_pdf', compact('monthlyData', 'totalCollected', 'totalExpenses', 'year'))
                      ->setPaper('a4', 'landscape');
            return $pdf->download("annual-summary-{$year}.pdf");
        }

        return view('reports.annual_summary', compact('monthlyData', 'totalCollected', 'totalExpenses', 'year'));
    }

    public function expenseReport(Request $request)
    {
        $query = Expense::query();
        if ($request->filled('date_from')) $query->where('expense_date', '>=', $request->date_from);
        if ($request->filled('date_to'))   $query->where('expense_date', '<=', $request->date_to);
        if ($request->filled('category'))  $query->where('category', $request->category);

        $expenses = $query->latest('expense_date')->get();
        $total    = $expenses->sum('amount');
        $byCategory = $expenses->groupBy('category')->map->sum('amount');

        if ($request->get('export') === 'pdf') {
            $pdf = Pdf::loadView('reports.expense_report_pdf', compact('expenses', 'total', 'byCategory'))
                      ->setPaper('a4');
            return $pdf->download("expense-report.pdf");
        }

        return view('reports.expense_report', compact('expenses', 'total', 'byCategory'));
    }

    public function dailyTransaction(Request $request)
    {
        $date = $request->get('date', today()->toDateString());

        $payments  = Payment::with(['member', 'bill'])->whereDate('payment_date', $date)->get();
        $expenses  = Expense::whereDate('expense_date', $date)->get();
        $totalIn   = $payments->sum('amount');
        $totalOut  = $expenses->sum('amount');

        return view('reports.daily_transaction', compact('payments', 'expenses', 'totalIn', 'totalOut', 'date'));
    }
}
