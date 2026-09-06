<?php

namespace App\Http\Controllers;

use App\Models\Floor;
use App\Models\Room;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;

class RenterController extends Controller
{
    public function index(Request $request)
    {
        $query = User::where('role', 'renter')->with(['room.floor', 'invoices']);

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%")
                  ->orWhere('phone', 'like', "%{$search}%");
            });
        }

        if ($request->filled('floor_id')) {
            $query->whereHas('room', function($q) use ($request) {
                $q->where('floor_id', $request->floor_id);
            });
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        $renters = $query->latest()->paginate(15);
        $floors = Floor::with('rooms')->get();

        return view('admin.renters.index', compact('renters', 'floors'));
    }

    public function create()
    {
        // Admin can assign ANY room (available, occupied, or maintenance)
        $rooms = Room::with(['floor', 'renters'])->get();
        return view('admin.renters.create', compact('rooms'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|string|min:6',
            'phone' => 'required|string|max:20',
            'emergency_contact' => 'required|string|max:20',
            'id_type' => 'required|string|max:50',
            'id_number' => 'required|string|max:100',
            'id_proof' => 'nullable|image|mimes:jpeg,png,jpg,webp,pdf|max:5120',
            'joining_date' => 'required|date',
            'assigned_room_id' => 'required|exists:rooms,id',
            'bed_number' => 'required|string|max:50',
            'monthly_rent' => 'nullable|numeric|min:0',
            'security_deposit' => 'required|numeric|min:0',
            'rent_due_day' => 'required|integer|between:1,28',
        ]);

        $room = Room::findOrFail($validated['assigned_room_id']);
        $monthlyRent = $validated['monthly_rent'] ?? $room->monthly_price;

        $idProofPath = null;
        if ($request->hasFile('id_proof')) {
            $idProofPath = $request->file('id_proof')->store('id_proofs', 'public');
        }

        $user = User::create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'password' => Hash::make($validated['password']),
            'role' => 'renter',
            'phone' => $validated['phone'],
            'emergency_contact' => $validated['emergency_contact'],
            'id_type' => $validated['id_type'],
            'id_number' => $validated['id_number'],
            'id_proof_path' => $idProofPath,
            'joining_date' => $validated['joining_date'],
            'status' => 'active',
            'assigned_room_id' => $validated['assigned_room_id'],
            'bed_number' => $validated['bed_number'],
            'monthly_rent' => $monthlyRent,
            'security_deposit' => $validated['security_deposit'],
            'rent_due_day' => $validated['rent_due_day'],
        ]);

        return redirect()->route('admin.renters.index')->with('success', 'Renter account created & assigned to Room ' . $room->room_number);
    }

    public function show(User $renter)
    {
        $renter->load(['room.floor', 'invoices.payment', 'delayRequests']);
        return view('admin.renters.show', compact('renter'));
    }

    public function edit(User $renter)
    {
        // Admin can assign ANY room during editing
        $rooms = Room::with(['floor', 'renters'])->get();
        return view('admin.renters.edit', compact('renter', 'rooms'));
    }

    public function update(Request $request, User $renter)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email,' . $renter->id,
            'password' => 'nullable|string|min:6',
            'phone' => 'required|string|max:20',
            'emergency_contact' => 'required|string|max:20',
            'id_type' => 'required|string|max:50',
            'id_number' => 'required|string|max:100',
            'id_proof' => 'nullable|image|mimes:jpeg,png,jpg,webp,pdf|max:5120',
            'joining_date' => 'required|date',
            'status' => 'required|in:active,inactive',
            'assigned_room_id' => 'required|exists:rooms,id',
            'bed_number' => 'required|string|max:50',
            'monthly_rent' => 'required|numeric|min:0',
            'security_deposit' => 'required|numeric|min:0',
            'rent_due_day' => 'required|integer|between:1,28',
        ]);

        if ($request->hasFile('id_proof')) {
            if ($renter->id_proof_path) {
                Storage::disk('public')->delete($renter->id_proof_path);
            }
            $validated['id_proof_path'] = $request->file('id_proof')->store('id_proofs', 'public');
        }

        if (!empty($validated['password'])) {
            $validated['password'] = Hash::make($validated['password']);
        } else {
            unset($validated['password']);
        }

        $renter->update($validated);

        return redirect()->route('admin.renters.show', $renter)->with('success', 'Renter profile & room assignment updated successfully.');
    }

    public function destroy(User $renter)
    {
        if ($renter->id_proof_path) {
            Storage::disk('public')->delete($renter->id_proof_path);
        }
        $renter->delete();
        return redirect()->route('admin.renters.index')->with('success', 'Renter profile deleted.');
    }
}
