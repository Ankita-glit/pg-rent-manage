<?php

namespace App\Http\Controllers;

use App\Models\Staff;
use App\Models\StaffSalary;
use Illuminate\Http\Request;

class StaffController extends Controller
{
    public function index()
    {
        $staffMembers = Staff::with(['salaries' => function($q) {
            $q->latest();
        }])->latest()->get();

        return view('admin.staff.index', compact('staffMembers'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'designation' => 'required|string|max:100',
            'phone' => 'required|string|max:20',
            'email' => 'nullable|email',
            'monthly_salary' => 'required|numeric|min:0',
            'joining_date' => 'required|date',
            'status' => 'required|in:active,inactive',
        ]);

        Staff::create($validated);

        return back()->with('success', 'Staff member added successfully!');
    }

    public function update(Request $request, Staff $staff)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'designation' => 'required|string|max:100',
            'phone' => 'required|string|max:20',
            'email' => 'nullable|email',
            'monthly_salary' => 'required|numeric|min:0',
            'joining_date' => 'required|date',
            'status' => 'required|in:active,inactive',
        ]);

        $staff->update($validated);

        return back()->with('success', 'Staff member details updated successfully!');
    }

    public function destroy(Staff $staff)
    {
        $staff->delete();
        return back()->with('success', 'Staff record deleted.');
    }

    public function storeSalary(Request $request)
    {
        $validated = $request->validate([
            'staff_id' => 'required|exists:staff,id',
            'amount' => 'required|numeric|min:1',
            'payment_date' => 'required|date',
            'payment_method' => 'required|in:cash,bank_transfer,upi',
            'notes' => 'nullable|string',
        ]);

        StaffSalary::create($validated);

        $staff = Staff::findOrFail($validated['staff_id']);

        return back()->with('success', "Salary payout of ₹{$validated['amount']} recorded for {$staff->name}.");
    }

    public function updateSalary(Request $request, StaffSalary $salary)
    {
        $validated = $request->validate([
            'staff_id' => 'required|exists:staff,id',
            'amount' => 'required|numeric|min:1',
            'payment_date' => 'required|date',
            'payment_method' => 'required|in:cash,bank_transfer,upi',
            'notes' => 'nullable|string',
        ]);

        $salary->update($validated);

        return back()->with('success', 'Staff salary payment record updated successfully!');
    }

    public function destroySalary(StaffSalary $salary)
    {
        $salary->delete();
        return back()->with('success', 'Salary payout record deleted.');
    }
}
