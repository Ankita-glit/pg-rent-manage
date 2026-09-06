@extends('layouts.app')

@section('title', 'Floors & Rooms Management - UrbanStay PG')

@section('content')
<div class="space-y-8">
    <!-- Header -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div>
            <h1 class="text-3xl font-extrabold text-white tracking-tight">Floors & Rooms Architecture</h1>
            <p class="text-sm text-slate-400">Configure floor structures, individual room monthly rent pricing & bed capacities</p>
        </div>

        <div class="flex items-center space-x-3">
            <button onclick="toggleModal('addFloorModal')" class="px-4 py-2.5 rounded-xl bg-slate-800 hover:bg-slate-700 text-slate-200 text-sm font-semibold border border-slate-700 transition-all flex items-center space-x-2">
                <i data-lucide="plus" class="w-4 h-4"></i>
                <span>Add New Floor</span>
            </button>
            <button onclick="toggleModal('addRoomModal')" class="px-4 py-2.5 rounded-xl bg-blue-600 hover:bg-blue-500 text-white text-sm font-semibold shadow-lg shadow-blue-500/20 transition-all flex items-center space-x-2">
                <i data-lucide="plus-circle" class="w-4 h-4"></i>
                <span>Add Room with Price</span>
            </button>
        </div>
    </div>

    <!-- Floors & Rooms Cards Grid -->
    <div class="space-y-8">
        @forelse($floors as $floor)
        <div class="glass-panel p-6 rounded-3xl border border-slate-800 space-y-6">
            <div class="flex items-center justify-between border-b border-slate-800/80 pb-4">
                <div class="flex items-center space-x-3">
                    <div class="w-10 h-10 rounded-xl bg-gradient-to-tr from-indigo-600 to-purple-600 flex items-center justify-center text-white font-bold text-lg shadow-md">
                        {{ $floor->floor_number }}
                    </div>
                    <div>
                        <h3 class="text-xl font-bold text-white">{{ $floor->name }}</h3>
                        <p class="text-xs text-slate-400">{{ $floor->description ?? 'No description' }} • {{ $floor->rooms->count() }} Total Rooms</p>
                    </div>
                </div>

                <div class="flex items-center space-x-2">
                    <form id="deleteFloorForm_{{ $floor->id }}" action="{{ route('admin.floors.destroy', $floor) }}" method="POST" class="hidden">
                        @csrf
                        @method('DELETE')
                    </form>
                    <button type="button" onclick="confirmCustomDelete(event, 'Delete {{ addslashes($floor->name) }}?\nThis will also delete all rooms on this floor if they have no active renters.', 'deleteFloorForm_{{ $floor->id }}')" class="p-2 rounded-xl text-slate-400 hover:text-rose-400 hover:bg-rose-500/10 transition-colors" title="Delete Floor">
                        <i data-lucide="trash-2" class="w-4 h-4"></i>
                    </button>
                </div>
            </div>

            <!-- Rooms Grid for this floor -->
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-5">
                @forelse($floor->rooms as $room)
                <div class="glass-card p-5 rounded-2xl border border-slate-800 flex flex-col justify-between space-y-4">
                    <div>
                        <div class="flex items-center justify-between mb-2">
                            <div class="flex items-center space-x-2">
                                <span class="text-lg font-extrabold text-white">Room {{ $room->room_number }}</span>
                                <span class="px-2.5 py-0.5 rounded-full text-[10px] font-bold uppercase tracking-wider {{ $room->status === 'available' ? 'bg-emerald-500/20 text-emerald-300 border border-emerald-500/30' : ($room->status === 'occupied' ? 'bg-blue-500/20 text-blue-300 border border-blue-500/30' : 'bg-rose-500/20 text-rose-300 border border-rose-500/30') }}">
                                    {{ $room->status }}
                                </span>
                            </div>

                            <div class="flex items-center space-x-1">
                                <button onclick="openEditRoomModal({{ json_encode($room) }})" class="p-1.5 rounded-lg text-slate-400 hover:text-blue-400 hover:bg-blue-500/10">
                                    <i data-lucide="edit-3" class="w-4 h-4"></i>
                                </button>
                                <form id="deleteRoomForm_{{ $room->id }}" action="{{ route('admin.rooms.destroy', $room) }}" method="POST" class="hidden">
                                    @csrf
                                    @method('DELETE')
                                </form>
                                <button type="button" onclick="confirmCustomDelete(event, 'Delete Room {{ addslashes($room->room_number) }}? This cannot be undone.', 'deleteRoomForm_{{ $room->id }}')" class="p-1.5 rounded-lg text-slate-400 hover:text-rose-400 hover:bg-rose-500/10">
                                    <i data-lucide="trash-2" class="w-4 h-4"></i>
                                </button>
                            </div>
                        </div>

                        <p class="text-xs text-slate-400 mb-3">{{ $room->description ?? 'Standard room' }}</p>

                        <!-- Room Price Highlight -->
                        <div class="p-3 rounded-xl bg-slate-800/80 border border-slate-700/60 flex items-center justify-between">
                            <div>
                                <span class="text-[10px] font-bold text-slate-400 uppercase tracking-wider block">Monthly Rent Price</span>
                                <span class="text-lg font-extrabold text-emerald-400">₹{{ number_format($room->monthly_price, 2) }}</span>
                            </div>
                            <div class="text-right">
                                <span class="text-[10px] font-bold text-slate-400 uppercase tracking-wider block">Bed Capacity</span>
                                <span class="text-sm font-semibold text-slate-200">{{ $room->occupied_beds_count }} / {{ $room->bed_capacity }} Occupied</span>
                            </div>
                        </div>
                    </div>

                    <!-- Renters in this room -->
                    <div class="pt-2 border-t border-slate-800/80">
                        <span class="text-[11px] font-semibold text-slate-400 block mb-1.5">Current Occupants:</span>
                        @if($room->renters->count() > 0)
                        <div class="space-y-1">
                            @foreach($room->renters as $renter)
                            <div class="flex items-center justify-between text-xs text-slate-300">
                                <span class="font-medium text-white">• {{ $renter->name }}</span>
                                <span class="text-[10px] text-slate-400">{{ $renter->bed_number ?? 'Bed assigned' }}</span>
                            </div>
                            @endforeach
                        </div>
                        @else
                        <span class="text-xs italic text-slate-500">No renters assigned yet</span>
                        @endif
                    </div>
                </div>
                @empty
                <div class="col-span-full p-6 text-center text-slate-500 italic border border-dashed border-slate-800 rounded-2xl">
                    No rooms created on this floor yet. Click "Add Room with Price" above to create one.
                </div>
                @endforelse
            </div>
        </div>
        @empty
        <div class="glass-panel p-12 text-center text-slate-500 rounded-3xl">
            No floors defined yet. Add your first floor to get started.
        </div>
        @endforelse
    </div>
