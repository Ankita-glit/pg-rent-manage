@extends('layouts.app')

@section('title', 'Rent Delay Requests - UrbanStay PG')

@section('content')
<div class="space-y-6">
    <!-- Header -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div>
            <h1 class="text-3xl font-extrabold text-white tracking-tight">Rent Delay Requests</h1>
            <p class="text-sm text-slate-400">Renter requests for late rent payment extensions & reason approvals</p>
        </div>

        <div class="flex items-center space-x-2">
            <a href="{{ route('admin.delay-requests.index', ['status' => 'pending']) }}" class="px-3 py-2 rounded-xl text-xs font-semibold {{ request('status', 'pending') === 'pending' ? 'bg-rose-500/20 text-rose-300 border border-rose-500/40' : 'bg-slate-800 text-slate-400' }}">
                Pending ({{ $pendingCount }})
            </a>
            <a href="{{ route('admin.delay-requests.index', ['status' => 'approved']) }}" class="px-3 py-2 rounded-xl text-xs font-semibold {{ request('status') === 'approved' ? 'bg-emerald-500/20 text-emerald-300 border border-emerald-500/40' : 'bg-slate-800 text-slate-400' }}">
                Approved
            </a>
            <a href="{{ route('admin.delay-requests.index', ['status' => 'rejected']) }}" class="px-3 py-2 rounded-xl text-xs font-semibold {{ request('status') === 'rejected' ? 'bg-slate-700 text-slate-300' : 'bg-slate-800 text-slate-400' }}">
                Rejected
            </a>
        </div>
    </div>

    <!-- Delay Requests Cards List -->
    <div class="space-y-6">
        @forelse($delayRequests as $req)
        <div class="glass-panel p-6 rounded-3xl border border-slate-800 space-y-4">
            <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 border-b border-slate-800 pb-4">
                <div class="flex items-center space-x-3">
                    <div class="w-10 h-10 rounded-xl bg-gradient-to-tr from-rose-600 to-amber-600 flex items-center justify-center text-white font-bold">
                        <i data-lucide="clock" class="w-5 h-5"></i>
                    </div>
                    <div>
                        <h3 class="text-lg font-bold text-white">{{ $req->renter->name ?? 'Unknown Renter' }}</h3>
                        <p class="text-xs text-slate-400">
                            Room {{ $req->renter->room->room_number ?? 'N/A' }} ({{ $req->renter->room->floor->name ?? 'Floor' }}) • Phone: {{ $req->renter->phone ?? 'N/A' }}
                        </p>
                    </div>
                </div>

                <div class="flex items-center space-x-3 text-xs">
                    <div class="p-2.5 rounded-xl bg-slate-900 border border-slate-800 text-right">
                        <span class="text-slate-500 block text-[10px]">Requested Extension Date</span>
                        <span class="font-extrabold text-amber-300 text-sm">{{ $req->requested_date->format('d M Y') }}</span>
                    </div>

                    <span class="px-3 py-1 rounded-full text-xs font-bold uppercase {{ $req->status === 'approved' ? 'bg-emerald-500/20 text-emerald-300 border border-emerald-500/40' : ($req->status === 'pending' ? 'bg-amber-500/20 text-amber-300 border border-amber-500/40' : 'bg-rose-500/20 text-rose-300 border border-rose-500/40') }}">
                        {{ $req->status }}
                    </span>
                </div>
            </div>

            <!-- Reason Box -->
            <div class="p-4 rounded-2xl bg-slate-900/80 border border-slate-800 space-y-2">
                <span class="text-xs font-bold uppercase text-slate-400">Renter's Stated Reason:</span>
                <p class="text-sm text-slate-200 leading-relaxed font-sans">"{{ $req->reason }}"</p>
            </div>

            @if($req->admin_comment)
            <div class="p-3 rounded-xl bg-blue-500/10 border border-blue-500/30 text-blue-300 text-xs">
                <strong>Admin Decision Comment:</strong> {{ $req->admin_comment }}
            </div>
            @endif

            <!-- Admin Actions -->
            @if($req->status === 'pending')
            <div class="pt-2 flex items-center justify-end space-x-3">
                <button onclick="openApproveDelayModal({{ $req->id }}, '{{ $req->requested_date->format('Y-m-d') }}')" class="px-5 py-2.5 rounded-xl bg-emerald-600 hover:bg-emerald-500 text-white font-bold text-xs shadow-lg shadow-emerald-500/20 flex items-center space-x-1.5">
                    <i data-lucide="check" class="w-4 h-4"></i>
                    <span>Approve Delay Extension</span>
                </button>

                <button onclick="openRejectDelayModal({{ $req->id }})" class="px-5 py-2.5 rounded-xl bg-rose-600/20 hover:bg-rose-600/30 text-rose-300 border border-rose-500/40 font-bold text-xs flex items-center space-x-1.5">
                    <i data-lucide="x" class="w-4 h-4"></i>
                    <span>Reject Request</span>
                </button>
            </div>
            @endif
        </div>
        @empty
        <div class="glass-panel p-12 text-center text-slate-500 rounded-3xl">
            No rent payment delay requests found under this filter.
        </div>
        @endforelse

        <div class="p-4">
            {{ $delayRequests->links() }}
        </div>
    </div>
