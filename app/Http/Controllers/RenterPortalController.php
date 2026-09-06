<?php

namespace App\Http\Controllers;

use App\Models\DelayRequest;
use App\Models\PgForm;
use App\Models\RentInvoice;
use App\Models\RentPayment;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class RenterPortalController extends Controller
{
    public function dashboard()
    {
        /** @var User $renter */
        $renter = Auth::user()->load(['room.floor']);

        $invoices = RentInvoice::where('renter_id', $renter->id)
            ->with(['payment', 'latestDelayRequest'])
            ->latest('due_date')
            ->get();

        $currentInvoice = $invoices->firstWhere('status', '!=', 'paid') ?? $invoices->first();

        $pendingPayment = $currentInvoice ? $currentInvoice->payment : null;
        $pendingDelay = $currentInvoice ? $currentInvoice->latestDelayRequest : null;

        $activeForms = PgForm::where('is_active', true)->latest()->get();

        return view('renter.dashboard', compact('renter', 'invoices', 'currentInvoice', 'pendingPayment', 'pendingDelay', 'activeForms'));
    }

    public function updateProfileDetails(Request $request)
    {
        $validated = $request->validate([
            'father_name' => 'required|string|max:255',
            'mother_name' => 'nullable|string|max:255',
            'phone' => 'required|string|max:20',
            'emergency_contact' => 'required|string|max:20',
            'permanent_address' => 'required|string|max:1000',
            'college_office_name' => 'nullable|string|max:255',
            'blood_group' => 'nullable|string|max:10',
        ]);

        /** @var User $user */
        $user = Auth::user();
        $user->update($validated);

        return back()->with('success', 'Your personal & emergency contact details were updated successfully!');
    }

    public function uploadDocument(Request $request)
    {
        $validated = $request->validate([
            'id_type' => 'required|string|max:50',
            'id_number' => 'required|string|max:100',
            'id_proof' => 'required|file|mimes:jpeg,png,jpg,webp,pdf|max:5120',
        ]);

        /** @var User $user */
        $user = Auth::user();

        if ($user->id_proof_path) {
            Storage::disk('public')->delete($user->id_proof_path);
        }

        $idProofPath = $request->file('id_proof')->store('id_proofs', 'public');

        $user->update([
            'id_type' => $validated['id_type'],
            'id_number' => $validated['id_number'],
            'id_proof_path' => $idProofPath,
        ]);

        return back()->with('success', 'Identity Document (' . $validated['id_type'] . ') uploaded successfully!');
    }

    public function submitPayment(Request $request, RentInvoice $invoice)
    {
        if ($invoice->renter_id !== Auth::id()) {
            abort(403, 'Unauthorized action.');
        }

        $validated = $request->validate([
            'payment_method' => 'required|in:online,cash',
            'amount' => 'required|numeric|min:1',
            'payment_date' => 'required|date',
            'payment_time' => 'nullable|string',
            'transaction_id' => 'required_if:payment_method,online|nullable|string|max:100',
            'screenshot' => 'required_if:payment_method,online|nullable|image|mimes:jpeg,png,jpg,webp|max:5120',
            'cash_receiver_name' => 'required_if:payment_method,cash|nullable|string|max:255',
            'notes' => 'nullable|string',
        ]);

        $screenshotPath = null;
        if ($request->hasFile('screenshot')) {
            $screenshotPath = $request->file('screenshot')->store('payment_proofs', 'public');
        }

        RentPayment::updateOrCreate(
            ['rent_invoice_id' => $invoice->id],
            [
                'payment_method' => $validated['payment_method'],
                'amount' => $validated['amount'],
                'payment_date' => $validated['payment_date'],
                'payment_time' => $validated['payment_time'] ?? Carbon::now()->format('H:i'),
                'transaction_id' => $validated['transaction_id'] ?? null,
                'screenshot_path' => $screenshotPath ?? ($invoice->payment->screenshot_path ?? null),
                'cash_receiver_name' => $validated['cash_receiver_name'] ?? null,
                'notes' => $validated['notes'] ?? null,
                'status' => 'pending_approval',
                'rejection_reason' => null,
            ]
        );

        return back()->with('success', 'Payment proof submitted successfully! Awaiting Admin verification.');
    }

    public function submitDelayRequest(Request $request, RentInvoice $invoice)
    {
        if ($invoice->renter_id !== Auth::id()) {
            abort(403, 'Unauthorized action.');
        }

        $validated = $request->validate([
            'requested_date' => 'required|date|after:today',
            'reason' => 'required|string|min:10|max:1000',
        ]);

        DelayRequest::create([
            'rent_invoice_id' => $invoice->id,
            'renter_id' => Auth::id(),
            'requested_date' => $validated['requested_date'],
            'reason' => $validated['reason'],
            'status' => 'pending',
        ]);

        $invoice->update(['status' => 'delay_requested']);

        return back()->with('success', 'Delay payment request submitted! Admin will review your request.');
    }

    public function printReceipt(RentInvoice $invoice)
    {
        if (Auth::user()->isRenter() && $invoice->renter_id !== Auth::id()) {
            abort(403, 'Unauthorized action.');
        }

        $invoice->load(['renter.room.floor', 'payment']);
        return view('renter.receipt', compact('invoice'));
    }
}