</div>

<!-- Modal: Add Floor -->
<div id="addFloorModal" class="fixed inset-0 z-50 hidden bg-black/80 backdrop-blur-sm flex items-center justify-center p-4">
    <div class="glass-panel p-6 rounded-3xl border border-slate-800 max-w-md w-full space-y-4">
        <div class="flex items-center justify-between">
            <h3 class="text-lg font-bold text-white">Add New Floor</h3>
            <button onclick="toggleModal('addFloorModal')" class="text-slate-400 hover:text-white"><i data-lucide="x" class="w-5 h-5"></i></button>
        </div>

        <form action="{{ route('admin.floors.store') }}" method="POST" class="space-y-4">
            @csrf
            <div>
                <label class="block text-xs font-semibold uppercase text-slate-400 mb-1">Floor Number</label>
                <input type="number" name="floor_number" required placeholder="0 for Ground, 1 for 1st Floor..." class="w-full px-4 py-2.5 rounded-xl bg-slate-900 border border-slate-700 text-white text-sm">
            </div>

            <div>
                <label class="block text-xs font-semibold uppercase text-slate-400 mb-1">Floor Name</label>
                <input type="text" name="name" required placeholder="e.g. Ground Floor, 1st Floor" class="w-full px-4 py-2.5 rounded-xl bg-slate-900 border border-slate-700 text-white text-sm">
            </div>

            <div>
                <label class="block text-xs font-semibold uppercase text-slate-400 mb-1">Description (Optional)</label>
                <textarea name="description" placeholder="e.g. Deluxe AC Rooms floor" class="w-full px-4 py-2.5 rounded-xl bg-slate-900 border border-slate-700 text-white text-sm h-20"></textarea>
            </div>

            <button type="submit" class="w-full py-3 rounded-xl bg-blue-600 hover:bg-blue-500 text-white font-semibold text-sm">Create Floor</button>
        </form>
    </div>
