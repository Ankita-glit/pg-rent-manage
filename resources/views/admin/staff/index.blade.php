@extends('layouts.app')

@section('title', 'Staff & Salary Management - UrbanStay PG')

@section('content')
<div class="space-y-8">
    <!-- Header -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div>
            <h1 class="text-3xl font-extrabold text-white tracking-tight">Staff & Salary Management</h1>
            <p class="text-sm text-slate-400">Manage PG employees, wardens, caretakers & log/edit monthly salary payouts</p>
        </div>

        <div class="flex items-center space-x-3">
            <button onclick="toggleModal('addStaffModal')" class="px-4 py-2.5 rounded-xl bg-slate-800 hover:bg-slate-700 text-slate-200 text-sm font-semibold border border-slate-700 transition-all flex items-center space-x-2">
                <i data-lucide="user-plus" class="w-4 h-4"></i>
                <span>Add Staff Member</span>
            </button>
            <button onclick="toggleModal('paySalaryModal')" class="px-4 py-2.5 rounded-xl bg-emerald-600 hover:bg-emerald-500 text-white text-sm font-semibold shadow-lg shadow-emerald-500/20 transition-all flex items-center space-x-2">
                <i data-lucide="dollar-sign" class="w-4 h-4"></i>
                <span>Log Salary Payout</span>
            </button>
        </div>
    </div>

    <!-- Staff Members List -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
        @forelse($staffMembers as $staff)
        <div class="glass-panel p-6 rounded-3xl border border-slate-800 flex flex-col justify-between space-y-4">
            <div>
                <div class="flex items-center justify-between mb-3">
                    <div class="flex items-center space-x-3">
                        <div class="w-12 h-12 rounded-2xl bg-gradient-to-tr from-purple-600 to-indigo-600 text-white font-bold flex items-center justify-center text-lg shadow-md">
                            {{ strtoupper(substr($staff->name, 0, 2)) }}
                        </div>
                        <div>
                            <h3 class="text-lg font-bold text-white">{{ $staff->name }}</h3>
                            <p class="text-xs text-indigo-400 font-semibold">{{ $staff->designation }}</p>
                        </div>
                    </div>

                    <div class="flex items-center space-x-1">
                        <button onclick="openEditStaffModal({{ json_encode($staff) }})" class="p-1.5 rounded-lg text-slate-400 hover:text-blue-400 hover:bg-blue-500/10" title="Edit Staff Member">
                            <i data-lucide="edit-3" class="w-4 h-4"></i>
                        </button>
                        <form action="{{ route('admin.staff.destroy', $staff) }}" method="POST" onsubmit="return confirmCustomDelete(event, 'Delete staff record for {{ $staff->name }}?')">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="p-1.5 rounded-lg text-slate-400 hover:text-rose-400 hover:bg-rose-500/10" title="Delete Staff">
                                <i data-lucide="trash-2" class="w-4 h-4"></i>
                            </button>
                        </form>
                    </div>
                </div>

                <div class="p-3.5 rounded-2xl bg-slate-900 border border-slate-800 space-y-1.5 text-xs">
                    <div class="flex justify-between">
                        <span class="text-slate-500">Phone:</span>
                        <span class="font-semibold text-slate-200">{{ $staff->phone }}</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-slate-500">Monthly Salary:</span>
                        <span class="font-bold text-emerald-400">₹{{ number_format($staff->monthly_salary, 2) }}</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-slate-500">Joining Date:</span>
                        <span class="text-slate-300">{{ $staff->joining_date->format('d M Y') }}</span>
                    </div>
                </div>
            </div>

            <!-- Salary History Mini Summary -->
            <div class="pt-3 border-t border-slate-800 space-y-2">
                <span class="text-[11px] font-bold uppercase tracking-wider text-slate-400 block">Recent Salary Payouts:</span>
                @forelse($staff->salaries as $sal)
                <div class="flex items-center justify-between text-xs p-2 rounded-xl bg-slate-900/60 border border-slate-800">
                    <div class="flex items-center space-x-2">
                        <span class="text-slate-300 font-semibold">{{ $sal->payment_date->format('d M Y') }}</span>
                        <span class="font-bold text-emerald-400">₹{{ number_format($sal->amount, 0) }}</span>
                    </div>
                    <div class="flex items-center space-x-1">
                        <button onclick="openEditSalaryModal({{ json_encode($sal) }})" class="p-1 text-slate-400 hover:text-blue-400" title="Edit Salary Entry">
                            <i data-lucide="edit" class="w-3.5 h-3.5"></i>
                        </button>
                        <form action="{{ route('admin.staff.salary.destroy', $sal) }}" method="POST" onsubmit="return confirmCustomDelete(event, 'Delete salary payout record?')">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="p-1 text-slate-400 hover:text-rose-400" title="Delete Entry">
                                <i data-lucide="x" class="w-3.5 h-3.5"></i>
                            </button>
                        </form>
                    </div>
                </div>
                @empty
                <span class="text-xs text-slate-500 italic">No salary payouts recorded yet.</span>
                @endforelse
            </div>
        </div>
        @empty
        <div class="col-span-full glass-panel p-12 text-center text-slate-500 rounded-3xl">
            No staff members added yet. Click "Add Staff Member" to get started.
        </div>
        @endforelse
    </div>
