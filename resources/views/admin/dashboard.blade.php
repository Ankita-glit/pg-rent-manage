@extends('layouts.app')

@section('title', 'Admin Dashboard - Nice Stay Girls PG')

@section('content')
<div class="space-y-8">
    <!-- Header Title -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div>
            <h1 class="text-3xl font-extrabold text-white tracking-tight">Nice Stay Girls PG Dashboard</h1>
            <p class="text-sm text-slate-400">Overview for {{ $currentMonthName }} • Room Occupancy & Profitability</p>
        </div>

        <div class="flex items-center space-x-3">
            <a href="{{ route('admin.invoices.index') }}" class="px-4 py-2.5 rounded-xl bg-gradient-to-r from-pink-600 to-rose-600 hover:from-pink-500 hover:to-rose-500 text-white text-sm font-bold shadow-lg shadow-pink-500/20 transition-all flex items-center space-x-2">
                <i data-lucide="plus-circle" class="w-4 h-4"></i>
                <span>Generate Monthly Invoices</span>
            </a>
            <a href="{{ route('admin.expenses.index') }}" class="px-4 py-2.5 rounded-xl bg-slate-800 hover:bg-slate-700 text-slate-200 text-sm font-semibold border border-slate-700 transition-all flex items-center space-x-2">
                <i data-lucide="plus" class="w-4 h-4"></i>
                <span>Log Expense</span>
            </a>
        </div>
    </div>

    <!-- Alert Badges for Pending Action Items -->
    @if($pendingPaymentsCount > 0 || $pendingDelayRequestsCount > 0)
    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
        @if($pendingPaymentsCount > 0)
        <a href="{{ route('admin.payments.index') }}" class="p-4 rounded-2xl bg-amber-500/10 border border-amber-500/30 flex items-center justify-between hover:bg-amber-500/15 transition-all">
            <div class="flex items-center space-x-3">
                <div class="w-10 h-10 rounded-xl bg-amber-500/20 text-amber-300 flex items-center justify-center font-bold">
                    <i data-lucide="clock" class="w-5 h-5"></i>
                </div>
                <div>
                    <h4 class="text-sm font-bold text-amber-200">{{ $pendingPaymentsCount }} Pending Payment Verifications</h4>
                    <p class="text-xs text-amber-300/80">Renters uploaded payment screenshots or cash details waiting for approval.</p>
                </div>
            </div>
            <i data-lucide="chevron-right" class="w-5 h-5 text-amber-400"></i>
        </a>
        @endif

        @if($pendingDelayRequestsCount > 0)
        <a href="{{ route('admin.delay-requests.index') }}" class="p-4 rounded-2xl bg-rose-500/10 border border-rose-500/30 flex items-center justify-between hover:bg-rose-500/15 transition-all">
            <div class="flex items-center space-x-3">
                <div class="w-10 h-10 rounded-xl bg-rose-500/20 text-rose-300 flex items-center justify-center font-bold">
                    <i data-lucide="alert-circle" class="w-5 h-5"></i>
                </div>
                <div>
                    <h4 class="text-sm font-bold text-rose-200">{{ $pendingDelayRequestsCount }} Pending Payment Delay Requests</h4>
                    <p class="text-xs text-rose-300/80">Renters requested payment extensions requiring your approval.</p>
                </div>
            </div>
            <i data-lucide="chevron-right" class="w-5 h-5 text-rose-400"></i>
        </a>
        @endif
    </div>
    @endif

    <!-- Financial KPI Summary Cards -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-5">
        <!-- Monthly Revenue -->
        <div class="glass-card p-5 rounded-2xl relative overflow-hidden">
            <div class="flex items-center justify-between">
                <span class="text-xs font-semibold text-slate-400 uppercase tracking-wider">Monthly Collected Rent</span>
                <div class="w-9 h-9 rounded-xl bg-emerald-500/10 border border-emerald-500/20 text-emerald-400 flex items-center justify-center">
                    <i data-lucide="arrow-down-left" class="w-5 h-5"></i>
                </div>
            </div>
            <p class="mt-3 text-2xl font-extrabold text-white">₹{{ number_format($monthlyIncome, 2) }}</p>
            <p class="mt-1 text-xs text-emerald-400 font-medium">Approved rent payments</p>
        </div>

        <!-- Monthly Expenses -->
        <div class="glass-card p-5 rounded-2xl relative overflow-hidden">
            <div class="flex items-center justify-between">
                <span class="text-xs font-semibold text-slate-400 uppercase tracking-wider">Total Monthly Expenses</span>
                <div class="w-9 h-9 rounded-xl bg-rose-500/10 border border-rose-500/20 text-rose-400 flex items-center justify-center">
                    <i data-lucide="arrow-up-right" class="w-5 h-5"></i>
                </div>
            </div>
            <p class="mt-3 text-2xl font-extrabold text-white">₹{{ number_format($totalMonthlyExpenses, 2) }}</p>
            <p class="mt-1 text-xs text-slate-400">Repairs (₹{{ number_format($monthlyDirectExpenses, 0) }}) + Salaries (₹{{ number_format($monthlyStaffSalaries, 0) }})</p>
        </div>

        <!-- Net Profit / Loss -->
        <div class="glass-card p-5 rounded-2xl relative overflow-hidden">
            <div class="flex items-center justify-between">
                <span class="text-xs font-semibold text-slate-400 uppercase tracking-wider">Net Monthly Profit</span>
                <div class="w-9 h-9 rounded-xl {{ $netProfitLoss >= 0 ? 'bg-indigo-500/10 border border-indigo-500/20 text-indigo-400' : 'bg-rose-500/10 border border-rose-500/20 text-rose-400' }} flex items-center justify-center">
                    <i data-lucide="{{ $netProfitLoss >= 0 ? 'trending-up' : 'trending-down' }}" class="w-5 h-5"></i>
                </div>
            </div>
            <p class="mt-3 text-2xl font-extrabold {{ $netProfitLoss >= 0 ? 'text-indigo-300' : 'text-rose-400' }}">
                ₹{{ number_format($netProfitLoss, 2) }}
            </p>
            <p class="mt-1 text-xs text-slate-400">Income minus total expenses</p>
        </div>

        <!-- Occupancy Rate -->
        <div class="glass-card p-5 rounded-2xl relative overflow-hidden">
            <div class="flex items-center justify-between">
                <span class="text-xs font-semibold text-slate-400 uppercase tracking-wider">Room Occupancy</span>
                <div class="w-9 h-9 rounded-xl bg-pink-500/10 border border-pink-500/20 text-pink-400 flex items-center justify-center">
                    <i data-lucide="bed" class="w-5 h-5"></i>
                </div>
            </div>
            <p class="mt-3 text-2xl font-extrabold text-white">{{ $occupancyRate }}%</p>
            <p class="mt-1 text-xs text-slate-400">{{ $totalOccupiedBeds }} / {{ $totalBedsCapacity }} beds occupied across {{ $totalFloors }} floors</p>
        </div>
    </div>

    <!-- Financial Trend Chart & Quick Stats -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
        <!-- 6-Month Profit & Loss Trend Chart -->
        <div class="lg:col-span-2 glass-panel p-6 rounded-3xl border border-slate-800 space-y-4">
            <div class="flex items-center justify-between">
                <div>
                    <h3 class="text-lg font-bold text-white">Profit & Loss Financial Trend</h3>
                    <p class="text-xs text-slate-400">Rent Income vs Operational Expenses over the past 6 months</p>
                </div>
                <a href="{{ route('admin.expenses.profit-loss') }}" class="text-xs text-pink-400 hover:text-pink-300 font-bold flex items-center space-x-1">
                    <span>Full P&L Report</span>
                    <i data-lucide="chevron-right" class="w-3.5 h-3.5"></i>
                </a>
            </div>
            <div class="h-64 relative">
                <canvas id="plChart"></canvas>
            </div>
        </div>

        <!-- PG Property Breakdown -->
        <div class="glass-panel p-6 rounded-3xl border border-slate-800 space-y-5 flex flex-col justify-between">
            <div>
                <h3 class="text-lg font-bold text-white mb-4">Property Structure</h3>
                <div class="space-y-4">
                    <div class="flex items-center justify-between p-3.5 rounded-2xl bg-slate-800/60 border border-slate-700/50">
                        <div class="flex items-center space-x-3">
                            <i data-lucide="layers" class="w-5 h-5 text-indigo-400"></i>
                            <span class="text-sm font-medium text-slate-200">Total Floors</span>
                        </div>
                        <span class="text-base font-bold text-white">{{ $totalFloors }} Floors</span>
                    </div>

                    <div class="flex items-center justify-between p-3.5 rounded-2xl bg-slate-800/60 border border-slate-700/50">
                        <div class="flex items-center space-x-3">
                            <i data-lucide="home" class="w-5 h-5 text-pink-400"></i>
                            <span class="text-sm font-medium text-slate-200">Total Rooms</span>
                        </div>
                        <span class="text-base font-bold text-white">{{ $totalRooms }} Rooms</span>
                    </div>

                    <div class="flex items-center justify-between p-3.5 rounded-2xl bg-slate-800/60 border border-slate-700/50">
                        <div class="flex items-center space-x-3">
                            <i data-lucide="users" class="w-5 h-5 text-emerald-400"></i>
                            <span class="text-sm font-medium text-slate-200">Active Residents</span>
                        </div>
                        <span class="text-base font-bold text-white">{{ $totalRenters }} Residents</span>
                    </div>
                </div>
            </div>

            <div class="pt-4 border-t border-slate-800">
                <a href="{{ route('admin.renters.create') }}" class="w-full py-3.5 px-4 rounded-xl bg-gradient-to-r from-pink-600 to-rose-600 hover:from-pink-500 hover:to-rose-500 text-white font-bold text-sm shadow-lg shadow-pink-500/20 transition-all flex items-center justify-center space-x-2">
                    <i data-lucide="user-plus" class="w-4 h-4"></i>
                    <span>Register New Resident</span>
                </a>
            </div>
        </div>
    </div>

    <!-- Recent Payments & Delay Requests -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
        <!-- Recent Rent Payment Proofs -->
        <div class="glass-panel p-6 rounded-3xl border border-slate-800 space-y-4">
            <div class="flex items-center justify-between">
                <h3 class="text-lg font-bold text-white">Recent Payment Submissions</h3>
                <a href="{{ route('admin.payments.index') }}" class="text-xs text-pink-400 hover:text-pink-300 font-bold">View All</a>
            </div>

            <div class="overflow-x-auto">
                <table class="w-full text-left text-xs">
                    <thead class="text-slate-400 border-b border-slate-800 uppercase tracking-wider">
                        <tr>
                            <th class="pb-3 font-semibold">Renter & Room</th>
                            <th class="pb-3 font-semibold">Method</th>
                            <th class="pb-3 font-semibold">Amount</th>
                            <th class="pb-3 font-semibold">Status</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-800/60">
                        @forelse($recentPayments as $p)
                        <tr class="hover:bg-slate-800/30">
                            <td class="py-3">
                                <p class="font-bold text-white">{{ $p->invoice->renter->name ?? 'Unknown' }}</p>
                                <p class="text-[11px] text-slate-400">Room {{ $p->invoice->room->room_number ?? 'N/A' }}</p>
                            </td>
                            <td class="py-3">
                                <span class="capitalize px-2 py-0.5 rounded text-[11px] font-semibold {{ $p->payment_method === 'online' ? 'bg-pink-500/10 text-pink-300' : 'bg-purple-500/10 text-purple-300' }}">
                                    {{ $p->payment_method }}
                                </span>
                            </td>
                            <td class="py-3 font-bold text-white">₹{{ number_format($p->amount, 2) }}</td>
                            <td class="py-3">
                                @if($p->status === 'approved')
                                <span class="px-2 py-0.5 rounded-full text-[10px] font-bold bg-emerald-500/20 text-emerald-300">Approved</span>
                                @elseif($p->status === 'pending_approval')
                                <span class="px-2 py-0.5 rounded-full text-[10px] font-bold bg-amber-500/20 text-amber-300">Pending Review</span>
                                @else
                                <span class="px-2 py-0.5 rounded-full text-[10px] font-bold bg-rose-500/20 text-rose-300">Rejected</span>
                                @endif
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="4" class="py-4 text-center text-slate-500">No payment submissions yet.</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Recent Delay Requests -->
        <div class="glass-panel p-6 rounded-3xl border border-slate-800 space-y-4">
            <div class="flex items-center justify-between">
                <h3 class="text-lg font-bold text-white">Recent Rent Delay Requests</h3>
                <a href="{{ route('admin.delay-requests.index') }}" class="text-xs text-pink-400 hover:text-pink-300 font-bold">View All</a>
            </div>

            <div class="overflow-x-auto">
                <table class="w-full text-left text-xs">
                    <thead class="text-slate-400 border-b border-slate-800 uppercase tracking-wider">
                        <tr>
                            <th class="pb-3 font-semibold">Renter</th>
                            <th class="pb-3 font-semibold">Requested Date</th>
                            <th class="pb-3 font-semibold">Reason</th>
                            <th class="pb-3 font-semibold">Status</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-800/60">
                        @forelse($recentDelayRequests as $d)
                        <tr class="hover:bg-slate-800/30">
                            <td class="py-3 font-bold text-white">{{ $d->renter->name ?? 'Unknown' }}</td>
                            <td class="py-3 text-slate-300">{{ $d->requested_date->format('d M Y') }}</td>
                            <td class="py-3 text-slate-400 max-w-[150px] truncate" title="{{ $d->reason }}">{{ $d->reason }}</td>
                            <td class="py-3">
                                @if($d->status === 'approved')
                                <span class="px-2 py-0.5 rounded-full text-[10px] font-bold bg-emerald-500/20 text-emerald-300">Approved</span>
                                @elseif($d->status === 'pending')
                                <span class="px-2 py-0.5 rounded-full text-[10px] font-bold bg-rose-500/20 text-rose-300">Pending</span>
                                @else
                                <span class="px-2 py-0.5 rounded-full text-[10px] font-bold bg-slate-700 text-slate-300">Rejected</span>
                                @endif
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="4" class="py-4 text-center text-slate-500">No delay requests submitted.</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
    document.addEventListener("DOMContentLoaded", function () {
        const ctx = document.getElementById('plChart').getContext('2d');
        
        const labels = {!! json_encode($chartMonths) !!};
        const incomeData = {!! json_encode($chartIncome) !!};
        const expenseData = {!! json_encode($chartExpenses) !!};
        const profitData = {!! json_encode($chartProfit) !!};

        new Chart(ctx, {
            type: 'line',
            data: {
                labels: labels,
                datasets: [
                    {
                        label: 'Income (Collected Rent)',
                        data: incomeData,
                        borderColor: '#10b981',
                        backgroundColor: 'rgba(16, 185, 129, 0.1)',
                        fill: true,
                        tension: 0.3,
                    },
                    {
                        label: 'Expenses (Repairs + Staff)',
                        data: expenseData,
                        borderColor: '#f43f5e',
                        backgroundColor: 'rgba(244, 63, 94, 0.1)',
                        fill: true,
                        tension: 0.3,
                    },
                    {
                        label: 'Net Profit',
                        data: profitData,
                        borderColor: '#ec4899',
                        borderDash: [5, 5],
                        tension: 0.3,
                    }
                ]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        labels: { color: '#94a3b8', font: { family: 'Inter', size: 11 } }
                    }
                },
                scales: {
                    x: {
                        ticks: { color: '#64748b' },
                        grid: { color: 'rgba(255, 255, 255, 0.05)' }
                    },
                    y: {
                        ticks: { color: '#64748b' },
                        grid: { color: 'rgba(255, 255, 255, 0.05)' }
                    }
                }
            }
        });
    });
</script>
@endpush
@endsection