</div>

<!-- Approve Delay Modal -->
<div id="approveDelayModal" class="fixed inset-0 z-50 hidden bg-black/80 backdrop-blur-sm flex items-center justify-center p-4">
    <div class="glass-panel p-6 rounded-3xl border border-slate-800 max-w-md w-full space-y-4">
        <div class="flex items-center justify-between">
            <h3 class="text-lg font-bold text-white">Approve Rent Delay Extension</h3>
            <button onclick="toggleModal('approveDelayModal')" class="text-slate-400 hover:text-white"><i data-lucide="x" class="w-5 h-5"></i></button>
        </div>

        <form id="approveDelayForm" method="POST" class="space-y-4">
            @csrf
            <div>
                <label class="block text-xs font-semibold uppercase text-slate-400 mb-1">Admin Note / Comment (Optional)</label>
                <textarea name="admin_comment" placeholder="e.g. Approved as per phone discussion. Please pay by the 15th." class="w-full px-4 py-3 rounded-xl bg-slate-900 border border-slate-700 text-white text-sm h-24"></textarea>
            </div>

            <button type="submit" class="w-full py-3 rounded-xl bg-emerald-600 hover:bg-emerald-500 text-white font-semibold text-sm">Approve Extension</button>
        </form>
    </div>
</div>

<!-- Reject Delay Modal -->
<div id="rejectDelayModal" class="fixed inset-0 z-50 hidden bg-black/80 backdrop-blur-sm flex items-center justify-center p-4">
    <div class="glass-panel p-6 rounded-3xl border border-slate-800 max-w-md w-full space-y-4">
        <div class="flex items-center justify-between">
            <h3 class="text-lg font-bold text-white">Reject Delay Request</h3>
            <button onclick="toggleModal('rejectDelayModal')" class="text-slate-400 hover:text-white"><i data-lucide="x" class="w-5 h-5"></i></button>
        </div>

        <form id="rejectDelayForm" method="POST" class="space-y-4">
            @csrf
            <div>
                <label class="block text-xs font-semibold uppercase text-slate-400 mb-1">Reason for Rejection *</label>
                <textarea name="admin_comment" required placeholder="e.g. Payment due date cannot be extended past the 7th of the month." class="w-full px-4 py-3 rounded-xl bg-slate-900 border border-slate-700 text-white text-sm h-24"></textarea>
            </div>

            <button type="submit" class="w-full py-3 rounded-xl bg-rose-600 hover:bg-rose-500 text-white font-semibold text-sm">Confirm Rejection</button>
        </form>
    </div>
</div>

<script>
    function toggleModal(id) {
        document.getElementById(id).classList.toggle('hidden');
    }

    function openApproveDelayModal(reqId) {
        document.getElementById('approveDelayForm').action = '/admin/delay-requests/' + reqId + '/approve';
        toggleModal('approveDelayModal');
    }

    function openRejectDelayModal(reqId) {
        document.getElementById('rejectDelayForm').action = '/admin/delay-requests/' + reqId + '/reject';
        toggleModal('rejectDelayModal');
    }
</script>
@endsection
