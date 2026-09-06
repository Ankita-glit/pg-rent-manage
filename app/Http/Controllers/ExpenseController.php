<?php

namespace App\Http\Controllers;

use App\Models\Expense;
use App\Models\RentPayment;
use App\Models\StaffSalary;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class ExpenseController extends Controller
{
    public function index(Request $request)
    {
        $query = Expense::query();

        if ($request->filled('category')) {
            $query->where('category', $request->category);
        }

        if ($request->filled('month')) {
            $date = Carbon::createFromFormat('Y-m', $request->month);
            $query->whereMonth('expense_date', $date->month)->whereYear('expense_date', $date->year);
        }

        $expenses = $query->latest('expense_date')->paginate(15);
        $categories = Expense::categories();
        $totalExpensesAmount = $expenses->sum('amount');

        return view('admin.expenses.index', compact('expenses', 'categories', 'totalExpensesAmount'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'category' => 'required|string',
            'title' => 'required|string|max:255',
            'amount' => 'required|numeric|min:0.01',
            'expense_date' => 'required|date',
            'receipt' => 'nullable|image|mimes:jpeg,png,jpg,webp,pdf|max:5120',
            'notes' => 'nullable|string',
        ]);

        $receiptPath = null;
        if ($request->hasFile('receipt')) {
            $receiptPath = $request->file('receipt')->store('expenses', 'public');
        }

        Expense::create([
            'category' => $validated['category'],
            'title' => $validated['title'],
            'amount' => $validated['amount'],
            'expense_date' => $validated['expense_date'],
            'receipt_path' => $receiptPath,
            'notes' => $validated['notes'] ?? null,
        ]);

        return back()->with('success', 'Expense logged successfully!');
    }

    public function destroy(Expense $expense)
    {
        if ($expense->receipt_path) {
            Storage::disk('public')->delete($expense->receipt_path);
        }
        $expense->delete();
        return back()->with('success', 'Expense record deleted.');
    }

    public function profitLossReport(Request $request)
    {
        $selectedMonth = $request->month ?? Carbon::now()->format('Y-m');
        $date = Carbon::createFromFormat('Y-m', $selectedMonth);

        // Rent Income
        $rentIncome = RentPayment::where('status', 'approved')
            ->whereMonth('payment_date', $date->month)
            ->whereYear('payment_date', $date->year)
            ->sum('amount');

        // Direct Expenses by Category
        $expensesByCategory = Expense::whereMonth('expense_date', $date->month)
            ->whereYear('expense_date', $date->year)
            ->selectRaw('category, SUM(amount) as total')
            ->groupBy('category')
            ->pluck('total', 'category');

        $totalDirectExpenses = $expensesByCategory->sum();

        // Staff Salaries Total
        $staffSalaries = StaffSalary::whereMonth('payment_date', $date->month)
            ->whereYear('payment_date', $date->year)
            ->sum('amount');

        $grandTotalExpenses = $totalDirectExpenses + $staffSalaries;
        $netProfitLoss = $rentIncome - $grandTotalExpenses;

        $categories = Expense::categories();

        return view('admin.expenses.profit-loss', compact(
            'selectedMonth',
            'date',
            'rentIncome',
            'expensesByCategory',
            'totalDirectExpenses',
            'staffSalaries',
            'grandTotalExpenses',
            'netProfitLoss',
            'categories'
        ));
    }
}
