<?php

namespace App\Http\Controllers;

use App\Models\DelayRequest;
use App\Models\RentInvoice;
use Illuminate\Http\Request;

class DelayRequestController extends Controller
{
    public function index(Request $request)
    {
        $query = DelayRequest::with(['renter.room.floor', 'invoice']);

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        } else {
            $query->where('status', 'pending');
        }

        $delayRequests = $query->latest()->paginate(15);
        $pendingCount = DelayRequest::where('status', 'pending')->count();

        return view('admin.delay-requests.index', compact('delayRequests', 'pendingCount'));
    }

    public function approve(Request $request, DelayRequest $delayRequest)
    {
        $validated = $request->validate([
            'admin_comment' => 'nullable|string|max:500',
        ]);

        $delayRequest->update([
            'status' => 'approved',
            'admin_comment' => $validated['admin_comment'] ?? 'Delay approved by Admin.',
        ]);

        // Update the invoice due date and status
        $invoice = $delayRequest->invoice;
        $invoice->update([
            'due_date' => $delayRequest->requested_date,
            'status' => 'delay_requested',
        ]);

        return back()->with('success', 'Rent payment delay approved! Due date extended to ' . $delayRequest->requested_date->format('d M Y'));
    }

    public function reject(Request $request, DelayRequest $delayRequest)
    {
        $validated = $request->validate([
            'admin_comment' => 'required|string|max:500',
        ]);

        $delayRequest->update([
            'status' => 'rejected',
            'admin_comment' => $validated['admin_comment'],
        ]);

        $invoice = $delayRequest->invoice;
        if ($invoice->status === 'delay_requested') {
            $invoice->update(['status' => 'pending']);
        }

        return back()->with('success', 'Delay request rejected.');
    }
}