</div>

<!-- Modal: Add Staff -->
<div id="addStaffModal" class="fixed inset-0 z-50 hidden bg-black/85 backdrop-blur-sm flex items-center justify-center p-4">
    <div class="glass-panel p-6 rounded-3xl border border-slate-800 max-w-md w-full space-y-4">
        <div class="flex items-center justify-between">
            <h3 class="text-lg font-bold text-white">Add Staff Member</h3>
            <button onclick="toggleModal('addStaffModal')" class="text-slate-400 hover:text-white"><i data-lucide="x" class="w-5 h-5"></i></button>
        </div>

        <form action="{{ route('admin.staff.store') }}" method="POST" class="space-y-4">
            @csrf
            <div>
                <label class="block text-xs font-semibold uppercase text-slate-400 mb-1">Full Name *</label>
                <input type="text" name="name" required placeholder="e.g. Ramesh Kumar" class="w-full px-4 py-2.5 rounded-xl bg-slate-900 border border-slate-700 text-white text-sm">
            </div>

            <div>
                <label class="block text-xs font-semibold uppercase text-slate-400 mb-1">Designation / Role *</label>
                <input type="text" name="designation" required placeholder="e.g. Resident Warden, Caretaker, Maintenance" class="w-full px-4 py-2.5 rounded-xl bg-slate-900 border border-slate-700 text-white text-sm">
            </div>

            <div class="grid grid-cols-2 gap-3">
                <div>
                    <label class="block text-xs font-semibold uppercase text-slate-400 mb-1">Phone Number *</label>
                    <input type="text" name="phone" required placeholder="+91 98765 43210" class="w-full px-4 py-2.5 rounded-xl bg-slate-900 border border-slate-700 text-white text-sm">
                </div>

                <div>
                    <label class="block text-xs font-semibold uppercase text-slate-400 mb-1">Monthly Salary (₹) *</label>
                    <input type="number" step="0.01" name="monthly_salary" required placeholder="18000" class="w-full px-4 py-2.5 rounded-xl bg-slate-900 border border-slate-700 text-white text-sm">
                </div>
            </div>

            <div>
                <label class="block text-xs font-semibold uppercase text-slate-400 mb-1">Joining Date *</label>
                <input type="date" name="joining_date" required value="{{ date('Y-m-d') }}" class="w-full px-4 py-2.5 rounded-xl bg-slate-900 border border-slate-700 text-white text-sm">
            </div>

            <input type="hidden" name="status" value="active">

            <button type="submit" class="w-full py-3 rounded-xl bg-blue-600 hover:bg-blue-500 text-white font-semibold text-sm">Save Staff Record</button>
        </form>
    </div>
</div>

