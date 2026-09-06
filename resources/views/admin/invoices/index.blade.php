@extends('layouts.app')

@section('title', 'Monthly Rent Invoices - UrbanStay PG')

@section('content')
<div class="space-y-6">
    <!-- Header & Batch Invoice Generator -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div>
            <h1 class="text-3xl font-extrabold text-white tracking-tight">Monthly Rent Invoices</h1>
            <p class="text-sm text-slate-400">Generate & track monthly rent demands for all active PG tenants</p>
        </div>

        <!-- Monthly Invoice Generator Form -->
        <form action="{{ route('admin.invoices.generate') }}" method="POST" class="flex items-center space-x-2 bg-slate-800/90 p-2 rounded-2xl border border-slate-700">
            @csrf
            <input type="month" name="month_year" value="{{ date('Y-m') }}" required class="px-3 py-1.5 rounded-xl bg-slate-900 border border-slate-700 text-white text-xs">
            <button type="submit" class="px-4 py-2 rounded-xl bg-gradient-to-r from-blue-600 to-indigo-600 hover:from-blue-500 hover:to-indigo-500 text-white text-xs font-bold shadow transition-all flex items-center space-x-1.5 shrink-0">
                <i data-lucide="zap" class="w-3.5 h-3.5"></i>
                <span>Generate Bills</span>
            </button>
        </form>
    </div>

    <!-- Filters Bar -->
    <div class="glass-panel p-4 rounded-2xl border border-slate-800">
        <form method="GET" action="{{ route('admin.invoices.index') }}" class="grid grid-cols-1 sm:grid-cols-4 gap-4">
            <div>
                <input type="text" name="search" value="{{ request('search') }}" placeholder="Search renter name..." class="w-full px-4 py-2 rounded-xl bg-slate-900 border border-slate-700 text-white text-xs">
            </div>

            <div>
                <input type="month" name="month" value="{{ request('month', $selectedMonth) }}" class="w-full px-4 py-2 rounded-xl bg-slate-900 border border-slate-700 text-white text-xs">
            </div>

            <div>
                <select name="status" class="w-full px-4 py-2 rounded-xl bg-slate-900 border border-slate-700 text-white text-xs">
                    <option value="">Status (All)</option>
                    <option value="pending" {{ request('status') === 'pending' ? 'selected' : '' }}>Pending Payment</option>
                    <option value="paid" {{ request('status') === 'paid' ? 'selected' : '' }}>Paid & Approved</option>
                    <option value="delay_requested" {{ request('status') === 'delay_requested' ? 'selected' : '' }}>Delay Requested</option>
                    <option value="overdue" {{ request('status') === 'overdue' ? 'selected' : '' }}>Overdue</option>
                </select>
            </div>

            <div class="flex items-center space-x-2">
                <button type="submit" class="w-full py-2 px-3 rounded-xl bg-slate-800 hover:bg-slate-700 text-slate-200 text-xs font-semibold border border-slate-700">Filter</button>
                <a href="{{ route('admin.invoices.index') }}" class="py-2 px-3 rounded-xl bg-slate-900 text-slate-400 hover:text-white text-xs">Reset</a>
            </div>
        </form>
    </div>

    <!-- Invoices Table -->
    <div class="glass-panel rounded-3xl border border-slate-800 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left text-sm">
                <thead class="bg-slate-900/80 text-slate-400 border-b border-slate-800 text-xs uppercase tracking-wider">
                    <tr>
                        <th class="p-4">Invoice ID & Month</th>
                        <th class="p-4">Renter & Room</th>
                        <th class="p-4">Rent Amount</th>
                        <th class="p-4">Due Date</th>
                        <th class="p-4">Payment / Delay Status</th>
                        <th class="p-4 text-right">Receipt / Details</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-800/60">
                    @forelse($invoices as $inv)
                    <tr class="hover:bg-slate-800/30">
                        <td class="p-4">
                            <div class="font-bold text-white">#INV-{{ str_pad($inv->id, 5, '0', STR_PAD_LEFT) }}</div>
                            <div class="text-xs text-slate-400">{{ $inv->month_year }}</div>
                        </td>

                        <td class="p-4">
                            <div class="font-bold text-white">{{ $inv->renter->name ?? 'Unknown Renter' }}</div>
                            <div class="text-xs text-slate-400">Room {{ $inv->room->room_number ?? 'N/A' }} ({{ $inv->room->floor->name ?? 'Floor' }})</div>
                        </td>

                        <td class="p-4">
                            <div class="font-extrabold text-emerald-400">₹{{ number_format($inv->amount, 2) }}</div>
                        </td>

                        <td class="p-4 text-xs text-slate-300">
                            {{ $inv->due_date->format('d M Y') }}
                        </td>

                        <td class="p-4">
                            @if($inv->status === 'paid')
                            <span class="px-2.5 py-1 rounded-full text-[10px] font-extrabold uppercase bg-emerald-500/20 text-emerald-300 border border-emerald-500/30">Paid</span>
                            @elseif($inv->status === 'delay_requested')
                            <span class="px-2.5 py-1 rounded-full text-[10px] font-extrabold uppercase bg-indigo-500/20 text-indigo-300 border border-indigo-500/30">Delay Requested</span>
                            @elseif($inv->status === 'pending')
                                @if($inv->payment && $inv->payment->status === 'pending_approval')
                                <span class="px-2.5 py-1 rounded-full text-[10px] font-extrabold uppercase bg-amber-500/20 text-amber-300 border border-amber-500/30">Proof Under Review</span>
                                @else
                                <span class="px-2.5 py-1 rounded-full text-[10px] font-extrabold uppercase bg-amber-500/20 text-amber-300 border border-amber-500/30">Pending</span>
                                @endif
                            @else
                            <span class="px-2.5 py-1 rounded-full text-[10px] font-extrabold uppercase bg-rose-500/20 text-rose-300 border border-rose-500/30">Overdue</span>
                            @endif
                        </td>

                        <td class="p-4 text-right">
                            <a href="{{ route('renter.receipt', $inv) }}" target="_blank" class="px-3 py-1.5 rounded-lg bg-slate-800 hover:bg-slate-700 text-slate-200 text-xs font-semibold border border-slate-700">View Receipt</a>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6" class="p-8 text-center text-slate-500">No rent invoices generated for this selection. Click "Generate Bills" to create monthly invoices.</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="p-4 border-t border-slate-800">
            {{ $invoices->links() }}
        </div>
    </div>
</div>
@endsection
