<?php

namespace App\Http\Controllers;

use App\Models\RentInvoice;
use App\Models\RentPayment;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class PaymentVerificationController extends Controller
{
    public function index(Request $request)
    {
        $query = RentPayment::with(['invoice.renter.room.floor']);

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        } else {
            // Default filter to pending approval first
            $query->where('status', 'pending_approval');
        }

        $payments = $query->latest()->paginate(15);
        $pendingCount = RentPayment::where('status', 'pending_approval')->count();

        return view('admin.payments.index', compact('payments', 'pendingCount'));
    }

    public function approve(RentPayment $payment)
    {
        $payment->update([
            'status' => 'approved',
            'verified_by' => Auth::id(),
        ]);

        $invoice = $payment->invoice;
        $invoice->update([
            'status' => 'paid',
            'paid_at' => Carbon::now(),
        ]);

        return back()->with('success', 'Payment verified and approved! Invoice marked as PAID.');
    }

    public function reject(Request $request, RentPayment $payment)
    {
        $validated = $request->validate([
            'rejection_reason' => 'required|string|max:500',
        ]);

        $payment->update([
            'status' => 'rejected',
            'rejection_reason' => $validated['rejection_reason'],
            'verified_by' => Auth::id(),
        ]);

        // Keep invoice pending so renter can re-submit
        $invoice = $payment->invoice;
        $invoice->update([
            'status' => 'pending',
        ]);

        return back()->with('success', 'Payment proof rejected with reason: ' . $validated['rejection_reason']);
    }
}
