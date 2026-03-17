<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\MemberController;
use App\Http\Controllers\BillController;
use App\Http\Controllers\PaymentController;
use App\Http\Controllers\CommitteeController;
use App\Http\Controllers\ExpenseController;
use App\Http\Controllers\ReportController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\SettingController;
use App\Http\Controllers\ActivityLogController;
use App\Http\Controllers\WithdrawalController;
use App\Http\Controllers\ElectionController;
use App\Http\Controllers\Auth\AuthenticatedSessionController;
use App\Http\Controllers\Auth\RegisteredUserController;
use App\Http\Controllers\Auth\PasswordResetLinkController;
use App\Http\Controllers\Auth\NewPasswordController;
use App\Http\Controllers\Auth\PasswordController;
use Illuminate\Support\Facades\Route;

// Guest routes
Route::middleware('guest')->group(function () {
    Route::get('login',                [AuthenticatedSessionController::class, 'create'])->name('login');
    Route::post('login',               [AuthenticatedSessionController::class, 'store']);
    Route::get('forgot-password',      [PasswordResetLinkController::class,    'create'])->name('password.request');
    Route::post('forgot-password',     [PasswordResetLinkController::class,    'store'])->name('password.email');
    Route::get('reset-password/{token}',[NewPasswordController::class,         'create'])->name('password.reset');
    Route::post('reset-password',      [NewPasswordController::class,          'store'])->name('password.store');
});

Route::middleware('auth')->group(function () {

    Route::post('logout', [AuthenticatedSessionController::class, 'destroy'])->name('logout');

    // Dashboard
    Route::get('/', [DashboardController::class, 'index'])->name('dashboard');
    Route::get('/dashboard', [DashboardController::class, 'index']);

    // Profile
    Route::get('/profile',    [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile',  [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
    Route::put('/password',   [PasswordController::class, 'update'])->name('password.update');

    // Members
    Route::middleware('permission:manage members')->group(function () {
        Route::resource('members', MemberController::class);
        Route::get('members/{member}/registration-pdf', [MemberController::class, 'registrationPdf'])->name('members.registration-pdf');
    });

    // Bills
    Route::middleware('permission:generate bills')->group(function () {
        Route::resource('bills', BillController::class);
        Route::post('bills/generate-monthly',   [BillController::class, 'generateMonthly'])->name('bills.generate-monthly');
        Route::post('bills/apply-fines',        [BillController::class, 'applyFines'])->name('bills.apply-fines');
    });

    // Payments
    Route::middleware('permission:collect payments')->group(function () {
        Route::resource('payments', PaymentController::class)->except(['edit', 'update']);
        Route::get('payments/{payment}/receipt',     [PaymentController::class, 'receipt'])->name('payments.receipt');
        Route::get('payments/{payment}/receipt-pdf', [PaymentController::class, 'receiptPdf'])->name('payments.receipt-pdf');
    });

    // Committees
    Route::middleware('permission:manage committees')->group(function () {
        Route::resource('committees', CommitteeController::class);
        Route::post('committees/{committee}/add-member',    [CommitteeController::class, 'addMember'])->name('committees.add-member');
        Route::delete('committees/{committee}/remove-member/{committeeMember}', [CommitteeController::class, 'removeMember'])->name('committees.remove-member');
        Route::post('committees/{committee}/record-draw',   [CommitteeController::class, 'recordDraw'])->name('committees.record-draw');
    });

    // Expenses
    Route::middleware('permission:manage expenses')->group(function () {
        Route::resource('expenses', ExpenseController::class)->except(['show']);
    });

    // Reports
    Route::middleware('permission:view reports')->prefix('reports')->name('reports.')->group(function () {
        Route::get('/',                   [ReportController::class, 'index'])->name('index');
        Route::get('monthly-collection',  [ReportController::class, 'monthlyCollection'])->name('monthly-collection');
        Route::get('defaulters',          [ReportController::class, 'defaulters'])->name('defaulters');
        Route::get('member-deposit',      [ReportController::class, 'memberDeposit'])->name('member-deposit');
        Route::get('annual-summary',      [ReportController::class, 'annualSummary'])->name('annual-summary');
        Route::get('expense-report',      [ReportController::class, 'expenseReport'])->name('expense-report');
        Route::get('daily-transaction',   [ReportController::class, 'dailyTransaction'])->name('daily-transaction');
    });

    // Users (admin only)
    Route::middleware('permission:manage users')->group(function () {
        Route::resource('users', UserController::class)->except(['show']);
        Route::post('users/{user}/reset-password', [UserController::class, 'resetPassword'])->name('users.reset-password');
    });

    // Settings
    Route::middleware('permission:manage settings')->group(function () {
        Route::get('settings',   [SettingController::class, 'index'])->name('settings.index');
        Route::post('settings',  [SettingController::class, 'update'])->name('settings.update');
    });

    // Activity Logs
    Route::middleware('role:Super Admin|Admin')->group(function () {
        Route::get('activity-logs', [ActivityLogController::class, 'index'])->name('activity-logs.index');
    });

    // Withdrawal Requests
    Route::middleware('permission:collect payments')->group(function () {
        Route::resource('withdrawals', WithdrawalController::class)->only(['index','create','store','show']);
        Route::post('withdrawals/{withdrawal}/approve',     [WithdrawalController::class, 'approve'])->name('withdrawals.approve');
        Route::post('withdrawals/{withdrawal}/reject',      [WithdrawalController::class, 'reject'])->name('withdrawals.reject');
        Route::post('withdrawals/{withdrawal}/repayment',   [WithdrawalController::class, 'recordRepayment'])->name('withdrawals.repayment');
        Route::post('withdrawals/{withdrawal}/mark-repaid', [WithdrawalController::class, 'markFullyRepaid'])->name('withdrawals.mark-repaid');
    });

    // Elections & Voting
    Route::prefix('elections')->name('elections.')->group(function () {
        Route::get('/',         [ElectionController::class, 'index'])->name('index');
        Route::get('/results',  fn() => redirect()->route('elections.index'));

        Route::middleware('permission:manage committees')->group(function () {
            Route::get('/create',                          [ElectionController::class, 'create'])->name('create');
            Route::post('/',                               [ElectionController::class, 'store'])->name('store');
            Route::post('{election}/status',               [ElectionController::class, 'updateStatus'])->name('status');
            Route::post('{election}/add-candidate',        [ElectionController::class, 'addCandidate'])->name('add-candidate');
            Route::delete('{election}/candidates/{candidate}', [ElectionController::class, 'removeCandidate'])->name('remove-candidate');
            Route::post('{election}/count',                [ElectionController::class, 'countVotes'])->name('count');
            Route::delete('{election}',                    [ElectionController::class, 'destroy'])->name('destroy');
        });

        Route::get('{election}',         [ElectionController::class, 'show'])->name('show');
        Route::get('{election}/results', [ElectionController::class, 'results'])->name('results');
        Route::post('{election}/vote',   [ElectionController::class, 'castVote'])->name('vote');
    });
});
