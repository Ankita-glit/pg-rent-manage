@extends('layouts.app')

@section('title', 'Rent Payment Verification Hub - UrbanStay PG')

@section('content')
<div class="space-y-6">
    <!-- Header -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div>
            <h1 class="text-3xl font-extrabold text-white tracking-tight">Payment Verification Hub</h1>
            <p class="text-sm text-slate-400">Review online payment screenshots & cash handover submissions from renters</p>
        </div>

        <div class="flex items-center space-x-2">
            <a href="{{ route('admin.payments.index', ['status' => 'pending_approval']) }}" class="px-3 py-2 rounded-xl text-xs font-semibold {{ request('status', 'pending_approval') === 'pending_approval' ? 'bg-amber-500/20 text-amber-300 border border-amber-500/40' : 'bg-slate-800 text-slate-400' }}">
                Pending Review ({{ $pendingCount }})
            </a>
            <a href="{{ route('admin.payments.index', ['status' => 'approved']) }}" class="px-3 py-2 rounded-xl text-xs font-semibold {{ request('status') === 'approved' ? 'bg-emerald-500/20 text-emerald-300 border border-emerald-500/40' : 'bg-slate-800 text-slate-400' }}">
                Approved
            </a>
            <a href="{{ route('admin.payments.index', ['status' => 'rejected']) }}" class="px-3 py-2 rounded-xl text-xs font-semibold {{ request('status') === 'rejected' ? 'bg-rose-500/20 text-rose-300 border border-rose-500/40' : 'bg-slate-800 text-slate-400' }}">
                Rejected
            </a>
        </div>
    </div>

    <!-- Payment Cards Grid -->
    <div class="space-y-6">
        @forelse($payments as $payment)
        <div class="glass-panel p-6 rounded-3xl border border-slate-800 flex flex-col lg:flex-row lg:items-center justify-between gap-6">
            <div class="space-y-3 flex-1">
                <div class="flex items-center space-x-3">
                    <span class="px-3 py-1 rounded-full text-xs font-extrabold uppercase {{ $payment->payment_method === 'online' ? 'bg-blue-500/20 text-blue-300 border border-blue-500/40' : 'bg-purple-500/20 text-purple-300 border border-purple-500/40' }}">
                        {{ $payment->payment_method === 'online' ? '📱 Online Payment Screenshot' : '💵 Cash Payment Handover' }}
                    </span>
                    <span class="text-xs text-slate-400">Month: <strong class="text-white">{{ $payment->invoice->month_year ?? 'N/A' }}</strong></span>
                </div>

                <div>
                    <h3 class="text-xl font-bold text-white">{{ $payment->invoice->renter->name ?? 'Unknown Renter' }}</h3>
                    <p class="text-xs text-slate-400">
                        Room {{ $payment->invoice->room->room_number ?? 'N/A' }} ({{ $payment->invoice->room->floor->name ?? 'Floor' }}) • Phone: {{ $payment->invoice->renter->phone ?? 'N/A' }}
                    </p>
                </div>

                <!-- Payment Method Specific Details -->
                <div class="p-4 rounded-2xl bg-slate-900/90 border border-slate-800 grid grid-cols-1 sm:grid-cols-3 gap-4 text-xs">
                    <div>
                        <span class="text-slate-500 block mb-1">Amount Submitted</span>
                        <span class="text-lg font-extrabold text-emerald-400">₹{{ number_format($payment->amount, 2) }}</span>
                    </div>

                    <div>
                        <span class="text-slate-500 block mb-1">Date & Time</span>
                        <span class="font-semibold text-slate-200">{{ $payment->payment_date->format('d M Y') }} @ {{ $payment->payment_time ?? 'N/A' }}</span>
                    </div>

                    <div>
                        @if($payment->payment_method === 'online')
                        <span class="text-slate-500 block mb-1">Transaction Ref ID</span>
                        <span class="font-mono text-indigo-300 font-bold">{{ $payment->transaction_id ?? 'N/A' }}</span>
                        @else
                        <span class="text-slate-500 block mb-1">Cash Given To (Staff/Admin)</span>
                        <span class="font-semibold text-purple-300">{{ $payment->cash_receiver_name ?? 'N/A' }}</span>
                        @endif
                    </div>
                </div>

                @if($payment->notes)
                <p class="text-xs text-slate-400 italic">Renter Note: "{{ $payment->notes }}"</p>
                @endif

                @if($payment->rejection_reason)
                <div class="p-3 rounded-xl bg-rose-500/10 border border-rose-500/30 text-rose-300 text-xs">
                    <strong>Rejection Reason:</strong> {{ $payment->rejection_reason }}
                </div>
                @endif
            </div>

            <!-- Proof Screenshot Preview & Action Buttons -->
            <div class="flex flex-col items-center lg:items-end space-y-4 shrink-0 border-t lg:border-t-0 lg:border-l border-slate-800 pt-4 lg:pt-0 lg:pl-6">
                @if($payment->screenshot_path)
                <button onclick="openImageModal('{{ asset('storage/' . $payment->screenshot_path) }}')" class="group relative rounded-2xl overflow-hidden border border-slate-700 w-36 h-28 shadow-lg">
                    <img src="{{ asset('storage/' . $payment->screenshot_path) }}" alt="Screenshot Proof" class="w-full h-full object-cover group-hover:scale-105 transition-transform">
                    <div class="absolute inset-0 bg-black/50 group-hover:bg-black/30 flex items-center justify-center text-white text-xs font-semibold transition-colors">
                        <i data-lucide="zoom-in" class="w-4 h-4 mr-1"></i> View Proof
                    </div>
                </button>
                @endif

                @if($payment->status === 'pending_approval')
                <div class="flex items-center space-x-2 w-full lg:w-auto">
                    <form action="{{ route('admin.payments.approve', $payment) }}" method="POST">
                        @csrf
                        <button type="submit" class="px-4 py-2.5 rounded-xl bg-emerald-600 hover:bg-emerald-500 text-white font-bold text-xs shadow-lg shadow-emerald-500/20 flex items-center space-x-1.5">
                            <i data-lucide="check-circle" class="w-4 h-4"></i>
                            <span>Approve Payment</span>
                        </button>
                    </form>

                    <button onclick="openRejectModal({{ $payment->id }})" class="px-4 py-2.5 rounded-xl bg-rose-600/20 hover:bg-rose-600/30 text-rose-300 border border-rose-500/40 font-bold text-xs flex items-center space-x-1.5">
                        <i data-lucide="x-circle" class="w-4 h-4"></i>
                        <span>Reject</span>
                    </button>
                </div>
                @else
                <span class="px-3 py-1 rounded-full text-xs font-bold uppercase {{ $payment->status === 'approved' ? 'bg-emerald-500/20 text-emerald-300' : 'bg-rose-500/20 text-rose-300' }}">
                    Status: {{ $payment->status }}
                </span>
                @endif
            </div>
        </div>
        @empty
        <div class="glass-panel p-12 text-center text-slate-500 rounded-3xl">
            No rent payment submissions found under this filter.
        </div>
        @endforelse

        <div class="p-4">
            {{ $payments->links() }}
        </div>
    </div>