<!-- Modal: Edit Staff Member -->
<div id="editStaffModal" class="fixed inset-0 z-50 hidden bg-black/85 backdrop-blur-sm flex items-center justify-center p-4">
    <div class="glass-panel p-6 rounded-3xl border border-slate-800 max-w-md w-full space-y-4">
        <div class="flex items-center justify-between">
            <h3 class="text-lg font-bold text-white">Edit Staff Member</h3>
            <button onclick="toggleModal('editStaffModal')" class="text-slate-400 hover:text-white"><i data-lucide="x" class="w-5 h-5"></i></button>
        </div>

        <form id="editStaffForm" method="POST" class="space-y-4">
            @csrf
            @method('PUT')
            <div>
                <label class="block text-xs font-semibold uppercase text-slate-400 mb-1">Full Name *</label>
                <input type="text" id="edit_staff_name" name="name" required class="w-full px-4 py-2.5 rounded-xl bg-slate-900 border border-slate-700 text-white text-sm">
            </div>

            <div>
                <label class="block text-xs font-semibold uppercase text-slate-400 mb-1">Designation / Role *</label>
                <input type="text" id="edit_staff_designation" name="designation" required class="w-full px-4 py-2.5 rounded-xl bg-slate-900 border border-slate-700 text-white text-sm">
            </div>

            <div class="grid grid-cols-2 gap-3">
                <div>
                    <label class="block text-xs font-semibold uppercase text-slate-400 mb-1">Phone Number *</label>
                    <input type="text" id="edit_staff_phone" name="phone" required class="w-full px-4 py-2.5 rounded-xl bg-slate-900 border border-slate-700 text-white text-sm">
                </div>

                <div>
                    <label class="block text-xs font-semibold uppercase text-slate-400 mb-1">Monthly Salary (₹) *</label>
                    <input type="number" step="0.01" id="edit_staff_salary" name="monthly_salary" required class="w-full px-4 py-2.5 rounded-xl bg-slate-900 border border-slate-700 text-white text-sm">
                </div>
            </div>

            <div>
                <label class="block text-xs font-semibold uppercase text-slate-400 mb-1">Joining Date *</label>
                <input type="date" id="edit_staff_joining_date" name="joining_date" required class="w-full px-4 py-2.5 rounded-xl bg-slate-900 border border-slate-700 text-white text-sm">
            </div>

            <input type="hidden" name="status" value="active">

            <button type="submit" class="w-full py-3 rounded-xl bg-blue-600 hover:bg-blue-500 text-white font-semibold text-sm">Update Staff Member</button>
        </form>
    </div>
</div>

<!-- Modal: Log Salary Payout -->
<div id="paySalaryModal" class="fixed inset-0 z-50 hidden bg-black/85 backdrop-blur-sm flex items-center justify-center p-4">
    <div class="glass-panel p-6 rounded-3xl border border-slate-800 max-w-md w-full space-y-4">
        <div class="flex items-center justify-between">
            <h3 class="text-lg font-bold text-white">Log Salary Payout</h3>
            <button onclick="toggleModal('paySalaryModal')" class="text-slate-400 hover:text-white"><i data-lucide="x" class="w-5 h-5"></i></button>
        </div>

        <form action="{{ route('admin.staff.salary.store') }}" method="POST" class="space-y-4">
            @csrf
            <div>
                <label class="block text-xs font-semibold uppercase text-slate-400 mb-1">Select Staff Member *</label>
                <select name="staff_id" required class="w-full px-4 py-2.5 rounded-xl bg-slate-900 border border-slate-700 text-white text-sm">
                    @foreach($staffMembers as $st)
                    <option value="{{ $st->id }}">{{ $st->name }} ({{ $st->designation }}) - ₹{{ number_format($st->monthly_salary, 0) }}</option>
                    @endforeach
                </select>
            </div>

            <div class="grid grid-cols-2 gap-3">
                <div>
                    <label class="block text-xs font-semibold uppercase text-slate-400 mb-1">Amount Paid (₹) *</label>
                    <input type="number" step="0.01" name="amount" required placeholder="18000" class="w-full px-4 py-2.5 rounded-xl bg-slate-900 border border-slate-700 text-white text-sm font-bold">
                </div>

                <div>
                    <label class="block text-xs font-semibold uppercase text-slate-400 mb-1">Payment Method *</label>
                    <select name="payment_method" class="w-full px-4 py-2.5 rounded-xl bg-slate-900 border border-slate-700 text-white text-sm">
                        <option value="cash">Cash</option>
                        <option value="bank_transfer">Bank Transfer</option>
                        <option value="upi">UPI Transfer</option>
                    </select>
                </div>
            </div>

            <div>
                <label class="block text-xs font-semibold uppercase text-slate-400 mb-1">Payment Date *</label>
                <input type="date" name="payment_date" required value="{{ date('Y-m-d') }}" class="w-full px-4 py-2.5 rounded-xl bg-slate-900 border border-slate-700 text-white text-sm">
            </div>

            <div>
                <label class="block text-xs font-semibold uppercase text-slate-400 mb-1">Notes / Voucher Reference</label>
                <input type="text" name="notes" placeholder="Salary payout for September" class="w-full px-4 py-2.5 rounded-xl bg-slate-900 border border-slate-700 text-white text-sm">
            </div>

            <button type="submit" class="w-full py-3 rounded-xl bg-emerald-600 hover:bg-emerald-500 text-white font-semibold text-sm">Record Salary Payment</button>
        </form>
    </div>
