@extends('layouts.app')

@section('title', 'Repairing & Operational Expenses - UrbanStay PG')

@section('content')
<div class="space-y-8">
    <!-- Header -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div>
            <h1 class="text-3xl font-extrabold text-white tracking-tight">Repair & Operational Expenses</h1>
            <p class="text-sm text-slate-400">Log PG maintenance, utility bills, repairs, & upload bill receipts</p>
        </div>

        <div class="flex items-center space-x-3">
            <a href="{{ route('admin.expenses.profit-loss') }}" class="px-4 py-2.5 rounded-xl bg-emerald-600 hover:bg-emerald-500 text-white text-sm font-semibold shadow-lg shadow-emerald-500/20 transition-all flex items-center space-x-2">
                <i data-lucide="trending-up" class="w-4 h-4"></i>
                <span>Profit & Loss Statement</span>
            </a>
            <button onclick="toggleModal('addExpenseModal')" class="px-4 py-2.5 rounded-xl bg-blue-600 hover:bg-blue-500 text-white text-sm font-semibold shadow-lg shadow-blue-500/20 transition-all flex items-center space-x-2">
                <i data-lucide="plus-circle" class="w-4 h-4"></i>
                <span>Log New Expense</span>
            </button>
        </div>
    </div>

    <!-- Filters Bar -->
    <div class="glass-panel p-4 rounded-2xl border border-slate-800">
        <form method="GET" action="{{ route('admin.expenses.index') }}" class="grid grid-cols-1 sm:grid-cols-3 gap-4">
            <div>
                <select name="category" class="w-full px-4 py-2 rounded-xl bg-slate-900 border border-slate-700 text-white text-xs">
                    <option value="">Filter Category (All)</option>
                    @foreach($categories as $key => $label)
                    <option value="{{ $key }}" {{ request('category') === $key ? 'selected' : '' }}>{{ $label }}</option>
                    @endforeach
                </select>
            </div>

            <div>
                <input type="month" name="month" value="{{ request('month') }}" class="w-full px-4 py-2 rounded-xl bg-slate-900 border border-slate-700 text-white text-xs">
            </div>

            <div class="flex items-center space-x-2">
                <button type="submit" class="w-full py-2 px-3 rounded-xl bg-slate-800 hover:bg-slate-700 text-slate-200 text-xs font-semibold border border-slate-700">Filter</button>
                <a href="{{ route('admin.expenses.index') }}" class="py-2 px-3 rounded-xl bg-slate-900 text-slate-400 hover:text-white text-xs">Reset</a>
            </div>
        </form>
    </div>

    <!-- Expenses Table -->
    <div class="glass-panel rounded-3xl border border-slate-800 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left text-sm">
                <thead class="bg-slate-900/80 text-slate-400 border-b border-slate-800 text-xs uppercase tracking-wider">
                    <tr>
                        <th class="p-4">Date</th>
                        <th class="p-4">Title / Item</th>
                        <th class="p-4">Category</th>
                        <th class="p-4">Amount</th>
                        <th class="p-4">Receipt Bill</th>
                        <th class="p-4 text-right">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-800/60">
                    @forelse($expenses as $exp)
                    <tr class="hover:bg-slate-800/30">
                        <td class="p-4 text-xs font-semibold text-slate-300">
                            {{ $exp->expense_date->format('d M Y') }}
                        </td>

                        <td class="p-4">
                            <div class="font-bold text-white">{{ $exp->title }}</div>
                            @if($exp->notes)<div class="text-xs text-slate-400 font-sans">{{ $exp->notes }}</div>@endif
                        </td>

                        <td class="p-4">
                            <span class="px-2.5 py-1 rounded-full text-[10px] font-bold bg-slate-800 border border-slate-700 text-slate-300">
                                {{ $exp->category_label }}
                            </span>
                        </td>

                        <td class="p-4 font-extrabold text-rose-400">
                            ₹{{ number_format($exp->amount, 2) }}
                        </td>

                        <td class="p-4">
                            @if($exp->receipt_path)
                            <button onclick="openImageModal('{{ asset('storage/' . $exp->receipt_path) }}')" class="text-xs text-blue-400 hover:underline flex items-center space-x-1">
                                <i data-lucide="file-text" class="w-3.5 h-3.5"></i>
                                <span>View Receipt</span>
                            </button>
                            @else
                            <span class="text-xs text-slate-500 italic">No receipt attached</span>
                            @endif
                        </td>

                        <td class="p-4 text-right">
                            <form action="{{ route('admin.expenses.destroy', $exp) }}" method="POST" onsubmit="return confirm('Delete expense record for {{ $exp->title }}?')">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="p-2 rounded-xl text-slate-400 hover:text-rose-400 hover:bg-rose-500/10">
                                    <i data-lucide="trash-2" class="w-4 h-4"></i>
                                </button>
                            </form>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6" class="p-8 text-center text-slate-500">No expenses logged matching selection.</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="p-4 border-t border-slate-800">
            {{ $expenses->links() }}
        </div>
    </div>
