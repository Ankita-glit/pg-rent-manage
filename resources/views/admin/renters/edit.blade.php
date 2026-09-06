@extends('layouts.app')

@section('title', 'Edit Renter Profile - ' . $renter->name)

@section('content')
<div class="max-w-4xl mx-auto space-y-8">
    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-3xl font-extrabold text-white tracking-tight">Edit Renter Profile</h1>
            <p class="text-sm text-slate-400">Update contact info, room assignment, or stay details for {{ $renter->name }}</p>
        </div>
        <a href="{{ route('admin.renters.show', $renter) }}" class="px-4 py-2.5 rounded-xl bg-slate-800 hover:bg-slate-700 text-slate-300 text-xs font-bold border border-slate-700">Cancel & Back</a>
    </div>

    <form action="{{ route('admin.renters.update', $renter) }}" method="POST" enctype="multipart/form-data" class="glass-panel p-8 rounded-3xl border border-slate-800 space-y-8">
        @csrf
        @method('PUT')

        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <div>
                <label class="block text-xs font-semibold uppercase text-slate-400 mb-1">Full Name *</label>
                <input type="text" name="name" required value="{{ old('name', $renter->name) }}" class="w-full px-4 py-3 rounded-xl bg-slate-900 border border-slate-700 text-white text-sm">
            </div>

            <div>
                <label class="block text-xs font-semibold uppercase text-slate-400 mb-1">Email Address *</label>
                <input type="email" name="email" required value="{{ old('email', $renter->email) }}" class="w-full px-4 py-3 rounded-xl bg-slate-900 border border-slate-700 text-white text-sm">
            </div>

            <div>
                <label class="block text-xs font-semibold uppercase text-slate-400 mb-1">New Password (Leave blank to keep unchanged)</label>
                <div class="relative">
                    <input id="edit_password" type="password" name="password" placeholder="••••••••" class="w-full pl-4 pr-12 py-3 rounded-xl bg-slate-900 border border-slate-700 text-white text-sm">
                    <button type="button" onclick="togglePasswordVisibility('edit_password', 'eyeIconEdit')" class="absolute inset-y-0 right-0 pr-3.5 flex items-center text-slate-400 hover:text-white" title="Toggle password visibility">
                        <i id="eyeIconEdit" data-lucide="eye" class="w-4 h-4"></i>
                    </button>
                </div>
            </div>

            <div>
                <label class="block text-xs font-semibold uppercase text-slate-400 mb-1">Phone Number *</label>
                <input type="text" name="phone" required value="{{ old('phone', $renter->phone) }}" class="w-full px-4 py-3 rounded-xl bg-slate-900 border border-slate-700 text-white text-sm">
            </div>

            <div>
                <label class="block text-xs font-semibold uppercase text-slate-400 mb-1">Emergency Contact *</label>
                <input type="text" name="emergency_contact" required value="{{ old('emergency_contact', $renter->emergency_contact) }}" class="w-full px-4 py-3 rounded-xl bg-slate-900 border border-slate-700 text-white text-sm">
            </div>

            <div>
                <label class="block text-xs font-semibold uppercase text-slate-400 mb-1">Joining Date *</label>
                <input type="date" name="joining_date" required value="{{ old('joining_date', $renter->joining_date ? $renter->joining_date->format('Y-m-d') : '') }}" class="w-full px-4 py-3 rounded-xl bg-slate-900 border border-slate-700 text-white text-sm">
            </div>

            <div>
                <label class="block text-xs font-semibold uppercase text-slate-400 mb-1">Govt ID Type *</label>
                <select name="id_type" required class="w-full px-4 py-3 rounded-xl bg-slate-900 border border-slate-700 text-white text-sm">
                    @foreach(['Aadhaar Card', 'PAN Card', 'Passport', 'Driving License', 'Voter ID'] as $idType)
                    <option value="{{ $idType }}" {{ $renter->id_type === $idType ? 'selected' : '' }}>{{ $idType }}</option>
                    @endforeach
                </select>
            </div>

            <div>
                <label class="block text-xs font-semibold uppercase text-slate-400 mb-1">Govt ID Number *</label>
                <input type="text" name="id_number" required value="{{ old('id_number', $renter->id_number) }}" class="w-full px-4 py-3 rounded-xl bg-slate-900 border border-slate-700 text-white text-sm">
            </div>

            <div>
                <label class="block text-xs font-semibold uppercase text-slate-400 mb-1">Status</label>
                <select name="status" class="w-full px-4 py-3 rounded-xl bg-slate-900 border border-slate-700 text-white text-sm">
                    <option value="active" {{ $renter->status === 'active' ? 'selected' : '' }}>Active Renter</option>
                    <option value="inactive" {{ $renter->status === 'inactive' ? 'selected' : '' }}>Inactive / Vacated</option>
                </select>
            </div>

            <div>
                <label class="block text-xs font-semibold uppercase text-slate-400 mb-1">Upload New ID Proof Document</label>
                <input type="file" name="id_proof" accept="image/*,application/pdf" class="w-full px-4 py-2.5 rounded-xl bg-slate-900 border border-slate-700 text-slate-300 text-xs">
            </div>

            <div class="md:col-span-2">
                <div class="flex items-center justify-between mb-1">
                    <label class="block text-xs font-semibold uppercase text-slate-400">Assigned Room (Admin Override: Any Room Selectable) *</label>
                    <span class="text-[10px] text-indigo-300 font-semibold">Shows current occupants in each room</span>
                </div>
                <select name="assigned_room_id" required class="w-full px-4 py-3 rounded-xl bg-slate-900 border border-slate-700 text-white text-sm font-medium">
                    @foreach($rooms as $rm)
                    @php
                        $occupants = $rm->renters->reject(fn($r) => $r->id === $renter->id);
                        $occupantNames = $occupants->pluck('name')->implode(', ');
                    @endphp
                    <option value="{{ $rm->id }}" {{ $renter->assigned_room_id == $rm->id ? 'selected' : '' }}>
                        Room {{ $rm->room_number }} ({{ $rm->floor->name ?? 'Floor' }}) - ₹{{ number_format($rm->monthly_price, 0) }} {{ $occupantNames ? '• Current Occupants: ' . $occupantNames : '• Empty / Available' }}
                    </option>
                    @endforeach
                </select>
            </div>

            <div>
                <label class="block text-xs font-semibold uppercase text-slate-400 mb-1">Bed / Seat Number *</label>
                <input type="text" name="bed_number" required value="{{ old('bed_number', $renter->bed_number) }}" class="w-full px-4 py-3 rounded-xl bg-slate-900 border border-slate-700 text-white text-sm">
            </div>

            <div>
                <label class="block text-xs font-semibold uppercase text-slate-400 mb-1">Monthly Rent Amount (₹) *</label>
                <input type="number" step="0.01" name="monthly_rent" required value="{{ old('monthly_rent', $renter->monthly_rent) }}" class="w-full px-4 py-3 rounded-xl bg-slate-900 border border-slate-700 text-white text-sm font-bold">
            </div>

            <div>
                <label class="block text-xs font-semibold uppercase text-slate-400 mb-1">Security Deposit (₹) *</label>
                <input type="number" step="0.01" name="security_deposit" required value="{{ old('security_deposit', $renter->security_deposit) }}" class="w-full px-4 py-3 rounded-xl bg-slate-900 border border-slate-700 text-white text-sm">
            </div>

            <div>
                <label class="block text-xs font-semibold uppercase text-slate-400 mb-1">Rent Due Day *</label>
                <input type="number" min="1" max="28" name="rent_due_day" required value="{{ old('rent_due_day', $renter->rent_due_day) }}" class="w-full px-4 py-3 rounded-xl bg-slate-900 border border-slate-700 text-white text-sm">
            </div>
        </div>

        <div class="pt-6 border-t border-slate-800 flex justify-end space-x-4">
            <a href="{{ route('admin.renters.show', $renter) }}" class="px-6 py-3 rounded-xl bg-slate-800 hover:bg-slate-700 text-slate-300 font-semibold text-sm">Cancel</a>
            <button type="submit" class="px-8 py-3 rounded-xl bg-blue-600 hover:bg-blue-500 text-white font-semibold text-sm shadow-lg">Save Changes</button>
        </div>
    </form>
</div>

<script>
    function togglePasswordVisibility(inputId, iconId) {
        const input = document.getElementById(inputId);
        const icon = document.getElementById(iconId);
        if (input.type === 'password') {
            input.type = 'text';
            icon.setAttribute('data-lucide', 'eye-off');
        } else {
            input.type = 'password';
            icon.setAttribute('data-lucide', 'eye');
        }
        lucide.createIcons();
    }
</script>
@endsection