</div>

<!-- Modal: Edit Salary Entry -->
<div id="editSalaryModal" class="fixed inset-0 z-50 hidden bg-black/85 backdrop-blur-sm flex items-center justify-center p-4">
    <div class="glass-panel p-6 rounded-3xl border border-slate-800 max-w-md w-full space-y-4">
        <div class="flex items-center justify-between">
            <h3 class="text-lg font-bold text-white">Edit Salary Payment Entry</h3>
            <button onclick="toggleModal('editSalaryModal')" class="text-slate-400 hover:text-white"><i data-lucide="x" class="w-5 h-5"></i></button>
        </div>

        <form id="editSalaryForm" method="POST" class="space-y-4">
            @csrf
            @method('PUT')
            <div>
                <label class="block text-xs font-semibold uppercase text-slate-400 mb-1">Staff Member *</label>
                <select id="edit_sal_staff_id" name="staff_id" required class="w-full px-4 py-2.5 rounded-xl bg-slate-900 border border-slate-700 text-white text-sm">
                    @foreach($staffMembers as $st)
                    <option value="{{ $st->id }}">{{ $st->name }} ({{ $st->designation }})</option>
                    @endforeach
                </select>
            </div>

            <div class="grid grid-cols-2 gap-3">
                <div>
                    <label class="block text-xs font-semibold uppercase text-slate-400 mb-1">Amount Paid (₹) *</label>
                    <input type="number" step="0.01" id="edit_sal_amount" name="amount" required class="w-full px-4 py-2.5 rounded-xl bg-slate-900 border border-slate-700 text-white text-sm font-bold">
                </div>

                <div>
                    <label class="block text-xs font-semibold uppercase text-slate-400 mb-1">Payment Method *</label>
                    <select id="edit_sal_method" name="payment_method" class="w-full px-4 py-2.5 rounded-xl bg-slate-900 border border-slate-700 text-white text-sm">
                        <option value="cash">Cash</option>
                        <option value="bank_transfer">Bank Transfer</option>
                        <option value="upi">UPI Transfer</option>
                    </select>
                </div>
            </div>

            <div>
                <label class="block text-xs font-semibold uppercase text-slate-400 mb-1">Payment Date *</label>
                <input type="date" id="edit_sal_date" name="payment_date" required class="w-full px-4 py-2.5 rounded-xl bg-slate-900 border border-slate-700 text-white text-sm">
            </div>

            <div>
                <label class="block text-xs font-semibold uppercase text-slate-400 mb-1">Notes / Voucher Reference</label>
                <input type="text" id="edit_sal_notes" name="notes" class="w-full px-4 py-2.5 rounded-xl bg-slate-900 border border-slate-700 text-white text-sm">
            </div>

            <button type="submit" class="w-full py-3 rounded-xl bg-emerald-600 hover:bg-emerald-500 text-white font-semibold text-sm">Update Salary Entry</button>
        </form>
    </div>
</div>

<script>
    function toggleModal(id) {
        document.getElementById(id).classList.toggle('hidden');
    }

    function openEditStaffModal(staff) {
        document.getElementById('editStaffForm').action = '/admin/staff/' + staff.id;
        document.getElementById('edit_staff_name').value = staff.name;
        document.getElementById('edit_staff_designation').value = staff.designation;
        document.getElementById('edit_staff_phone').value = staff.phone;
        document.getElementById('edit_staff_salary').value = staff.monthly_salary;
        document.getElementById('edit_staff_joining_date').value = staff.joining_date ? staff.joining_date.substring(0, 10) : '';
        toggleModal('editStaffModal');
    }

    function openEditSalaryModal(sal) {
        document.getElementById('editSalaryForm').action = '/admin/staff/salary/' + sal.id;
        document.getElementById('edit_sal_staff_id').value = sal.staff_id;
        document.getElementById('edit_sal_amount').value = sal.amount;
        document.getElementById('edit_sal_method').value = sal.payment_method;
        document.getElementById('edit_sal_date').value = sal.payment_date ? sal.payment_date.substring(0, 10) : '';
        document.getElementById('edit_sal_notes').value = sal.notes || '';
        toggleModal('editSalaryModal');
    }
</script>
@endsection