</div>

<!-- Reject Payment Modal -->
<div id="rejectModal" class="fixed inset-0 z-50 hidden bg-black/80 backdrop-blur-sm flex items-center justify-center p-4">
    <div class="glass-panel p-6 rounded-3xl border border-slate-800 max-w-md w-full space-y-4">
        <div class="flex items-center justify-between">
            <h3 class="text-lg font-bold text-white">Reject Payment Proof</h3>
            <button onclick="toggleModal('rejectModal')" class="text-slate-400 hover:text-white"><i data-lucide="x" class="w-5 h-5"></i></button>
        </div>

        <form id="rejectForm" method="POST" class="space-y-4">
            @csrf
            <div>
                <label class="block text-xs font-semibold uppercase text-slate-400 mb-1">Reason for Rejection *</label>
                <textarea name="rejection_reason" required placeholder="e.g. Screenshot blurry / Transaction ID not found on bank statement / Invalid amount" class="w-full px-4 py-3 rounded-xl bg-slate-900 border border-slate-700 text-white text-sm h-28"></textarea>
            </div>

            <button type="submit" class="w-full py-3 rounded-xl bg-rose-600 hover:bg-rose-500 text-white font-semibold text-sm">Confirm Rejection</button>
        </form>
    </div>
</div>

<script>
    function toggleModal(id) {
        document.getElementById(id).classList.toggle('hidden');
    }

    function openRejectModal(paymentId) {
        document.getElementById('rejectForm').action = '/admin/payments/' + paymentId + '/reject';
        toggleModal('rejectModal');
    }
</script>
@endsection