</div>

<!-- Modal: Add Room -->
<div id="addRoomModal" class="fixed inset-0 z-50 hidden bg-black/80 backdrop-blur-sm flex items-center justify-center p-4">
    <div class="glass-panel p-6 rounded-3xl border border-slate-800 max-w-md w-full space-y-4">
        <div class="flex items-center justify-between">
            <h3 class="text-lg font-bold text-white">Add Room with Price</h3>
            <button onclick="toggleModal('addRoomModal')" class="text-slate-400 hover:text-white"><i data-lucide="x" class="w-5 h-5"></i></button>
        </div>

        <form action="{{ route('admin.rooms.store') }}" method="POST" class="space-y-4">
            @csrf
            <div>
                <label class="block text-xs font-semibold uppercase text-slate-400 mb-1">Select Floor</label>
                <select name="floor_id" required class="w-full px-4 py-2.5 rounded-xl bg-slate-900 border border-slate-700 text-white text-sm">
                    @foreach($floors as $fl)
                    <option value="{{ $fl->id }}">{{ $fl->name }} (Floor {{ $fl->floor_number }})</option>
                    @endforeach
                </select>
            </div>

            <div>
                <label class="block text-xs font-semibold uppercase text-slate-400 mb-1">Room Number / Name</label>
                <input type="text" name="room_number" required placeholder="e.g. 101, 102, G-1" class="w-full px-4 py-2.5 rounded-xl bg-slate-900 border border-slate-700 text-white text-sm">
            </div>

            <div class="grid grid-cols-2 gap-3">
                <div>
                    <label class="block text-xs font-semibold uppercase text-slate-400 mb-1">Bed Capacity</label>
                    <input type="number" name="bed_capacity" value="2" min="1" required class="w-full px-4 py-2.5 rounded-xl bg-slate-900 border border-slate-700 text-white text-sm">
                </div>

                <div>
                    <label class="block text-xs font-semibold uppercase text-slate-400 mb-1">Monthly Rent Price (₹)</label>
                    <input type="number" step="0.01" name="monthly_price" required placeholder="8500" class="w-full px-4 py-2.5 rounded-xl bg-slate-900 border border-slate-700 text-white text-sm">
                </div>
            </div>

            <div>
                <label class="block text-xs font-semibold uppercase text-slate-400 mb-1">Initial Status</label>
                <select name="status" class="w-full px-4 py-2.5 rounded-xl bg-slate-900 border border-slate-700 text-white text-sm">
                    <option value="available">Available</option>
                    <option value="occupied">Occupied</option>
                    <option value="maintenance">Under Maintenance</option>
                </select>
            </div>

            <div>
                <label class="block text-xs font-semibold uppercase text-slate-400 mb-1">Room Description</label>
                <textarea name="description" placeholder="AC / Non-AC, attached bathroom, balcony..." class="w-full px-4 py-2.5 rounded-xl bg-slate-900 border border-slate-700 text-white text-sm h-16"></textarea>
            </div>

            <button type="submit" class="w-full py-3 rounded-xl bg-blue-600 hover:bg-blue-500 text-white font-semibold text-sm">Save Room & Price</button>
        </form>
    </div>
