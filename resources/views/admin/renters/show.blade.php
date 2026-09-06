@extends('layouts.app')

@section('title', $renter->name . ' - Renter Profile')

@section('content')
<div class="space-y-8 max-w-5xl mx-auto">
    <!-- Header -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div class="flex items-center space-x-4">
            <div class="w-16 h-16 rounded-2xl bg-gradient-to-tr from-pink-600 via-rose-500 to-indigo-600 text-white font-bold flex items-center justify-center text-2xl shadow-lg">
                {{ strtoupper(substr($renter->name, 0, 2)) }}
            </div>
            <div>
                <h1 class="text-3xl font-extrabold text-white tracking-tight">{{ $renter->name }}</h1>
                <div class="flex items-center space-x-2 mt-1">
                    <span class="px-3 py-1 rounded-full text-xs font-bold bg-pink-500/20 text-pink-300 border border-pink-500/30 flex items-center space-x-1.5">
                        <i data-lucide="clock" class="w-3.5 h-3.5"></i>
                        <span>Living Duration: {{ $renter->tenure_formatted }}</span>
                    </span>
                </div>
            </div>
        </div>

        <div class="flex items-center space-x-3">
            <a href="{{ route('admin.renters.edit', $renter) }}" class="px-4 py-2.5 rounded-xl bg-slate-800 hover:bg-slate-700 text-slate-200 text-xs font-semibold border border-slate-700 transition-all flex items-center space-x-2">
                <i data-lucide="edit" class="w-4 h-4"></i>
                <span>Edit Profile</span>
            </a>
            <a href="{{ route('admin.renters.index') }}" class="px-4 py-2.5 rounded-xl bg-slate-900 text-slate-400 hover:text-white text-xs font-semibold">Back to Directory</a>
        </div>
    </div>

    <!-- Info Cards Grid -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
        <!-- Room Allocation -->
        <div class="glass-panel p-6 rounded-3xl border border-slate-800 space-y-4">
            <div class="flex items-center space-x-3 text-pink-400">
                <i data-lucide="building-2" class="w-5 h-5"></i>
                <h3 class="text-xs font-bold uppercase tracking-wider text-slate-300">Room Allocation</h3>
            </div>
            @if($renter->room)
            <div>
                <p class="text-2xl font-bold text-white">Room {{ $renter->room->room_number }}</p>
                <p class="text-xs text-slate-400">{{ $renter->room->floor->name ?? 'Floor' }}</p>
                <p class="text-xs font-semibold text-pink-300 mt-2">Seat: {{ $renter->bed_number ?? 'Bed assigned' }}</p>
            </div>
            @else
            <p class="text-sm text-rose-400 font-semibold">No room currently assigned</p>
            @endif
        </div>

        <!-- Rent Terms -->
        <div class="glass-panel p-6 rounded-3xl border border-slate-800 space-y-4">
            <div class="flex items-center space-x-3 text-emerald-400">
                <i data-lucide="wallet" class="w-5 h-5"></i>
                <h3 class="text-xs font-bold uppercase tracking-wider text-slate-300">Rent Terms</h3>
            </div>
            <div>
                <p class="text-2xl font-bold text-emerald-400">₹{{ number_format($renter->monthly_rent, 2) }} <span class="text-xs text-slate-400 font-normal">/mo</span></p>
                <p class="text-xs text-slate-400 mt-1">Security Deposit: ₹{{ number_format($renter->security_deposit, 2) }}</p>
                <p class="text-xs text-slate-400">Due Day: {{ $renter->rent_due_day }}th of every month</p>
            </div>
        </div>

        <!-- Govt ID Proof -->
        <div class="glass-panel p-6 rounded-3xl border border-slate-800 space-y-4">
            <div class="flex items-center space-x-3 text-blue-400">
                <i data-lucide="shield-check" class="w-5 h-5"></i>
                <h3 class="text-xs font-bold uppercase tracking-wider text-slate-300">Identity Verification</h3>
            </div>
            <div>
                <p class="text-sm font-bold text-white">{{ $renter->id_type }}</p>
                <p class="text-xs text-slate-400 font-mono">{{ $renter->id_number }}</p>
                @if($renter->id_proof_path)
                <button onclick="openImageModal('{{ asset('storage/' . $renter->id_proof_path) }}')" class="mt-3 px-3 py-1.5 rounded-lg bg-blue-600/20 text-blue-300 border border-blue-500/30 text-xs font-semibold flex items-center space-x-1">
                    <i data-lucide="file-text" class="w-3.5 h-3.5"></i>
                    <span>Preview Uploaded ID Document</span>
                </button>
                @endif
            </div>
        </div>
    </div>

    <!-- Extended Basic & Family Contact Details Card -->
    <div class="glass-panel p-6 rounded-3xl border border-slate-800 space-y-4">
        <h3 class="text-xs font-bold uppercase tracking-wider text-slate-400">Personal, Family & Emergency Details</h3>
        
        <div class="grid grid-cols-1 md:grid-cols-3 gap-4 text-xs">
            <div class="p-3.5 rounded-2xl bg-slate-900 border border-slate-800">
                <span class="text-slate-500 block mb-1">Father's Name</span>
                <span class="font-bold text-white text-sm">{{ $renter->father_name ?? 'Not Provided' }}</span>
            </div>

            <div class="p-3.5 rounded-2xl bg-slate-900 border border-slate-800">
                <span class="text-slate-500 block mb-1">Mother's Name</span>
                <span class="font-bold text-white text-sm">{{ $renter->mother_name ?? 'Not Provided' }}</span>
            </div>

            <div class="p-3.5 rounded-2xl bg-slate-900 border border-slate-800">
                <span class="text-slate-500 block mb-1">Emergency Contact</span>
                <span class="font-bold text-rose-300 text-sm">{{ $renter->emergency_contact ?? 'Not Provided' }}</span>
            </div>

            <div class="p-3.5 rounded-2xl bg-slate-900 border border-slate-800">
                <span class="text-slate-500 block mb-1">Renter Email</span>
                <span class="font-semibold text-white">{{ $renter->email }}</span>
            </div>

            <div class="p-3.5 rounded-2xl bg-slate-900 border border-slate-800">
                <span class="text-slate-500 block mb-1">Renter Phone</span>
                <span class="font-semibold text-white">{{ $renter->phone }}</span>
            </div>

            <div class="p-3.5 rounded-2xl bg-slate-900 border border-slate-800">
                <span class="text-slate-500 block mb-1">Blood Group</span>
                <span class="font-bold text-pink-400">{{ $renter->blood_group ?? 'N/A' }}</span>
            </div>

            <div class="p-3.5 rounded-2xl bg-slate-900 border border-slate-800 md:col-span-2">
                <span class="text-slate-500 block mb-1">Permanent Home Address</span>
                <span class="font-medium text-slate-200">{{ $renter->permanent_address ?? 'Not Provided' }}</span>
            </div>

            <div class="p-3.5 rounded-2xl bg-slate-900 border border-slate-800">
                <span class="text-slate-500 block mb-1">College / Office Name</span>
                <span class="font-semibold text-indigo-300">{{ $renter->college_office_name ?? 'N/A' }}</span>
            </div>
        </div>
    </div>

    <!-- Rent Invoice History -->
    <div class="glass-panel p-6 rounded-3xl border border-slate-800 space-y-4">
        <h3 class="text-xs font-bold uppercase tracking-wider text-slate-400">Rent Payment History</h3>
        
        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs">
                <thead class="text-slate-400 border-b border-slate-800 uppercase tracking-wider">
                    <tr>
                        <th class="pb-3">Month</th>
                        <th class="pb-3">Amount</th>
                        <th class="pb-3">Due Date</th>
                        <th class="pb-3">Status</th>
                        <th class="pb-3">Payment Info</th>
                        <th class="pb-3 text-right">Receipt</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-800/60">
                    @forelse($renter->invoices as $inv)
                    <tr class="hover:bg-slate-800/30">
                        <td class="py-3 font-bold text-white">{{ $inv->month_year }}</td>
                        <td class="py-3 font-bold text-emerald-400">₹{{ number_format($inv->amount, 2) }}</td>
                        <td class="py-3 text-slate-300">{{ $inv->due_date->format('d M Y') }}</td>
                        <td class="py-3">
                            <span class="px-2 py-0.5 rounded-full text-[10px] font-bold uppercase {{ $inv->status === 'paid' ? 'bg-emerald-500/20 text-emerald-300' : ($inv->status === 'pending' ? 'bg-amber-500/20 text-amber-300' : 'bg-rose-500/20 text-rose-300') }}">
                                {{ $inv->status }}
                            </span>
                        </td>
                        <td class="py-3 text-slate-400">
                            @if($inv->payment)
                            <span>{{ ucfirst($inv->payment->payment_method) }} • {{ $inv->payment->transaction_id ?? $inv->payment->cash_receiver_name }}</span>
                            @else
                            <span>-</span>
                            @endif
                        </td>
                        <td class="py-3 text-right">
                            <a href="{{ route('renter.receipt', $inv) }}" target="_blank" class="text-pink-400 hover:underline font-semibold">Receipt</a>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6" class="py-4 text-center text-slate-500">No invoices generated for this renter yet.</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
