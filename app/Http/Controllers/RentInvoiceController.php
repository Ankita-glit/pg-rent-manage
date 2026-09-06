<?php

namespace App\Http\Controllers;

use App\Models\RentInvoice;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;

class RentInvoiceController extends Controller
{
    public function index(Request $request)
    {
        $query = RentInvoice::with(['renter.room.floor', 'payment', 'latestDelayRequest']);

        if ($request->filled('month')) {
            $query->where('month_year', $request->month);
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('search')) {
            $search = $request->search;
            $query->whereHas('renter', function($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%");
            });
        }

        $invoices = $query->latest()->paginate(15);
        $selectedMonth = $request->month ?? Carbon::now()->format('Y-m');

        return view('admin.invoices.index', compact('invoices', 'selectedMonth'));
    }

    public function generateMonthlyInvoices(Request $request)
    {
        $request->validate([
            'month_year' => 'required|string|regex:/^\d{4}-\d{2}$/',
        ]);

        $monthYear = $request->month_year;
        $dateObj = Carbon::createFromFormat('Y-m', $monthYear);

        $activeRenters = User::where('role', 'renter')
            ->where('status', 'active')
            ->whereNotNull('assigned_room_id')
            ->get();

        $generatedCount = 0;

        foreach ($activeRenters as $renter) {
            // Check if invoice already generated for this month
            $exists = RentInvoice::where('renter_id', $renter->id)
                ->where('month_year', $monthYear)
                ->exists();

            if (!$exists) {
                $dueDate = Carbon::create($dateObj->year, $dateObj->month, min($renter->rent_due_day, 28));

                RentInvoice::create([
                    'renter_id' => $renter->id,
                    'room_id' => $renter->assigned_room_id,
                    'month_year' => $monthYear,
                    'amount' => $renter->monthly_rent ?? ($renter->room ? $renter->room->monthly_price : 0),
                    'due_date' => $dueDate,
                    'status' => 'pending',
                ]);

                $generatedCount++;
            }
        }

        return back()->with('success', "Generated {$generatedCount} rent invoices for " . $dateObj->format('F Y'));
    }

    public function show(RentInvoice $invoice)
    {
        $invoice->load(['renter.room.floor', 'payment.verifiedByAdmin', 'delayRequests']);
        return view('admin.invoices.show', compact('invoice'));
    }
}
