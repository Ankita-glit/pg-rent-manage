@extends('layouts.app')

@section('title', 'Profit & Loss Financial Report - Nice Stay Girls PG')

@section('content')
<div class="space-y-8 max-w-5xl mx-auto">
    <!-- Header -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 print:hidden">
        <div>
            <h1 class="text-3xl font-extrabold text-white tracking-tight">Profit & Loss Financial Statement</h1>
            <p class="text-sm text-slate-400">Detailed financial summary of income vs operational expenses for {{ $date->format('F Y') }}</p>
        </div>

        <div class="flex items-center space-x-3">
            <form method="GET" action="{{ route('admin.expenses.profit-loss') }}" class="flex items-center space-x-2">
                <input type="month" name="month" value="{{ $selectedMonth }}" class="px-3 py-2 rounded-xl bg-slate-900 border border-slate-700 text-white text-xs">
                <button type="submit" class="px-4 py-2 rounded-xl bg-slate-800 hover:bg-slate-700 text-slate-200 text-xs font-semibold border border-slate-700">Go</button>
            </form>

            <button onclick="window.print()" class="px-4 py-2 rounded-xl bg-pink-600 hover:bg-pink-500 text-white text-xs font-bold shadow flex items-center space-x-1.5">
                <i data-lucide="printer" class="w-4 h-4"></i>
                <span>Print Statement</span>
            </button>
        </div>
    </div>

    <!-- P&L Financial Summary Card -->
    <div class="glass-panel p-8 rounded-3xl border border-slate-800 space-y-8">
        <div class="border-b border-slate-800 pb-6 flex items-center justify-between">
            <div>
                <span class="text-xs font-bold text-pink-400 uppercase tracking-widest block">Financial Statement Report</span>
                <h2 class="text-2xl font-extrabold text-white">Nice Stay Girls PG • {{ $date->format('F Y') }}</h2>
            </div>
            <div class="text-right">
                <span class="text-xs text-slate-400 block">Generated On</span>
                <span class="text-xs font-semibold text-white">{{ date('d M Y, h:i A') }}</span>
            </div>
        </div>

        <!-- Income Section -->
        <div class="space-y-3">
            <div class="flex items-center justify-between border-b border-slate-800 pb-2">
                <h3 class="text-sm font-bold uppercase tracking-wider text-emerald-400 flex items-center">
                    <i data-lucide="arrow-down-left" class="w-4 h-4 mr-2"></i>
                    1. Revenue & Rent Inflows
                </h3>
                <span class="text-sm font-bold text-emerald-400">Total Collected</span>
            </div>

            <div class="flex items-center justify-between py-2 text-sm">
                <span class="text-slate-300">Collected Rent Payments (Approved)</span>
                <span class="font-extrabold text-white">₹{{ number_format($rentIncome, 2) }}</span>
            </div>

            <div class="flex items-center justify-between py-3 px-4 rounded-2xl bg-emerald-500/10 border border-emerald-500/30 text-emerald-300 font-bold text-base">
                <span>TOTAL GROSS REVENUE</span>
                <span>₹{{ number_format($rentIncome, 2) }}</span>
            </div>
        </div>

        <!-- Expense Section -->
        <div class="space-y-4">
            <div class="flex items-center justify-between border-b border-slate-800 pb-2">
                <h3 class="text-sm font-bold uppercase tracking-wider text-rose-400 flex items-center">
                    <i data-lucide="arrow-up-right" class="w-4 h-4 mr-2"></i>
                    2. Operational Expenses & Outflows
                </h3>
                <span class="text-sm font-bold text-rose-400">Total Expenses</span>
            </div>

            <!-- Direct Expenses Breakdown -->
            <div class="space-y-2 text-sm pl-2">
                @foreach($categories as $catKey => $catLabel)
                <div class="flex items-center justify-between py-1.5 border-b border-slate-800/40 text-slate-300">
                    <span>{{ $catLabel }}</span>
                    <span class="font-mono text-slate-200">₹{{ number_format($expensesByCategory[$catKey] ?? 0, 2) }}</span>
                </div>
                @endforeach

                <div class="flex items-center justify-between py-1.5 border-b border-slate-800/40 text-slate-300">
                    <span class="font-semibold text-purple-300">Staff Salaries Payouts</span>
                    <span class="font-mono text-purple-300 font-bold">₹{{ number_format($staffSalaries, 2) }}</span>
                </div>
            </div>

            <div class="flex items-center justify-between py-3 px-4 rounded-2xl bg-rose-500/10 border border-rose-500/30 text-rose-300 font-bold text-base">
                <span>TOTAL OPERATIONAL EXPENSES</span>
                <span>₹{{ number_format($grandTotalExpenses, 2) }}</span>
            </div>
        </div>

        <!-- Net Profit / Loss Section -->
        <div class="pt-6 border-t-2 border-slate-700">
            <div class="p-6 rounded-3xl {{ $netProfitLoss >= 0 ? 'bg-gradient-to-r from-emerald-600/20 to-indigo-600/20 border-emerald-500/40' : 'bg-rose-600/20 border-rose-500/40' }} border-2 flex items-center justify-between">
                <div>
                    <span class="text-xs font-bold uppercase tracking-widest text-slate-300 block">NET MONTHLY FINANCIAL RESULT</span>
                    <h3 class="text-3xl font-extrabold {{ $netProfitLoss >= 0 ? 'text-emerald-400' : 'text-rose-400' }}">
                        {{ $netProfitLoss >= 0 ? 'NET PROFIT' : 'NET LOSS' }}: ₹{{ number_format(abs($netProfitLoss), 2) }}
                    </h3>
                </div>

                <div class="w-16 h-16 rounded-2xl {{ $netProfitLoss >= 0 ? 'bg-emerald-500/20 text-emerald-400' : 'bg-rose-500/20 text-rose-400' }} flex items-center justify-center font-bold">
                    <i data-lucide="{{ $netProfitLoss >= 0 ? 'trending-up' : 'trending-down' }}" class="w-8 h-8"></i>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
