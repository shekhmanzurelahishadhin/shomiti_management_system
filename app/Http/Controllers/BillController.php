<?php

namespace App\Http\Controllers;

use App\Models\Bill;
use App\Models\Member;
use App\Models\Setting;
use App\Models\ActivityLog;
use Illuminate\Http\Request;
use Carbon\Carbon;

class BillController extends Controller
{
    public function index(Request $request)
    {
        $query = Bill::with('member');

        if ($request->filled('month')) $query->where('bill_month', $request->month);
        if ($request->filled('year'))  $query->where('bill_year',  $request->year);
        if ($request->filled('status'))$query->where('status',     $request->status);
        if ($request->filled('search')) {
            $s = $request->search;
            $query->whereHas('member', fn($q) => $q->where('name', 'like', "%$s%")
                                                    ->orWhere('member_id', 'like', "%$s%"));
        }

        $bills = $query->latest()->paginate(20)->withQueryString();
        return view('bills.index', compact('bills'));
    }

    public function create()
    {
        $members = Member::where('status', 'active')->orderBy('name')->get();
        return view('bills.create', compact('members'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'member_id'  => 'required|exists:members,id',
            'bill_month' => 'required|integer|between:1,12',
            'bill_year'  => 'required|integer|min:2020',
            'amount'     => 'required|numeric|min:0',
            'due_date'   => 'required|date',
            'notes'      => 'nullable|string',
        ]);

        $existing = Bill::where('member_id', $data['member_id'])
                        ->where('bill_month', $data['bill_month'])
                        ->where('bill_year',  $data['bill_year'])
                        ->exists();

        if ($existing) {
            return back()->withErrors(['bill_month' => 'Bill already exists for this member for the selected month/year.'])->withInput();
        }

        $data['created_by'] = auth()->id();
        $bill = Bill::create($data);
        ActivityLog::log('create', "Generated bill for member ID {$bill->member_id}", $bill);

        return redirect()->route('bills.show', $bill)->with('success', 'Bill created successfully.');
    }

    public function show(Bill $bill)
    {
        $bill->load(['member', 'payments.collector']);
        return view('bills.show', compact('bill'));
    }

    public function edit(Bill $bill)
    {
        $members = Member::where('status', 'active')->orderBy('name')->get();
        return view('bills.edit', compact('bill', 'members'));
    }

    public function update(Request $request, Bill $bill)
    {
        $data = $request->validate([
            'amount'           => 'required|numeric|min:0',
            'fine'             => 'nullable|numeric|min:0',
            'discount'         => 'nullable|numeric|min:0',
            'due_date'         => 'required|date',
            'fine_waived'      => 'nullable|boolean',
            'fine_waive_reason'=> 'nullable|string|max:500',
            'notes'            => 'nullable|string',
        ]);

        $data['fine_waived'] = $request->boolean('fine_waived');
        if ($data['fine_waived']) {
            $data['fine'] = 0;
        }

        $bill->update($data);
        $bill->updateStatus();
        ActivityLog::log('update', "Updated bill #{$bill->id}", $bill);

        return redirect()->route('bills.show', $bill)->with('success', 'Bill updated successfully.');
    }

    public function destroy(Bill $bill)
    {
        ActivityLog::log('delete', "Deleted bill #{$bill->id}", $bill);
        $bill->delete();
        return redirect()->route('bills.index')->with('success', 'Bill deleted.');
    }

    public function generateMonthly(Request $request)
    {
        $request->validate([
            'month' => 'required|integer|between:1,12',
            'year'  => 'required|integer|min:2020',
        ]);

        $month   = $request->month;
        $year    = $request->year;
        $dueDay  = (int) Setting::get('due_date_end', 15);
        $dueDate = Carbon::create($year, $month, $dueDay);

        $members = Member::whereIn('status', ['active', 'suspended'])->get();
        $created = 0;

        foreach ($members as $member) {
            $exists = Bill::where('member_id', $member->id)
                          ->where('bill_month', $month)
                          ->where('bill_year', $year)
                          ->exists();
            if (!$exists) {
                Bill::create([
                    'member_id'  => $member->id,
                    'bill_month' => $month,
                    'bill_year'  => $year,
                    'amount'     => $member->monthly_deposit,
                    'due_date'   => $dueDate,
                    'created_by' => auth()->id(),
                ]);
                $created++;
            }
        }

        ActivityLog::log('generate', "Generated {$created} bills for {$month}/{$year}");
        return redirect()->route('bills.index')->with('success', "Generated {$created} bills for " . date('F Y', mktime(0,0,0,$month,1,$year)));
    }

    public function applyFines()
    {
        $lateFee = (float) Setting::get('late_fee', 50);
        $updated = 0;

        $overdueBills = Bill::whereIn('status', ['pending', 'partial'])
                            ->where('due_date', '<', now())
                            ->where('fine', 0)
                            ->where('fine_waived', false)
                            ->get();

        foreach ($overdueBills as $bill) {
            $bill->update(['fine' => $lateFee, 'status' => 'overdue']);
            $updated++;
        }

        return redirect()->route('bills.index')->with('success', "Applied fines to {$updated} overdue bills.");
    }
}