</div>

<!-- Modal: Log Expense -->
<div id="addExpenseModal" class="fixed inset-0 z-50 hidden bg-black/80 backdrop-blur-sm flex items-center justify-center p-4">
    <div class="glass-panel p-6 rounded-3xl border border-slate-800 max-w-md w-full space-y-4">
        <div class="flex items-center justify-between">
            <h3 class="text-lg font-bold text-white">Log PG Expense / Repair</h3>
            <button onclick="toggleModal('addExpenseModal')" class="text-slate-400 hover:text-white"><i data-lucide="x" class="w-5 h-5"></i></button>
        </div>

        <form action="{{ route('admin.expenses.store') }}" method="POST" enctype="multipart/form-data" class="space-y-4">
            @csrf
            <div>
                <label class="block text-xs font-semibold uppercase text-slate-400 mb-1">Expense Category *</label>
                <select name="category" required class="w-full px-4 py-2.5 rounded-xl bg-slate-900 border border-slate-700 text-white text-sm">
                    @foreach($categories as $key => $label)
                    <option value="{{ $key }}">{{ $label }}</option>
                    @endforeach
                </select>
            </div>

            <div>
                <label class="block text-xs font-semibold uppercase text-slate-400 mb-1">Title / Expense Description *</label>
                <input type="text" name="title" required placeholder="e.g. Geyser Repair in Room 101" class="w-full px-4 py-2.5 rounded-xl bg-slate-900 border border-slate-700 text-white text-sm">
            </div>

            <div class="grid grid-cols-2 gap-3">
                <div>
                    <label class="block text-xs font-semibold uppercase text-slate-400 mb-1">Amount (₹) *</label>
                    <input type="number" step="0.01" name="amount" required placeholder="2500" class="w-full px-4 py-2.5 rounded-xl bg-slate-900 border border-slate-700 text-white text-sm">
                </div>

                <div>
                    <label class="block text-xs font-semibold uppercase text-slate-400 mb-1">Expense Date *</label>
                    <input type="date" name="expense_date" required value="{{ date('Y-m-d') }}" class="w-full px-4 py-2.5 rounded-xl bg-slate-900 border border-slate-700 text-white text-sm">
                </div>
            </div>

            <div>
                <label class="block text-xs font-semibold uppercase text-slate-400 mb-1">Upload Receipt Photo (Optional)</label>
                <input type="file" name="receipt" accept="image/*,application/pdf" class="w-full px-4 py-2 rounded-xl bg-slate-900 border border-slate-700 text-slate-300 text-xs">
            </div>

            <div>
                <label class="block text-xs font-semibold uppercase text-slate-400 mb-1">Notes / Voucher Details</label>
                <textarea name="notes" placeholder="Replaced plumbing pipe, paid cash..." class="w-full px-4 py-2.5 rounded-xl bg-slate-900 border border-slate-700 text-white text-sm h-16"></textarea>
            </div>

            <button type="submit" class="w-full py-3 rounded-xl bg-blue-600 hover:bg-blue-500 text-white font-semibold text-sm">Save Expense</button>
        </form>
    </div>
</div>

<script>
    function toggleModal(id) {
        document.getElementById(id).classList.toggle('hidden');
    }
</script>
@endsection
