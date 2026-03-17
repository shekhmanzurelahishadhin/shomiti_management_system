<?php

namespace App\Http\Controllers;

use App\Models\Member;
use App\Models\Bill;
use App\Models\Payment;
use App\Models\Expense;
use App\Models\WithdrawalRequest;
use App\Models\Election;
use Illuminate\Http\Request;
use Carbon\Carbon;

class DashboardController extends Controller
{
    public function index()
    {
        $now = Carbon::now();

        $totalMembers    = Member::where('status', 'active')->count();
        $totalCollection = Payment::whereMonth('payment_date', $now->month)
                                  ->whereYear('payment_date', $now->year)
                                  ->sum('amount');
        $yearlyCollection = Payment::whereYear('payment_date', $now->year)->sum('amount');
        $pendingDues      = Bill::whereIn('status', ['pending', 'partial', 'overdue'])
                                ->selectRaw('SUM(amount + fine - discount - paid_amount) as total')
                                ->value('total') ?? 0;
        $overdueCount     = Bill::where('status', 'overdue')->count();
        $monthlyExpenses  = Expense::whereMonth('expense_date', $now->month)
                                   ->whereYear('expense_date', $now->year)
                                   ->sum('amount');

        $recentPayments = Payment::with(['member', 'bill'])
                                  ->latest()
                                  ->take(10)
                                  ->get();

        // Monthly collection chart (last 6 months)
        $chartData = [];
        for ($i = 5; $i >= 0; $i--) {
            $month = Carbon::now()->subMonths($i);
            $chartData[] = [
                'label'  => $month->format('M Y'),
                'amount' => Payment::whereMonth('payment_date', $month->month)
                                   ->whereYear('payment_date', $month->year)
                                   ->sum('amount'),
            ];
        }

        $pendingWithdrawals    = WithdrawalRequest::where('status','pending')->count();
        $onHoldWithdrawals     = WithdrawalRequest::where('status','on_hold')->count();
        $withdrawalDue         = WithdrawalRequest::whereIn('status',['on_hold','partially_repaid'])
                                  ->selectRaw('SUM(total_amount - repaid_amount) as t')->value('t') ?? 0;

        $activeElection = Election::whereIn('status',['voting','nomination'])->latest()->first();
        $disconnectedMembers = Member::where('status','disconnected')->count();

                return view('dashboard', compact(
            'totalMembers', 'totalCollection', 'yearlyCollection',
            'pendingDues', 'overdueCount', 'monthlyExpenses',
            'recentPayments', 'chartData',
            'pendingWithdrawals', 'onHoldWithdrawals', 'withdrawalDue',
            'activeElection', 'disconnectedMembers'
        ));
    }
}