</div>

<!-- Modal: Edit Room -->
<div id="editRoomModal" class="fixed inset-0 z-50 hidden bg-black/80 backdrop-blur-sm flex items-center justify-center p-4">
    <div class="glass-panel p-6 rounded-3xl border border-slate-800 max-w-md w-full space-y-4">
        <div class="flex items-center justify-between">
            <h3 class="text-lg font-bold text-white">Edit Room Price & Details</h3>
            <button onclick="toggleModal('editRoomModal')" class="text-slate-400 hover:text-white"><i data-lucide="x" class="w-5 h-5"></i></button>
        </div>

        <form id="editRoomForm" method="POST" class="space-y-4">
            @csrf
            @method('PUT')

            <div>
                <label class="block text-xs font-semibold uppercase text-slate-400 mb-1">Floor</label>
                <select id="edit_floor_id" name="floor_id" required class="w-full px-4 py-2.5 rounded-xl bg-slate-900 border border-slate-700 text-white text-sm">
                    @foreach($floors as $fl)
                    <option value="{{ $fl->id }}">{{ $fl->name }}</option>
                    @endforeach
                </select>
            </div>

            <div>
                <label class="block text-xs font-semibold uppercase text-slate-400 mb-1">Room Number</label>
                <input type="text" id="edit_room_number" name="room_number" required class="w-full px-4 py-2.5 rounded-xl bg-slate-900 border border-slate-700 text-white text-sm">
            </div>

            <div class="grid grid-cols-2 gap-3">
                <div>
                    <label class="block text-xs font-semibold uppercase text-slate-400 mb-1">Bed Capacity</label>
                    <input type="number" id="edit_bed_capacity" name="bed_capacity" min="1" required class="w-full px-4 py-2.5 rounded-xl bg-slate-900 border border-slate-700 text-white text-sm">
                </div>

                <div>
                    <label class="block text-xs font-semibold uppercase text-slate-400 mb-1">Monthly Rent Price (₹)</label>
                    <input type="number" step="0.01" id="edit_monthly_price" name="monthly_price" required class="w-full px-4 py-2.5 rounded-xl bg-slate-900 border border-slate-700 text-white text-sm">
                </div>
            </div>

            <div>
                <label class="block text-xs font-semibold uppercase text-slate-400 mb-1">Status</label>
                <select id="edit_status" name="status" class="w-full px-4 py-2.5 rounded-xl bg-slate-900 border border-slate-700 text-white text-sm">
                    <option value="available">Available</option>
                    <option value="occupied">Occupied</option>
                    <option value="maintenance">Under Maintenance</option>
                </select>
            </div>

            <div>
                <label class="block text-xs font-semibold uppercase text-slate-400 mb-1">Room Description</label>
                <textarea id="edit_description" name="description" class="w-full px-4 py-2.5 rounded-xl bg-slate-900 border border-slate-700 text-white text-sm h-16"></textarea>
            </div>

            <button type="submit" class="w-full py-3 rounded-xl bg-blue-600 hover:bg-blue-500 text-white font-semibold text-sm">Update Room Details</button>
        </form>
    </div>
</div>

<script>
    function toggleModal(id) {
        document.getElementById(id).classList.toggle('hidden');
    }

    function openEditRoomModal(room) {
        document.getElementById('editRoomForm').action = '/admin/rooms/' + room.id;
        document.getElementById('edit_floor_id').value = room.floor_id;
        document.getElementById('edit_room_number').value = room.room_number;
        document.getElementById('edit_bed_capacity').value = room.bed_capacity;
        document.getElementById('edit_monthly_price').value = room.monthly_price;
        document.getElementById('edit_status').value = room.status;
        document.getElementById('edit_description').value = room.description || '';
        toggleModal('editRoomModal');
    }
</script>
@endsection
