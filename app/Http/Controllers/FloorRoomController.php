<?php

namespace App\Http\Controllers;

use App\Models\Floor;
use App\Models\Room;
use Illuminate\Http\Request;

class FloorRoomController extends Controller
{
    public function index()
    {
        $floors = Floor::with(['rooms.renters'])->orderBy('floor_number')->get();
        return view('admin.floors-rooms.index', compact('floors'));
    }

    public function storeFloor(Request $request)
    {
        $validated = $request->validate([
            'floor_number' => 'required|integer|unique:floors,floor_number',
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
        ]);

        Floor::create($validated);

        return back()->with('success', 'Floor created successfully!');
    }

    public function updateFloor(Request $request, Floor $floor)
    {
        $validated = $request->validate([
            'floor_number' => 'required|integer|unique:floors,floor_number,' . $floor->id,
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
        ]);

        $floor->update($validated);

        return back()->with('success', 'Floor updated successfully!');
    }

    public function destroyFloor(Floor $floor)
    {
        if ($floor->rooms()->count() > 0) {
            return back()->with('error', 'Cannot delete floor with existing rooms. Delete rooms first.');
        }

        $floor->delete();
        return back()->with('success', 'Floor deleted successfully!');
    }

    public function storeRoom(Request $request)
    {
        $validated = $request->validate([
            'floor_id' => 'required|exists:floors,id',
            'room_number' => 'required|string|max:50|unique:rooms,room_number',
            'bed_capacity' => 'required|integer|min:1',
            'monthly_price' => 'required|numeric|min:0',
            'status' => 'required|in:available,occupied,maintenance',
            'description' => 'nullable|string',
        ]);

        Room::create($validated);

        return back()->with('success', 'Room created with custom price!');
    }

    public function updateRoom(Request $request, Room $room)
    {
        $validated = $request->validate([
            'floor_id' => 'required|exists:floors,id',
            'room_number' => 'required|string|max:50|unique:rooms,room_number,' . $room->id,
            'bed_capacity' => 'required|integer|min:1',
            'monthly_price' => 'required|numeric|min:0',
            'status' => 'required|in:available,occupied,maintenance',
            'description' => 'nullable|string',
        ]);

        $room->update($validated);

        return back()->with('success', 'Room price & details updated successfully!');
    }

    public function destroyRoom(Room $room)
    {
        if ($room->renters()->where('status', 'active')->count() > 0) {
            return back()->with('error', 'Cannot delete room with active renters. Unassign renters first.');
        }

        $room->delete();
        return back()->with('success', 'Room deleted successfully!');
    }
}
