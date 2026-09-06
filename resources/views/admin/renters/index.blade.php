@extends('layouts.app')

@section('title', 'Renter Directory - UrbanStay PG')

@section('content')
<div class="space-y-6">
    <!-- Header -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div>
            <h1 class="text-3xl font-extrabold text-white tracking-tight">Renter Directory</h1>
            <p class="text-sm text-slate-400">All registered PG renters managed & onboarded by Admin</p>
        </div>

        <a href="{{ route('admin.renters.create') }}" class="px-4 py-2.5 rounded-xl bg-gradient-to-r from-blue-600 to-indigo-600 hover:from-blue-500 hover:to-indigo-500 text-white text-sm font-bold shadow-lg shadow-blue-500/20 transition-all flex items-center space-x-2 shrink-0">
            <i data-lucide="user-plus" class="w-4 h-4"></i>
            <span>Register & Assign Renter</span>
        </a>
    </div>

    <!-- Filters & Search -->
    <div class="glass-panel p-4 rounded-2xl border border-slate-800">
        <form method="GET" action="{{ route('admin.renters.index') }}" class="grid grid-cols-1 sm:grid-cols-4 gap-4">
            <div>
                <input type="text" name="search" value="{{ request('search') }}" placeholder="Search by name, email, phone..." class="w-full px-4 py-2 rounded-xl bg-slate-900 border border-slate-700 text-white text-xs">
            </div>

            <div>
                <select name="floor_id" class="w-full px-4 py-2 rounded-xl bg-slate-900 border border-slate-700 text-white text-xs">
                    <option value="">Filter by Floor (All)</option>
                    @foreach($floors as $fl)
                    <option value="{{ $fl->id }}" {{ request('floor_id') == $fl->id ? 'selected' : '' }}>{{ $fl->name }}</option>
                    @endforeach
                </select>
            </div>

            <div>
                <select name="status" class="w-full px-4 py-2 rounded-xl bg-slate-900 border border-slate-700 text-white text-xs">
                    <option value="">Filter by Status (All)</option>
                    <option value="active" {{ request('status') === 'active' ? 'selected' : '' }}>Active</option>
                    <option value="inactive" {{ request('status') === 'inactive' ? 'selected' : '' }}>Inactive / Vacated</option>
                </select>
            </div>

            <div class="flex items-center space-x-2">
                <button type="submit" class="w-full py-2 px-3 rounded-xl bg-slate-800 hover:bg-slate-700 text-slate-200 text-xs font-bold border border-slate-700">Filter</button>
                <a href="{{ route('admin.renters.index') }}" class="py-2 px-3 rounded-xl bg-slate-900 text-slate-400 hover:text-white text-xs">Reset</a>
            </div>
        </form>
    </div>

    <!-- Renters Table -->
    <div class="glass-panel rounded-3xl border border-slate-800 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left text-sm">
                <thead class="bg-slate-900/80 text-slate-400 border-b border-slate-800 text-xs uppercase tracking-wider">
                    <tr>
                        <th class="p-4">Renter Info</th>
                        <th class="p-4">Assigned Room & Bed</th>
                        <th class="p-4">Living Duration</th>
                        <th class="p-4">Rent & Security</th>
                        <th class="p-4">Govt ID & Contact</th>
                        <th class="p-4 text-right">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-800/60">
                    @forelse($renters as $renter)
                    <tr class="hover:bg-slate-800/30">
                        <td class="p-4">
                            <div class="flex items-center space-x-3">
                                <div class="w-10 h-10 rounded-full bg-gradient-to-tr from-blue-600 to-indigo-600 text-white font-bold flex items-center justify-center text-sm shadow">
                                    {{ strtoupper(substr($renter->name, 0, 2)) }}
                                </div>
                                <div>
                                    <a href="{{ route('admin.renters.show', $renter) }}" class="font-bold text-white hover:text-blue-400 transition-colors">{{ $renter->name }}</a>
                                    <p class="text-xs text-slate-400">{{ $renter->email }}</p>
                                </div>
                            </div>
                        </td>

                        <td class="p-4">
                            @if($renter->room)
                            <div class="font-bold text-white">Room {{ $renter->room->room_number }}</div>
                            <div class="text-xs text-slate-400">{{ $renter->room->floor->name ?? 'Floor' }} • {{ $renter->bed_number ?? 'Bed assigned' }}</div>
                            @else
                            <span class="text-xs italic text-rose-400">Unassigned</span>
                            @endif
                        </td>

                        <td class="p-4">
                            <span class="px-2.5 py-1 rounded-full text-xs font-semibold bg-indigo-500/20 text-indigo-300 border border-indigo-500/30">
                                {{ $renter->tenure_duration }}
                            </span>
                            <span class="block text-[11px] text-slate-400 mt-1">Since {{ $renter->joining_date ? $renter->joining_date->format('d M Y') : 'N/A' }}</span>
                        </td>

                        <td class="p-4">
                            <div class="font-bold text-emerald-400">₹{{ number_format($renter->monthly_rent, 2) }} / mo</div>
                            <div class="text-xs text-slate-400">Deposit: ₹{{ number_format($renter->security_deposit, 0) }} • Due Day: {{ $renter->rent_due_day }}th</div>
                        </td>

                        <td class="p-4">
                            <div class="text-xs font-semibold text-slate-200">{{ $renter->phone }}</div>
                            <div class="text-[11px] text-slate-400">{{ $renter->id_type }}: {{ $renter->id_number }}</div>
                            @if($renter->id_proof_path)
                            <button onclick="openImageModal('{{ asset('storage/' . $renter->id_proof_path) }}')" class="mt-1 text-[10px] text-blue-400 hover:underline flex items-center space-x-1">
                                <i data-lucide="file-text" class="w-3 h-3"></i>
                                <span>View ID Document</span>
                            </button>
                            @endif
                        </td>

                        <td class="p-4 text-right">
                            <div class="flex items-center justify-end space-x-2">
                                <a href="{{ route('admin.renters.show', $renter) }}" class="p-2 rounded-xl text-slate-400 hover:text-blue-400 hover:bg-blue-500/10" title="View Profile">
                                    <i data-lucide="eye" class="w-4 h-4"></i>
                                </a>
                                <a href="{{ route('admin.renters.edit', $renter) }}" class="p-2 rounded-xl text-slate-400 hover:text-amber-400 hover:bg-amber-500/10" title="Edit Renter">
                                    <i data-lucide="edit" class="w-4 h-4"></i>
                                </a>
                                <form action="{{ route('admin.renters.destroy', $renter) }}" method="POST" onsubmit="return confirmCustomDelete(event, 'Delete renter profile for {{ $renter->name }}?')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="p-2 rounded-xl text-slate-400 hover:text-rose-400 hover:bg-rose-500/10" title="Delete">
                                        <i data-lucide="trash-2" class="w-4 h-4"></i>
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6" class="p-8 text-center text-slate-500">No renters found matching criteria.</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="p-4 border-t border-slate-800">
            {{ $renters->links() }}
        </div>
    </div>
</div>
@endsection
