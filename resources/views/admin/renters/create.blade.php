@extends('layouts.app')

@section('title', 'Onboard New Renter - Nice Stay Girls PG')

@section('content')
<div class="max-w-4xl mx-auto space-y-8">
    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-3xl font-extrabold text-white tracking-tight">Onboard New Resident</h1>
            <p class="text-sm text-slate-400">Register new PG tenant, record family contacts, assign floor/room & set rent terms</p>
        </div>
        <a href="{{ route('admin.renters.index') }}" class="px-4 py-2.5 rounded-xl bg-slate-800 hover:bg-slate-700 text-slate-300 text-xs font-bold border border-slate-700">Back to Directory</a>
    </div>

    <form action="{{ route('admin.renters.store') }}" method="POST" enctype="multipart/form-data" class="glass-panel p-8 rounded-3xl border border-slate-800 space-y-8">
        @csrf

        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <!-- Personal Info -->
            <div class="space-y-4 md:col-span-2">
                <h3 class="text-xs font-bold uppercase tracking-wider text-pink-400 border-b border-slate-800 pb-2">1. Resident & Family Contact Details</h3>
            </div>

            <div>
                <label class="block text-xs font-semibold uppercase text-slate-400 mb-1">Full Name *</label>
                <input type="text" name="name" required value="{{ old('name') }}" placeholder="e.g. Ananya Roy" class="w-full px-4 py-3 rounded-xl bg-slate-900 border border-slate-700 text-white text-sm">
            </div>

            <div>
                <label class="block text-xs font-semibold uppercase text-slate-400 mb-1">Father's Full Name *</label>
                <input type="text" name="father_name" required value="{{ old('father_name') }}" placeholder="e.g. Suresh Roy" class="w-full px-4 py-3 rounded-xl bg-slate-900 border border-slate-700 text-white text-sm font-bold">
            </div>

            <div>
                <label class="block text-xs font-semibold uppercase text-slate-400 mb-1">Mother's Name (Optional)</label>
                <input type="text" name="mother_name" value="{{ old('mother_name') }}" placeholder="e.g. Sunita Roy" class="w-full px-4 py-3 rounded-xl bg-slate-900 border border-slate-700 text-white text-sm">
            </div>

            <div>
                <label class="block text-xs font-semibold uppercase text-slate-400 mb-1">Email Address (Login Username) *</label>
                <input type="email" name="email" required value="{{ old('email') }}" placeholder="ananya@example.com" class="w-full px-4 py-3 rounded-xl bg-slate-900 border border-slate-700 text-white text-sm">
            </div>

            <div>
                <label class="block text-xs font-semibold uppercase text-slate-400 mb-1">Initial Login Password *</label>
                <div class="relative">
                    <input id="renter_password" type="password" name="password" required value="password123" class="w-full pl-4 pr-12 py-3 rounded-xl bg-slate-900 border border-slate-700 text-white text-sm font-mono">
                    <button type="button" onclick="togglePasswordVisibility('renter_password', 'eyeIconCreate')" class="absolute inset-y-0 right-0 pr-3.5 flex items-center text-slate-400 hover:text-white" title="Toggle password visibility">
                        <i id="eyeIconCreate" data-lucide="eye" class="w-4 h-4"></i>
                    </button>
                </div>
            </div>

            <div>
                <label class="block text-xs font-semibold uppercase text-slate-400 mb-1">Phone Number *</label>
                <input type="text" name="phone" required value="{{ old('phone') }}" placeholder="+91 98765 43210" class="w-full px-4 py-3 rounded-xl bg-slate-900 border border-slate-700 text-white text-sm">
            </div>

            <div>
                <label class="block text-xs font-semibold uppercase text-slate-400 mb-1">Emergency Contact Phone & Relation *</label>
                <input type="text" name="emergency_contact" required value="{{ old('emergency_contact') }}" placeholder="+91 98765 00000 (Father)" class="w-full px-4 py-3 rounded-xl bg-slate-900 border border-slate-700 text-white text-sm">
            </div>

            <div>
                <label class="block text-xs font-semibold uppercase text-slate-400 mb-1">Blood Group (Optional)</label>
                <input type="text" name="blood_group" value="{{ old('blood_group') }}" placeholder="e.g. B+, O+, AB-" class="w-full px-4 py-3 rounded-xl bg-slate-900 border border-slate-700 text-white text-sm">
            </div>

            <div>
                <label class="block text-xs font-semibold uppercase text-slate-400 mb-1">College / Office Name</label>
                <input type="text" name="college_office_name" value="{{ old('college_office_name') }}" placeholder="e.g. St. Xavier College / Tech Mahindra" class="w-full px-4 py-3 rounded-xl bg-slate-900 border border-slate-700 text-white text-sm">
            </div>

            <div class="md:col-span-2">
                <label class="block text-xs font-semibold uppercase text-slate-400 mb-1">Permanent Home Address *</label>
                <textarea name="permanent_address" required placeholder="House No, Street, Landmark, City, State, Pincode" class="w-full px-4 py-3 rounded-xl bg-slate-900 border border-slate-700 text-white text-sm h-20"></textarea>
            </div>

            <div>
                <label class="block text-xs font-semibold uppercase text-slate-400 mb-1">Joining Date *</label>
                <input type="date" name="joining_date" required value="{{ old('joining_date', date('Y-m-d')) }}" class="w-full px-4 py-3 rounded-xl bg-slate-900 border border-slate-700 text-white text-sm">
            </div>

            <!-- Identity Verification -->
            <div class="space-y-4 md:col-span-2 pt-4">
                <h3 class="text-xs font-bold uppercase tracking-wider text-pink-400 border-b border-slate-800 pb-2">2. Identity Proof Verification</h3>
            </div>

            <div>
                <label class="block text-xs font-semibold uppercase text-slate-400 mb-1">Govt ID Type *</label>
                <select name="id_type" required class="w-full px-4 py-3 rounded-xl bg-slate-900 border border-slate-700 text-white text-sm">
                    <option value="Aadhaar Card">Aadhaar Card</option>
                    <option value="PAN Card">PAN Card</option>
                    <option value="Passport">Passport</option>
                    <option value="Driving License">Driving License</option>
                    <option value="Voter ID">Voter ID</option>
                </select>
            </div>

            <div>
                <label class="block text-xs font-semibold uppercase text-slate-400 mb-1">Govt ID Number *</label>
                <input type="text" name="id_number" required value="{{ old('id_number') }}" placeholder="e.g. 1234 5678 9012" class="w-full px-4 py-3 rounded-xl bg-slate-900 border border-slate-700 text-white text-sm">
            </div>

            <div class="md:col-span-2">
                <label class="block text-xs font-semibold uppercase text-slate-400 mb-1">Upload ID Proof Screenshot/Document Photo</label>
                <input type="file" name="id_proof" accept="image/*,application/pdf" class="w-full px-4 py-2.5 rounded-xl bg-slate-900 border border-slate-700 text-slate-300 text-xs">
            </div>

            <!-- Room & Rent Allocation -->
            <div class="space-y-4 md:col-span-2 pt-4">
                <div class="flex items-center justify-between border-b border-slate-800 pb-2">
                    <h3 class="text-xs font-bold uppercase tracking-wider text-pink-400">3. Room & Rent Pricing Allocation</h3>
                </div>
            </div>

            <div class="md:col-span-2">
                <label class="block text-xs font-semibold uppercase text-slate-400 mb-1">Assign Room (Shows Current Occupants) *</label>
                <select id="assigned_room_id" name="assigned_room_id" required onchange="updateDefaultRent(this)" class="w-full px-4 py-3 rounded-xl bg-slate-900 border border-slate-700 text-white text-sm font-medium">
                    <option value="">Select Room...</option>
                    @foreach($rooms as $rm)
                    @php
                        $occupantNames = $rm->renters->pluck('name')->implode(', ');
                        $statusBadge = $rm->occupied_beds_count >= $rm->bed_capacity ? 'FULL (' . $rm->occupied_beds_count . '/' . $rm->bed_capacity . ')' : 'Available (' . $rm->available_beds_count . ' beds left)';
                    @endphp
                    <option value="{{ $rm->id }}" data-price="{{ $rm->monthly_price }}">
                        Room {{ $rm->room_number }} ({{ $rm->floor->name ?? 'Floor' }}) - Price: ₹{{ number_format($rm->monthly_price, 0) }} • Status: {{ $statusBadge }} {{ $occupantNames ? '• Occupants: ' . $occupantNames : '• No Occupants' }}
                    </option>
                    @endforeach
                </select>
            </div>

            <div>
                <label class="block text-xs font-semibold uppercase text-slate-400 mb-1">Bed / Seat Number *</label>
                <input type="text" name="bed_number" required value="{{ old('bed_number', 'Bed 1') }}" placeholder="e.g. Bed 1 (Window Side)" class="w-full px-4 py-3 rounded-xl bg-slate-900 border border-slate-700 text-white text-sm">
            </div>

            <div>
                <label class="block text-xs font-semibold uppercase text-slate-400 mb-1">Monthly Rent Amount (₹)</label>
                <input type="number" step="0.01" id="monthly_rent" name="monthly_rent" placeholder="Auto-filled from room price" class="w-full px-4 py-3 rounded-xl bg-slate-900 border border-slate-700 text-white text-sm font-bold">
            </div>

            <div>
                <label class="block text-xs font-semibold uppercase text-slate-400 mb-1">Security Deposit Collected (₹) *</label>
                <input type="number" step="0.01" name="security_deposit" required value="{{ old('security_deposit', 10000) }}" placeholder="10000" class="w-full px-4 py-3 rounded-xl bg-slate-900 border border-slate-700 text-white text-sm">
            </div>

            <div>
                <label class="block text-xs font-semibold uppercase text-slate-400 mb-1">Monthly Rent Due Day *</label>
                <input type="number" min="1" max="28" name="rent_due_day" required value="{{ old('rent_due_day', 5) }}" class="w-full px-4 py-3 rounded-xl bg-slate-900 border border-slate-700 text-white text-sm">
            </div>
        </div>

        <div class="pt-6 border-t border-slate-800 flex justify-end space-x-4">
            <a href="{{ route('admin.renters.index') }}" class="px-6 py-3 rounded-xl bg-slate-800 hover:bg-slate-700 text-slate-300 font-semibold text-sm">Cancel</a>
            <button type="submit" class="px-8 py-3 rounded-xl bg-gradient-to-r from-pink-600 to-rose-600 hover:from-pink-500 hover:to-rose-500 text-white font-bold text-sm shadow-lg shadow-pink-500/25">Complete Onboarding</button>
        </div>
    </form>
</div>

<script>
    function updateDefaultRent(selectElem) {
        const selectedOption = selectElem.options[selectElem.selectedIndex];
        const price = selectedOption.getAttribute('data-price');
        if (price) {
            document.getElementById('monthly_rent').value = price;
        }
    }

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
