<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Rent Receipt - #INV-{{ str_pad($invoice->id, 5, '0', STR_PAD_LEFT) }}</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <style>
        body { font-family: 'Inter', sans-serif; }
    </style>
</head>
<body class="bg-slate-100 text-slate-800 py-10 print:py-0 print:bg-white">

    <div class="max-w-2xl mx-auto bg-white p-8 rounded-2xl shadow-xl border border-slate-200 print:shadow-none print:border-none print:rounded-none">
        
        <!-- Header -->
        <div class="flex justify-between items-start border-b border-slate-200 pb-6 mb-6">
            <div>
                <h1 class="text-2xl font-extrabold text-pink-600">Nice Stay Girls PG</h1>
                <p class="text-xs text-slate-500">Official Rent Payment Receipt</p>
                <p class="text-xs text-slate-500">123 PG Heights, Main Road, City</p>
            </div>
            <div class="text-right">
                <span class="inline-block px-3 py-1 bg-emerald-100 text-emerald-800 text-xs font-bold rounded-full uppercase mb-1">
                    {{ $invoice->status === 'paid' ? 'PAID RECEIPT' : 'INVOICE DEMAND' }}
                </span>
                <p class="text-xs font-mono font-bold text-slate-700">Receipt No: #INV-{{ str_pad($invoice->id, 5, '0', STR_PAD_LEFT) }}</p>
                <p class="text-xs text-slate-500">Date: {{ date('d M Y') }}</p>
            </div>
        </div>

        <!-- Tenant & Room Details -->
        <div class="grid grid-cols-2 gap-4 text-xs mb-6 p-4 bg-slate-50 rounded-xl border border-slate-200">
            <div>
                <span class="text-slate-400 block font-semibold uppercase">Received From (Resident)</span>
                <span class="font-bold text-slate-900 text-sm block">{{ $invoice->renter->name ?? 'N/A' }}</span>
                @if($invoice->renter->father_name)
                <span class="text-slate-600 block">Father's Name: {{ $invoice->renter->father_name }}</span>
                @endif
                <span class="text-slate-600 block">{{ $invoice->renter->email }}</span>
                <span class="text-slate-600 block">Phone: {{ $invoice->renter->phone }}</span>
                @if($invoice->renter->emergency_contact)
                <span class="text-slate-600 block">Emergency: {{ $invoice->renter->emergency_contact }}</span>
                @endif
            </div>
            <div class="text-right">
                <span class="text-slate-400 block font-semibold uppercase">Accommodation Details</span>
                <span class="font-bold text-slate-900 text-sm block">Room {{ $invoice->room->room_number ?? 'N/A' }}</span>
                <span class="text-slate-600 block">{{ $invoice->room->floor->name ?? 'Floor' }}</span>
                <span class="text-slate-600 block">Seat: {{ $invoice->renter->bed_number ?? 'Bed 1' }}</span>
                <span class="text-slate-600 block">Stay: {{ $invoice->renter->tenure_duration }}</span>
            </div>
        </div>

        <!-- Invoice Particulars -->
        <table class="w-full text-xs text-left mb-6">
            <thead class="bg-slate-100 text-slate-600 uppercase border-b border-slate-200 font-bold">
                <tr>
                    <th class="py-2.5 px-3">Description</th>
                    <th class="py-2.5 px-3">Month</th>
                    <th class="py-2.5 px-3 text-right">Amount</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-200">
                <tr>
                    <td class="py-3 px-3">
                        <span class="font-bold text-slate-800">Monthly PG Accommodation Rent</span>
                        <span class="block text-[11px] text-slate-500">Includes lodging, electricity & water utilities</span>
                    </td>
                    <td class="py-3 px-3 font-semibold">{{ $invoice->month_year }}</td>
                    <td class="py-3 px-3 text-right font-bold text-slate-900">₹{{ number_format($invoice->amount, 2) }}</td>
                </tr>
            </tbody>
        </table>

        <!-- Payment Mode & Summary -->
        <div class="border-t-2 border-slate-900 pt-4 flex justify-between items-end mb-8">
            <div class="text-xs space-y-1">
                @if($invoice->payment)
                <p><strong class="text-slate-700">Payment Mode:</strong> {{ ucfirst($invoice->payment->payment_method) }}</p>
                <p><strong class="text-slate-700">Ref / Details:</strong> {{ $invoice->payment->transaction_id ?? $invoice->payment->cash_receiver_name }}</p>
                <p><strong class="text-slate-700">Payment Date:</strong> {{ $invoice->payment->payment_date->format('d M Y') }}</p>
                @endif
            </div>

            <div class="text-right">
                <span class="text-xs text-slate-500 font-bold uppercase block">Total Paid</span>
                <span class="text-2xl font-extrabold text-emerald-600">₹{{ number_format($invoice->amount, 2) }}</span>
            </div>
        </div>

        <!-- Signature Footer -->
        <div class="flex justify-between items-end text-xs pt-8 border-t border-slate-200">
            <div class="text-slate-400">
                <p>Computer generated receipt.</p>
                <p>Thank you for staying at Nice Stay Girls PG!</p>
            </div>
            <div class="text-center">
                <div class="w-32 border-b border-slate-400 mb-1"></div>
                <span class="font-bold text-slate-700">Authorized Signatory</span>
            </div>
        </div>

        <!-- Print Action Button -->
        <div class="mt-8 text-center print:hidden">
            <button onclick="window.print()" class="px-6 py-2.5 bg-pink-600 hover:bg-pink-500 text-white font-bold text-xs rounded-xl shadow">
                🖨️ Print Receipt / Save PDF
            </button>
        </div>

    </div>

</body>
</html>
