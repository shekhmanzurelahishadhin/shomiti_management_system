<?php

namespace App\Http\Controllers;

use App\Models\Expense;
use App\Models\ActivityLog;
use Illuminate\Http\Request;

class ExpenseController extends Controller
{
    public function index(Request $request)
    {
        $query = Expense::with('creator');

        if ($request->filled('category'))  $query->where('category', $request->category);
        if ($request->filled('date_from')) $query->where('expense_date', '>=', $request->date_from);
        if ($request->filled('date_to'))   $query->where('expense_date', '<=', $request->date_to);
        if ($request->filled('search'))    $query->where('title', 'like', '%'.$request->search.'%');

        $expenses     = $query->latest('expense_date')->paginate(20)->withQueryString();
        $monthTotal   = Expense::whereMonth('expense_date', now()->month)
                                ->whereYear('expense_date', now()->year)->sum('amount');
        $categories   = Expense::select('category')->distinct()->pluck('category');

        return view('expenses.index', compact('expenses', 'monthTotal', 'categories'));
    }

    public function create()
    {
        $categories = ['general', 'office', 'utilities', 'maintenance', 'salary', 'miscellaneous'];
        return view('expenses.create', compact('categories'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'title'        => 'required|string|max:255',
            'category'     => 'required|string|max:100',
            'amount'       => 'required|numeric|min:0',
            'expense_date' => 'required|date',
            'description'  => 'nullable|string',
        ]);

        $data['created_by'] = auth()->id();
        $expense = Expense::create($data);
        ActivityLog::log('create', "Added expense: {$expense->title}", $expense);

        return redirect()->route('expenses.index')->with('success', 'Expense added.');
    }

    public function edit(Expense $expense)
    {
        $categories = ['general', 'office', 'utilities', 'maintenance', 'salary', 'miscellaneous'];
        return view('expenses.edit', compact('expense', 'categories'));
    }

    public function update(Request $request, Expense $expense)
    {
        $data = $request->validate([
            'title'        => 'required|string|max:255',
            'category'     => 'required|string|max:100',
            'amount'       => 'required|numeric|min:0',
            'expense_date' => 'required|date',
            'description'  => 'nullable|string',
        ]);

        $expense->update($data);
        ActivityLog::log('update', "Updated expense: {$expense->title}", $expense);

        return redirect()->route('expenses.index')->with('success', 'Expense updated.');
    }

    public function destroy(Expense $expense)
    {
        ActivityLog::log('delete', "Deleted expense: {$expense->title}", $expense);
        $expense->delete();
        return redirect()->route('expenses.index')->with('success', 'Expense deleted.');
    }
}
