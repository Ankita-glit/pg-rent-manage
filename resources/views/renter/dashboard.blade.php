@extends('layouts.app')

@section('title', 'Resident Portal - Nice Stay Girls PG')

@section('content')
<div class="space-y-8 max-w-5xl mx-auto">
    <!-- Header Greeting & Living Duration -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div>
            <h1 class="text-3xl font-extrabold text-white tracking-tight">Welcome, {{ $renter->name }}!</h1>
            <div class="flex items-center space-x-2 mt-1">
                <span class="px-3 py-1 rounded-full text-xs font-bold bg-pink-500/20 text-pink-300 border border-pink-500/30 flex items-center space-x-1.5">
                    <i data-lucide="clock" class="w-3.5 h-3.5"></i>
                    <span>Living Duration in PG: {{ $renter->tenure_formatted }}</span>
                </span>
            </div>
        </div>

        <div class="flex items-center space-x-3">
            <button onclick="toggleModal('editProfileModal')" class="px-4 py-2 rounded-xl bg-slate-800 hover:bg-slate-700 text-slate-200 text-xs font-bold border border-slate-700 transition-all flex items-center space-x-2">
                <i data-lucide="user-cog" class="w-4 h-4"></i>
                <span>Edit Personal Details</span>
            </button>
            <span class="px-3.5 py-1.5 rounded-full text-xs font-bold bg-emerald-500/20 text-emerald-300 border border-emerald-500/40 flex items-center space-x-1.5">
                <span class="w-2.5 h-2.5 rounded-full bg-emerald-400 animate-pulse"></span>
                <span>Active Resident</span>
            </span>
        </div>
    </div>

    <!-- Room & Rent Details Cards -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
        <!-- Assigned Room -->
        <div class="glass-panel p-6 rounded-3xl border border-slate-800 space-y-3">
            <div class="flex items-center space-x-3 text-pink-400">
                <i data-lucide="home" class="w-5 h-5"></i>
                <span class="text-xs font-bold uppercase tracking-wider text-slate-400">Assigned Accommodation</span>
            </div>
            @if($renter->room)
            <div>
                <p class="text-2xl font-extrabold text-white">Room {{ $renter->room->room_number }}</p>
                <p class="text-xs text-slate-400">{{ $renter->room->floor->name ?? 'Floor' }} • {{ $renter->bed_number ?? 'Bed assigned' }}</p>
            </div>
            @else
            <p class="text-sm font-semibold text-rose-400">No room assigned</p>
            @endif
        </div>

        <!-- Monthly Rent -->
        <div class="glass-panel p-6 rounded-3xl border border-slate-800 space-y-3">
            <div class="flex items-center space-x-3 text-emerald-400">
                <i data-lucide="wallet" class="w-5 h-5"></i>
                <span class="text-xs font-bold uppercase tracking-wider text-slate-400">Monthly Rent Rate</span>
            </div>
            <div>
                <p class="text-2xl font-extrabold text-emerald-400">₹{{ number_format($renter->monthly_rent, 2) }}</p>
                <p class="text-xs text-slate-400">Due on the {{ $renter->rent_due_day }}th of every month</p>
            </div>
        </div>

        <!-- Security Deposit -->
        <div class="glass-panel p-6 rounded-3xl border border-slate-800 space-y-3">
            <div class="flex items-center space-x-3 text-indigo-400">
                <i data-lucide="shield-check" class="w-5 h-5"></i>
                <span class="text-xs font-bold uppercase tracking-wider text-slate-400">Security Deposit</span>
            </div>
            <div>
                <p class="text-2xl font-extrabold text-white">₹{{ number_format($renter->security_deposit, 2) }}</p>
                <p class="text-xs text-slate-400">Refundable on checkout</p>
            </div>
        </div>
    </div>

    <!-- Personal & Emergency Information Summary Card (Clean View with Edit Trigger) -->
    <div class="glass-panel p-6 rounded-3xl border border-slate-800 space-y-4">
        <div class="flex items-center justify-between border-b border-slate-800 pb-3">
            <div class="flex items-center space-x-3 text-pink-400">
                <i data-lucide="user-check" class="w-5 h-5"></i>
                <h3 class="text-sm font-bold uppercase tracking-wider text-slate-300">Personal & Family Details Summary</h3>
            </div>

            <button onclick="toggleModal('editProfileModal')" class="text-xs text-pink-400 hover:text-pink-300 font-bold flex items-center space-x-1">
                <i data-lucide="edit-3" class="w-3.5 h-3.5"></i>
                <span>Update Details</span>
            </button>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-3 gap-4 text-xs">
            <div class="p-3.5 rounded-2xl bg-slate-900 border border-slate-800">
                <span class="text-slate-500 block mb-1">Father's Name</span>
                <span class="font-bold text-white text-sm">{{ $renter->father_name ?? 'Click update to add' }}</span>
            </div>

            <div class="p-3.5 rounded-2xl bg-slate-900 border border-slate-800">
                <span class="text-slate-500 block mb-1">Mother's Name</span>
                <span class="font-bold text-white text-sm">{{ $renter->mother_name ?? 'Click update to add' }}</span>
            </div>

            <div class="p-3.5 rounded-2xl bg-slate-900 border border-slate-800">
                <span class="text-slate-500 block mb-1">Emergency Contact</span>
                <span class="font-bold text-rose-300 text-sm">{{ $renter->emergency_contact ?? 'Not Provided' }}</span>
            </div>

            <div class="p-3.5 rounded-2xl bg-slate-900 border border-slate-800">
                <span class="text-slate-500 block mb-1">Phone Number</span>
                <span class="font-semibold text-white">{{ $renter->phone }}</span>
            </div>

            <div class="p-3.5 rounded-2xl bg-slate-900 border border-slate-800">
                <span class="text-slate-500 block mb-1">College / Office</span>
                <span class="font-semibold text-indigo-300">{{ $renter->college_office_name ?? 'N/A' }}</span>
            </div>

            <div class="p-3.5 rounded-2xl bg-slate-900 border border-slate-800">
                <span class="text-slate-500 block mb-1">Blood Group</span>
                <span class="font-bold text-pink-400">{{ $renter->blood_group ?? 'N/A' }}</span>
            </div>

            <div class="p-3.5 rounded-2xl bg-slate-900 border border-slate-800 md:col-span-3">
                <span class="text-slate-500 block mb-1">Permanent Home Address</span>
                <span class="font-medium text-slate-200">{{ $renter->permanent_address ?? 'Click update details button to add your permanent home address' }}</span>
            </div>
        </div>
    </div>

    <!-- Renter Upload Govt ID Documents Summary / Status (File Upload Opens in Dedicated Modal) -->
    <div class="glass-panel p-6 rounded-3xl border border-slate-800 space-y-4">
        <div class="flex items-center justify-between">
            <div class="flex items-center space-x-3 text-pink-400">
                <i data-lucide="file-badge" class="w-6 h-6"></i>
                <div>
                    <h3 class="text-lg font-bold text-white">Identity Document & Verification Status</h3>
                    <p class="text-xs text-slate-400">Govt ID proof (Aadhaar, PAN, Passport) for PG tenancy verification</p>
                </div>
            </div>

            @if($renter->id_proof_path)
            <span class="px-3.5 py-1 rounded-full text-xs font-extrabold uppercase bg-emerald-500/20 text-emerald-300 border border-emerald-500/40 flex items-center space-x-1.5">
                <i data-lucide="check-circle" class="w-4 h-4"></i>
                <span>Document Uploaded</span>
            </span>
            @else
            <span class="px-3.5 py-1 rounded-full text-xs font-extrabold uppercase bg-amber-500/20 text-amber-300 border border-amber-500/40 flex items-center space-x-1.5">
                <i data-lucide="alert-circle" class="w-4 h-4"></i>
                <span>Upload Required</span>
            </span>
            @endif
        </div>

        @if($renter->id_proof_path)
        <!-- Document Uploaded Summary View (NO UPLOAD FORM SHOWN DIRECTLY ON DASHBOARD) -->
        <div class="p-5 rounded-2xl bg-slate-900 border border-slate-800 flex flex-col sm:flex-row sm:items-center justify-between gap-4">
            <div class="space-y-1">
                <div class="flex items-center space-x-2">
                    <span class="font-extrabold text-white text-base">{{ $renter->id_type ?? 'Govt ID Proof' }}</span>
                    <span class="px-2.5 py-0.5 rounded bg-pink-500/20 text-pink-300 text-[10px] font-bold">VERIFIED FORMAT</span>
                </div>
                <p class="text-xs text-slate-400 font-mono">ID Number: {{ $renter->id_number ?? 'Not specified' }}</p>
            </div>

            <div class="flex items-center space-x-3">
                <button type="button" onclick="openImageModal('{{ asset('storage/' . $renter->id_proof_path) }}', '{{ $renter->id_type }} Document')" class="px-4 py-2 rounded-xl bg-pink-600/20 hover:bg-pink-600/30 text-pink-300 border border-pink-500/30 font-bold text-xs flex items-center space-x-1.5">
                    <i data-lucide="eye" class="w-4 h-4"></i>
                    <span>Preview Document (PDF/Image)</span>
                </button>
                <button type="button" onclick="toggleModal('uploadDocumentModal')" class="px-4 py-2 rounded-xl bg-slate-800 hover:bg-slate-700 text-slate-200 border border-slate-700 font-bold text-xs flex items-center space-x-1.5">
                    <i data-lucide="upload" class="w-4 h-4"></i>
                    <span>Update Document</span>
                </button>
            </div>
        </div>
        @else
        <!-- Document Pending Callout -->
        <div class="p-5 rounded-2xl bg-amber-500/10 border border-amber-500/30 flex flex-col sm:flex-row sm:items-center justify-between gap-4">
            <div class="space-y-1">
                <h4 class="text-sm font-bold text-amber-200">Aadhaar Card or Govt ID Document Missing</h4>
                <p class="text-xs text-amber-300/80">Please upload your Aadhaar, PAN, Passport, or Driving License document photo or PDF for police verification records.</p>
            </div>
            <button type="button" onclick="toggleModal('uploadDocumentModal')" class="px-5 py-2.5 rounded-xl bg-amber-600 hover:bg-amber-500 text-white font-bold text-xs shadow flex items-center space-x-2 whitespace-nowrap">
                <i data-lucide="upload-cloud" class="w-4 h-4"></i>
                <span>Upload ID Document Now</span>
            </button>
        </div>
        @endif
    </div>

    <!-- Current Month Invoice & Action Forms -->
    @if($currentInvoice)
    <div class="glass-panel p-8 rounded-3xl border border-slate-800 space-y-6">
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 border-b border-slate-800 pb-4">
            <div>
                <span class="text-xs font-bold uppercase tracking-wider text-pink-400">Current Rent Invoice</span>
                <h2 class="text-2xl font-bold text-white">{{ $currentInvoice->month_year }} Rent Bill</h2>
                <p class="text-xs text-slate-400">Due Date: {{ $currentInvoice->due_date->format('d M Y') }}</p>
            </div>

            <div class="flex items-center space-x-3">
                <span class="text-2xl font-extrabold text-white">₹{{ number_format($currentInvoice->amount, 2) }}</span>
                <span class="px-3.5 py-1 rounded-full text-xs font-extrabold uppercase {{ $currentInvoice->status === 'paid' ? 'bg-emerald-500/20 text-emerald-300 border border-emerald-500/40' : ($currentInvoice->status === 'pending' ? 'bg-amber-500/20 text-amber-300 border border-amber-500/40' : 'bg-indigo-500/20 text-indigo-300 border border-indigo-500/40') }}">
                    {{ $currentInvoice->status }}
                </span>
            </div>
        </div>

        @if($currentInvoice->status === 'paid')
        <!-- Approved Payment Banner -->
        <div class="p-6 rounded-2xl bg-emerald-500/10 border border-emerald-500/30 flex items-center justify-between">
            <div class="flex items-center space-x-3">
                <i data-lucide="check-circle-2" class="w-8 h-8 text-emerald-400"></i>
                <div>
                    <h4 class="text-base font-bold text-emerald-200">Rent Payment Approved by Admin!</h4>
                    <p class="text-xs text-emerald-300/80">Your payment for {{ $currentInvoice->month_year }} has been verified and cleared.</p>
                </div>
            </div>
            <a href="{{ route('renter.receipt', $currentInvoice) }}" target="_blank" class="px-4 py-2 rounded-xl bg-emerald-600 hover:bg-emerald-500 text-white font-bold text-xs shadow flex items-center space-x-1">
                <i data-lucide="download" class="w-4 h-4"></i>
                <span>Download Receipt</span>
            </a>
        </div>
        @else

        <!-- Rejected Payment Description Warning Box (Displays when Admin rejects payment) -->
        @if($pendingPayment && $pendingPayment->status === 'rejected')
        <div class="p-5 rounded-2xl bg-rose-500/10 border border-rose-500/30 space-y-3">
            <div class="flex items-center space-x-2 text-rose-300 font-bold text-sm">
                <i data-lucide="x-circle" class="w-5 h-5 text-rose-400"></i>
                <span>Payment Proof Rejected by Admin</span>
            </div>
            <div class="p-4 rounded-xl bg-slate-900/90 border border-rose-500/20 space-y-1">
                <span class="text-xs font-bold uppercase tracking-wider text-rose-400 block">Admin Rejection Description / Reason:</span>
                <p class="text-sm text-rose-200 font-sans leading-relaxed">"{{ $pendingPayment->rejection_reason ?? 'Please provide a clear transaction receipt or valid UTR number.' }}"</p>
            </div>
            <p class="text-xs text-rose-300/90 font-medium">
                👉 Please review the reason above and re-submit a fresh payment proof or cash entry below.
            </p>
        </div>
        @elseif($pendingPayment && $pendingPayment->status === 'pending_approval')
        <!-- Pending Approval Banner -->
        <div class="p-4 rounded-2xl bg-amber-500/10 border border-amber-500/30 space-y-2">
            <div class="flex items-center space-x-2 text-amber-300 font-bold text-sm">
                <i data-lucide="clock" class="w-5 h-5"></i>
                <span>Payment Submission Under Review</span>
            </div>
            <p class="text-xs text-amber-200/90">
                You submitted a <strong>{{ $pendingPayment->payment_method }}</strong> payment proof of <strong>₹{{ number_format($pendingPayment->amount, 2) }}</strong> on {{ $pendingPayment->payment_date->format('d M Y') }}. Admin is verifying your transaction.
            </p>
        </div>
        @endif

        <!-- Delay Request Status Banner -->
        @if($pendingDelay)
        <div class="p-4 rounded-2xl bg-indigo-500/10 border border-indigo-500/30 space-y-2">
            <div class="flex items-center space-x-2 text-indigo-300 font-bold text-sm">
                <i data-lucide="calendar" class="w-5 h-5"></i>
                <span>Delay Extension Request Status: {{ strtoupper($pendingDelay->status) }}</span>
            </div>
            <p class="text-xs text-indigo-200/90">
                Requested Date: <strong>{{ $pendingDelay->requested_date->format('d M Y') }}</strong> • Reason: "{{ $pendingDelay->reason }}"
            </p>
            @if($pendingDelay->admin_comment)
            <p class="text-xs font-semibold text-indigo-300">Admin Response: {{ $pendingDelay->admin_comment }}</p>
            @endif
        </div>
        @endif

        <!-- Action Forms Tabs (Pay Rent vs Request Delay) -->
        <div class="pt-4" x-data="{ activeTab: 'pay' }">
            <div class="flex space-x-3 border-b border-slate-800 pb-3">
                <button onclick="switchTab('pay')" id="tab-pay" class="px-4 py-2 rounded-xl font-bold text-xs bg-pink-600 text-white shadow">
                    💳 Pay Rent / Submit Proof
                </button>
                <button onclick="switchTab('delay')" id="tab-delay" class="px-4 py-2 rounded-xl font-bold text-xs bg-slate-800 text-slate-300 hover:bg-slate-700">
                    ⏱️ Request Delay Approval
                </button>
            </div>

            <!-- Tab 1: Pay Rent Form -->
            <div id="content-pay" class="pt-6 space-y-6">
                <form id="payRentForm" action="{{ route('renter.pay', $currentInvoice) }}" method="POST" enctype="multipart/form-data" onsubmit="return confirmCrucialAction(event, 'Confirm Payment Proof Submission', 'Are you sure you want to submit this rent payment proof for Admin verification?')" class="space-y-6">
                    @csrf
                    <div>
                        <label class="block text-xs font-semibold uppercase text-slate-400 mb-2">Select Payment Method *</label>
                        <div class="grid grid-cols-2 gap-4">
                            <label class="p-4 rounded-2xl bg-slate-900 border border-slate-700 flex items-center space-x-3 cursor-pointer hover:border-pink-500">
                                <input type="radio" name="payment_method" value="online" checked onclick="togglePaymentFields('online')" class="text-pink-600">
                                <div>
                                    <span class="font-bold text-white text-sm block">Online Transfer</span>
                                    <span class="text-[11px] text-slate-400">Upload screenshot photo + Transaction ID</span>
                                </div>
                            </label>

                            <label class="p-4 rounded-2xl bg-slate-900 border border-slate-700 flex items-center space-x-3 cursor-pointer hover:border-purple-500">
                                <input type="radio" name="payment_method" value="cash" onclick="togglePaymentFields('cash')" class="text-purple-600">
                                <div>
                                    <span class="font-bold text-white text-sm block">Cash Handover</span>
                                    <span class="text-[11px] text-slate-400">Enter date, time & receiver staff name</span>
                                </div>
                            </label>
                        </div>
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                        <div>
                            <label class="block text-xs font-semibold uppercase text-slate-400 mb-1">Amount Paid (₹) *</label>
                            <input type="number" step="0.01" name="amount" required value="{{ $currentInvoice->amount }}" class="w-full px-4 py-2.5 rounded-xl bg-slate-900 border border-slate-700 text-white text-sm font-bold">
                        </div>

                        <div>
                            <label class="block text-xs font-semibold uppercase text-slate-400 mb-1">Payment Date *</label>
                            <input type="date" name="payment_date" required value="{{ date('Y-m-d') }}" class="w-full px-4 py-2.5 rounded-xl bg-slate-900 border border-slate-700 text-white text-sm">
                        </div>

                        <div>
                            <label class="block text-xs font-semibold uppercase text-slate-400 mb-1">Payment Time</label>
                            <input type="time" name="payment_time" value="{{ date('H:i') }}" class="w-full px-4 py-2.5 rounded-xl bg-slate-900 border border-slate-700 text-white text-sm">
                        </div>
                    </div>

                    <!-- Online Payment Specific Inputs -->
                    <div id="onlineFields" class="space-y-4 p-4 rounded-2xl bg-pink-500/5 border border-pink-500/20">
                        <div>
                            <label class="block text-xs font-semibold uppercase text-slate-400 mb-1">Transaction Ref / UTR Number *</label>
                            <input type="text" name="transaction_id" placeholder="e.g. UPI/9871236540/GPay or Bank Ref No." class="w-full px-4 py-2.5 rounded-xl bg-slate-900 border border-slate-700 text-white text-sm">
                        </div>

                        <div>
                            <label class="block text-xs font-semibold uppercase text-slate-400 mb-1">Upload Paying Screenshot Photo (or PDF Receipt) *</label>
                            <input type="file" name="screenshot" accept="image/*,application/pdf" class="w-full px-4 py-2 rounded-xl bg-slate-900 border border-slate-700 text-slate-300 text-xs">
                        </div>
                    </div>

                    <!-- Cash Payment Specific Inputs -->
                    <div id="cashFields" class="space-y-4 p-4 rounded-2xl bg-purple-500/5 border border-purple-500/20 hidden">
                        <div>
                            <label class="block text-xs font-semibold uppercase text-slate-400 mb-1">Name of Person / Staff Cash Handed To *</label>
                            <input type="text" name="cash_receiver_name" placeholder="e.g. Ramesh Kumar (Caretaker) or Super Admin" class="w-full px-4 py-2.5 rounded-xl bg-slate-900 border border-slate-700 text-white text-sm">
                        </div>
                    </div>

                    <div>
                        <label class="block text-xs font-semibold uppercase text-slate-400 mb-1">Additional Notes / Remarks</label>
                        <input type="text" name="notes" placeholder="e.g. Paid from HDFC bank account..." class="w-full px-4 py-2.5 rounded-xl bg-slate-900 border border-slate-700 text-white text-sm">
                    </div>

                    <button type="submit" class="w-full py-3.5 rounded-xl bg-gradient-to-r from-pink-600 via-rose-600 to-indigo-600 hover:from-pink-500 hover:to-indigo-500 text-white font-bold text-sm shadow-lg shadow-pink-500/30">
                        Submit Rent Payment Proof
                    </button>
                </form>
            </div>

            <!-- Tab 2: Request Delay Approval Form -->
            <div id="content-delay" class="pt-6 space-y-6 hidden">
                <form action="{{ route('renter.delay', $currentInvoice) }}" method="POST" onsubmit="return confirmCrucialAction(event, 'Confirm Delay Extension Request', 'Are you sure you want to submit this delay payment request to Admin?')" class="space-y-4">
                    @csrf
                    <div class="p-4 rounded-2xl bg-indigo-500/10 border border-indigo-500/30 text-xs text-indigo-200">
                        <i data-lucide="info" class="w-4 h-4 inline mr-1 text-indigo-400"></i>
                        Rent delay requests are submitted directly to the Admin for approval. Explain your reason clearly.
                    </div>

                    <div>
                        <label class="block text-xs font-semibold uppercase text-slate-400 mb-1">Requested Extension Date *</label>
                        <input type="date" name="requested_date" required min="{{ date('Y-m-d', strtotime('+1 day')) }}" value="{{ date('Y-m-d', strtotime('+10 days')) }}" class="w-full px-4 py-2.5 rounded-xl bg-slate-900 border border-slate-700 text-white text-sm">
                    </div>

                    <div>
                        <label class="block text-xs font-semibold uppercase text-slate-400 mb-1">Reason for Delay *</label>
                        <textarea name="reason" required placeholder="e.g. Salary from my employer is delayed until the 15th of this month. Kindly grant 10 days extension." class="w-full px-4 py-3 rounded-xl bg-slate-900 border border-slate-700 text-white text-sm h-32"></textarea>
                    </div>

                    <button type="submit" class="w-full py-3.5 rounded-xl bg-indigo-600 hover:bg-indigo-500 text-white font-bold text-sm shadow-lg shadow-indigo-500/30">
                        Send Delay Request for Admin Approval
                    </button>
                </form>
            </div>
        </div>
        @endif
    </div>
    @endif

    <!-- Admin Published Forms & Notices Section for Renters (e.g. Police Verification & Tenant Info Form 2026) -->
    @if($activeForms->count() > 0)
    <div class="glass-panel p-6 rounded-3xl border border-slate-800 space-y-6">
        <div class="flex items-center space-x-3 text-pink-400">
            <i data-lucide="file-text" class="w-6 h-6"></i>
            <div>
                <h3 class="text-lg font-bold text-white">Forms & Notices Published by Admin</h3>
                <p class="text-xs text-slate-400">View PDF/Image form templates published by Admin (e.g. Police Verification Form 2026) and submit your responses</p>
            </div>
        </div>

        <div class="space-y-6">
            @foreach($activeForms as $form)
            @php $userSubmission = $form->submissionForUser($renter->id); @endphp
            <div class="p-5 rounded-2xl bg-slate-900/90 border border-slate-800 space-y-4">
                <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-2 border-b border-slate-800/80 pb-3">
                    <div>
                        <h4 class="text-base font-bold text-white">{{ $form->title }}</h4>
                        <p class="text-xs text-slate-400">Published by Admin on {{ $form->created_at->format('d M Y') }}</p>
                    </div>

                    @if($userSubmission)
                    <span class="px-3 py-1 rounded-full text-xs font-bold uppercase {{ $userSubmission->status === 'approved' ? 'bg-emerald-500/20 text-emerald-300 border border-emerald-500/30' : 'bg-amber-500/20 text-amber-300 border border-amber-500/30' }}">
                        Submitted ({{ ucfirst($userSubmission->status) }})
                    </span>
                    @else
                    <span class="px-3 py-1 rounded-full text-xs font-bold uppercase bg-rose-500/20 text-rose-300 border border-rose-500/30">
                        Action Required
                    </span>
                    @endif
                </div>

                <p class="text-xs text-slate-300 leading-relaxed">{{ $form->description }}</p>

                <!-- Admin Uploaded Image or PDF Form Template Preview Box -->
                @if($form->template_file_path)
                @php
                    $isPdf = Str::endsWith(strtolower($form->template_file_path), '.pdf');
                    $formFileUrl = asset('storage/' . $form->template_file_path);
                @endphp
                <div class="p-4 rounded-2xl bg-slate-950/80 border border-pink-500/30 space-y-3">
                    <div class="flex items-center justify-between text-xs border-b border-slate-800 pb-2">
                        <div class="flex items-center space-x-2 text-pink-400 font-bold">
                            <i data-lucide="{{ $isPdf ? 'file-text' : 'image' }}" class="w-4 h-4"></i>
                            <span>Admin Published {{ $isPdf ? 'PDF Notice/Form File' : 'Form Image File' }}</span>
                        </div>
                        <a href="{{ $formFileUrl }}" target="_blank" class="text-xs text-pink-400 hover:underline font-semibold flex items-center space-x-1">
                            <i data-lucide="download" class="w-3.5 h-3.5"></i>
                            <span>Download Original</span>
                        </a>
                    </div>

                    @if(!$isPdf)
                    <!-- Image Preview Container -->
                    <div class="relative group cursor-pointer overflow-hidden rounded-xl bg-black/60 border border-slate-800 max-h-72 flex items-center justify-center" onclick="openImageModal('{{ $formFileUrl }}', '{{ $form->title }}')">
                        <img src="{{ $formFileUrl }}" alt="{{ $form->title }}" class="max-h-72 w-full object-contain rounded-xl">
                        <div class="absolute inset-0 bg-black/60 opacity-0 group-hover:opacity-100 transition-opacity flex items-center justify-center text-white text-xs font-bold space-x-2">
                            <i data-lucide="maximize-2" class="w-5 h-5"></i>
                            <span>Click to View Image Fullscreen</span>
                        </div>
                    </div>
                    @else
                    <!-- PDF File Box Preview -->
                    <div class="p-4 rounded-xl bg-pink-500/10 border border-pink-500/20 flex flex-col sm:flex-row sm:items-center justify-between gap-3">
                        <div class="flex items-center space-x-3">
                            <div class="w-10 h-10 rounded-xl bg-pink-600/30 flex items-center justify-center text-pink-300">
                                <i data-lucide="file-text" class="w-5 h-5"></i>
                            </div>
                            <div>
                                <span class="text-xs font-bold text-white block">{{ $form->title }} (PDF Document)</span>
                                <span class="text-[11px] text-pink-300/80">Click preview to view embedded PDF file viewer</span>
                            </div>
                        </div>
                        <button type="button" onclick="openImageModal('{{ $formFileUrl }}', '{{ $form->title }}')" class="px-4 py-2 rounded-xl bg-pink-600 hover:bg-pink-500 text-white font-bold text-xs shadow flex items-center space-x-1.5 shrink-0">
                            <i data-lucide="eye" class="w-4 h-4"></i>
                            <span>Preview PDF Fullscreen</span>
                        </button>
                    </div>
                    @endif
                </div>
                @endif

                <div class="flex items-center justify-end pt-2">
                    <button type="button" onclick="toggleModal('submitFormModal_{{ $form->id }}')" class="px-5 py-2.5 rounded-xl bg-gradient-to-r from-pink-600 to-indigo-600 hover:from-pink-500 hover:to-indigo-500 text-white text-xs font-bold flex items-center space-x-2 shadow-lg shadow-pink-500/20">
                        <i data-lucide="edit-3" class="w-4 h-4"></i>
                        <span>{{ $userSubmission ? 'View / Edit My Submission' : 'Fill & Submit Form Response' }}</span>
                    </button>
                </div>
            </div>
            @endforeach
        </div>
    </div>
    @endif

    <!-- Payment Receipts History -->
    <div class="glass-panel p-6 rounded-3xl border border-slate-800 space-y-4">
        <h3 class="text-lg font-bold text-white">Your Rent Payment History & Receipts</h3>

        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs">
                <thead class="text-slate-400 border-b border-slate-800 uppercase tracking-wider">
                    <tr>
                        <th class="pb-3">Month</th>
                        <th class="pb-3">Amount</th>
                        <th class="pb-3">Due Date</th>
                        <th class="pb-3">Status</th>
                        <th class="pb-3 text-right">Action</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-800/60">
                    @forelse($invoices as $inv)
                    <tr class="hover:bg-slate-800/30">
                        <td class="py-3 font-bold text-white">{{ $inv->month_year }}</td>
                        <td class="py-3 font-extrabold text-emerald-400">₹{{ number_format($inv->amount, 2) }}</td>
                        <td class="py-3 text-slate-300">{{ $inv->due_date->format('d M Y') }}</td>
                        <td class="py-3">
                            <span class="px-2.5 py-0.5 rounded-full text-[10px] font-bold uppercase {{ $inv->status === 'paid' ? 'bg-emerald-500/20 text-emerald-300' : 'bg-amber-500/20 text-amber-300' }}">
                                {{ $inv->status }}
                            </span>
                        </td>
                        <td class="py-3 text-right">
                            <a href="{{ route('renter.receipt', $inv) }}" target="_blank" class="text-pink-400 hover:underline font-semibold flex items-center justify-end space-x-1">
                                <i data-lucide="printer" class="w-3.5 h-3.5"></i>
                                <span>Receipt</span>
                            </a>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="5" class="py-4 text-center text-slate-500">No past invoice history.</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- Modal: Document Upload (Dedicated Modal so file input form does NOT show directly on dashboard) -->
<div id="uploadDocumentModal" class="fixed inset-0 z-50 hidden bg-black/85 backdrop-blur-sm flex items-center justify-center p-4">
    <div class="glass-panel p-6 rounded-3xl border border-slate-800 max-w-lg w-full space-y-4">
        <div class="flex items-center justify-between border-b border-slate-800 pb-3">
            <h3 class="text-lg font-bold text-white flex items-center space-x-2">
                <i data-lucide="upload-cloud" class="w-5 h-5 text-pink-400"></i>
                <span>Upload Aadhaar / Govt ID Proof</span>
            </h3>
            <button onclick="toggleModal('uploadDocumentModal')" class="text-slate-400 hover:text-white"><i data-lucide="x" class="w-5 h-5"></i></button>
        </div>

        <form action="{{ route('renter.upload-document') }}" method="POST" enctype="multipart/form-data" class="space-y-4">
            @csrf
            <div>
                <label class="block text-xs font-semibold uppercase text-slate-400 mb-1">Identity Document Type *</label>
                <select name="id_type" required class="w-full px-4 py-2.5 rounded-xl bg-slate-900 border border-slate-700 text-white text-sm">
                    <option value="Aadhaar Card" {{ $renter->id_type === 'Aadhaar Card' ? 'selected' : '' }}>Aadhaar Card</option>
                    <option value="PAN Card" {{ $renter->id_type === 'PAN Card' ? 'selected' : '' }}>PAN Card</option>
                    <option value="Passport" {{ $renter->id_type === 'Passport' ? 'selected' : '' }}>Passport</option>
                    <option value="Driving License" {{ $renter->id_type === 'Driving License' ? 'selected' : '' }}>Driving License</option>
                </select>
            </div>

            <div>
                <label class="block text-xs font-semibold uppercase text-slate-400 mb-1">Govt ID Card / Unique Number *</label>
                <input type="text" name="id_number" required value="{{ $renter->id_number }}" placeholder="e.g. 1234 5678 9012" class="w-full px-4 py-2.5 rounded-xl bg-slate-900 border border-slate-700 text-white text-sm">
            </div>

            <div>
                <label class="block text-xs font-semibold uppercase text-slate-400 mb-1">Upload Document Photo or PDF File *</label>
                <input type="file" name="id_proof" required accept="image/*,application/pdf" class="w-full px-3.5 py-2 rounded-xl bg-slate-900 border border-slate-700 text-slate-300 text-xs">
                <p class="text-[11px] text-slate-500 mt-1">Accepts JPG, PNG images or PDF documents (Max size: 5MB)</p>
            </div>

            <div class="pt-3 border-t border-slate-800 flex justify-end space-x-3">
                <button type="button" onclick="toggleModal('uploadDocumentModal')" class="px-5 py-2.5 rounded-xl bg-slate-800 hover:bg-slate-700 text-slate-300 font-semibold text-xs">Cancel</button>
                <button type="submit" class="px-6 py-2.5 rounded-xl bg-pink-600 hover:bg-pink-500 text-white font-bold text-xs shadow">
                    Upload & Save Document
                </button>
            </div>
        </form>
    </div>
</div>

<!-- Dedicated Modals for Each Admin Published Form (e.g. Police Verification & Tenant Information Form 2026) -->
@foreach($activeForms as $form)
@php $userSubmission = $form->submissionForUser($renter->id); @endphp
<div id="submitFormModal_{{ $form->id }}" class="fixed inset-0 z-50 hidden bg-black/85 backdrop-blur-sm flex items-center justify-center p-4">
    <div class="glass-panel p-6 rounded-3xl border border-slate-800 max-w-2xl w-full max-h-[90vh] overflow-y-auto space-y-5">
        <div class="flex items-center justify-between border-b border-slate-800 pb-3">
            <div>
                <h3 class="text-lg font-bold text-white flex items-center space-x-2">
                    <i data-lucide="file-check-2" class="w-5 h-5 text-pink-400"></i>
                    <span>{{ $form->title }}</span>
                </h3>
                <p class="text-xs text-slate-400">Fill response & submit form directly to Admin</p>
            </div>
            <button onclick="toggleModal('submitFormModal_{{ $form->id }}')" class="text-slate-400 hover:text-white"><i data-lucide="x" class="w-5 h-5"></i></button>
        </div>

        <!-- Form Description & Preview Option -->
        <div class="p-4 rounded-2xl bg-slate-900 border border-slate-800 space-y-3">
            <p class="text-xs text-slate-300 leading-relaxed">{{ $form->description }}</p>

            @if($form->template_file_path)
            @php
                $isPdfModal = Str::endsWith(strtolower($form->template_file_path), '.pdf');
                $modalFileUrl = asset('storage/' . $form->template_file_path);
            @endphp
            <div class="p-3 rounded-xl bg-pink-500/10 border border-pink-500/20 space-y-2 text-xs">
                <div class="flex items-center justify-between">
                    <div class="flex items-center space-x-2 text-pink-300 font-semibold">
                        <i data-lucide="paperclip" class="w-4 h-4"></i>
                        <span>Admin Form Document Template ({{ $isPdfModal ? 'PDF' : 'Image' }})</span>
                    </div>
                    <div class="flex items-center space-x-2">
                        <button type="button" onclick="openImageModal('{{ $modalFileUrl }}', '{{ $form->title }}')" class="px-3 py-1.5 rounded-lg bg-pink-600 text-white font-bold text-[11px] shadow">
                            Preview Fullscreen
                        </button>
                        <a href="{{ $modalFileUrl }}" target="_blank" class="px-3 py-1.5 rounded-lg bg-slate-800 hover:bg-slate-700 text-slate-200 font-bold text-[11px] border border-slate-700">
                            Download
                        </a>
                    </div>
                </div>

                @if(!$isPdfModal)
                <div class="mt-2 rounded-xl overflow-hidden bg-black/50 border border-slate-800 max-h-48 flex items-center justify-center">
                    <img src="{{ $modalFileUrl }}" alt="{{ $form->title }}" class="max-h-48 object-contain">
                </div>
                @endif
            </div>
            @endif
        </div>

        @if($userSubmission)
        <!-- Submission Status Banner -->
        <div class="p-4 rounded-2xl bg-emerald-500/10 border border-emerald-500/30 text-xs text-emerald-200 space-y-1">
            <div class="flex items-center space-x-2 font-bold">
                <i data-lucide="check-circle" class="w-4 h-4 text-emerald-400"></i>
                <span>Form Previously Submitted on {{ $userSubmission->created_at->format('d M Y, h:i A') }}</span>
            </div>
            <p class="text-emerald-300/80">Status: <strong>{{ ucfirst($userSubmission->status) }}</strong></p>
            @if($userSubmission->submitted_file_path)
            <div class="pt-1">
                <button type="button" onclick="openImageModal('{{ asset('storage/' . $userSubmission->submitted_file_path) }}', 'Your Submitted File')" class="text-pink-400 underline font-semibold">
                    View Your Previously Uploaded Response File
                </button>
            </div>
            @endif
        </div>
        @endif

        <!-- Response Submission Form -->
        <form action="{{ route('renter.forms.submit', $form) }}" method="POST" enctype="multipart/form-data" class="space-y-4 pt-2">
            @csrf
            <div>
                <label class="block text-xs font-semibold uppercase text-slate-400 mb-1">Your Response Notes / Tenant Details / Remarks *</label>
                <textarea name="response_notes" rows="4" placeholder="Enter police verification details, permanent address, local guardian info or response requested by admin..." class="w-full px-4 py-3 rounded-xl bg-slate-950 border border-slate-700 text-white text-sm leading-relaxed">{{ old('response_notes', $userSubmission->response_notes ?? '') }}</textarea>
            </div>

            <div>
                <label class="block text-xs font-semibold uppercase text-slate-400 mb-1">Attach Completed/Signed Document File (PDF or Image)</label>
                <input type="file" name="submitted_file" accept=".pdf,.doc,.docx,.jpg,.jpeg,.png" class="w-full px-3.5 py-2 rounded-xl bg-slate-950 border border-slate-700 text-slate-300 text-xs">
                <p class="text-[11px] text-slate-500 mt-1">Attach completed Police Verification PDF or photos if required by admin.</p>
            </div>

            <div class="pt-3 border-t border-slate-800 flex justify-end space-x-3">
                <button type="button" onclick="toggleModal('submitFormModal_{{ $form->id }}')" class="px-5 py-2.5 rounded-xl bg-slate-800 hover:bg-slate-700 text-slate-300 font-semibold text-xs">Close</button>
                <button type="submit" class="px-6 py-2.5 rounded-xl bg-indigo-600 hover:bg-indigo-500 text-white font-bold text-xs shadow-lg">
                    {{ $userSubmission ? 'Update Submission' : 'Submit Form Response to Admin' }}
                </button>
            </div>
        </form>
    </div>
</div>
@endforeach

<!-- Modal: Edit Personal & Family Details (Opens on clicking "Edit Personal Details" button) -->
<div id="editProfileModal" class="fixed inset-0 z-50 hidden bg-black/85 backdrop-blur-sm flex items-center justify-center p-4">
    <div class="glass-panel p-6 rounded-3xl border border-slate-800 max-w-2xl w-full max-h-[90vh] overflow-y-auto space-y-4">
        <div class="flex items-center justify-between border-b border-slate-800 pb-3">
            <h3 class="text-lg font-bold text-white flex items-center space-x-2">
                <i data-lucide="user-edit" class="w-5 h-5 text-pink-400"></i>
                <span>Update Personal, Family & Address Details</span>
            </h3>
            <button onclick="toggleModal('editProfileModal')" class="text-slate-400 hover:text-white"><i data-lucide="x" class="w-5 h-5"></i></button>
        </div>

        <form action="{{ route('renter.update-profile') }}" method="POST" class="space-y-4">
            @csrf
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <label class="block text-xs font-semibold uppercase text-slate-400 mb-1">Father's Full Name *</label>
                    <input type="text" name="father_name" required value="{{ old('father_name', $renter->father_name) }}" placeholder="e.g. Suresh Roy" class="w-full px-4 py-2.5 rounded-xl bg-slate-900 border border-slate-700 text-white text-sm font-bold">
                </div>

                <div>
                    <label class="block text-xs font-semibold uppercase text-slate-400 mb-1">Mother's Name (Optional)</label>
                    <input type="text" name="mother_name" value="{{ old('mother_name', $renter->mother_name) }}" placeholder="e.g. Sunita Roy" class="w-full px-4 py-2.5 rounded-xl bg-slate-900 border border-slate-700 text-white text-sm">
                </div>

                <div>
                    <label class="block text-xs font-semibold uppercase text-slate-400 mb-1">Emergency Contact Phone & Relation *</label>
                    <input type="text" name="emergency_contact" required value="{{ old('emergency_contact', $renter->emergency_contact) }}" placeholder="+91 98765 00000 (Father)" class="w-full px-4 py-2.5 rounded-xl bg-slate-900 border border-slate-700 text-rose-300 text-sm font-bold">
                </div>

                <div>
                    <label class="block text-xs font-semibold uppercase text-slate-400 mb-1">Mobile Phone Number *</label>
                    <input type="text" name="phone" required value="{{ old('phone', $renter->phone) }}" placeholder="+91 98765 43210" class="w-full px-4 py-2.5 rounded-xl bg-slate-900 border border-slate-700 text-white text-sm">
                </div>

                <div>
                    <label class="block text-xs font-semibold uppercase text-slate-400 mb-1">College / Office Name</label>
                    <input type="text" name="college_office_name" value="{{ old('college_office_name', $renter->college_office_name) }}" placeholder="e.g. St. Xavier College" class="w-full px-4 py-2.5 rounded-xl bg-slate-900 border border-slate-700 text-white text-sm">
                </div>

                <div>
                    <label class="block text-xs font-semibold uppercase text-slate-400 mb-1">Blood Group</label>
                    <input type="text" name="blood_group" value="{{ old('blood_group', $renter->blood_group) }}" placeholder="e.g. B+, O+, AB-" class="w-full px-4 py-2.5 rounded-xl bg-slate-900 border border-slate-700 text-white text-sm">
                </div>

                <div class="sm:col-span-2">
                    <label class="block text-xs font-semibold uppercase text-slate-400 mb-1">Permanent Home Address *</label>
                    <textarea name="permanent_address" required placeholder="Enter full permanent home address with city, state & pincode..." class="w-full px-4 py-2.5 rounded-xl bg-slate-900 border border-slate-700 text-white text-sm h-20">{{ old('permanent_address', $renter->permanent_address) }}</textarea>
                </div>
            </div>

            <div class="pt-3 border-t border-slate-800 flex justify-end space-x-3">
                <button type="button" onclick="toggleModal('editProfileModal')" class="px-5 py-2.5 rounded-xl bg-slate-800 hover:bg-slate-700 text-slate-300 font-semibold text-xs">Cancel</button>
                <button type="submit" class="px-6 py-2.5 rounded-xl bg-pink-600 hover:bg-pink-500 text-white font-bold text-xs shadow-lg shadow-pink-500/25">
                    Save Details & Return to Dashboard
                </button>
            </div>
        </form>
    </div>
</div>

<script>
    function toggleModal(id) {
        const modal = document.getElementById(id);
        if (modal) {
            modal.classList.toggle('hidden');
        }
    }

    function switchTab(tab) {
        if (tab === 'pay') {
            document.getElementById('content-pay').classList.remove('hidden');
            document.getElementById('content-delay').classList.add('hidden');
            document.getElementById('tab-pay').className = 'px-4 py-2 rounded-xl font-bold text-xs bg-pink-600 text-white shadow';
            document.getElementById('tab-delay').className = 'px-4 py-2 rounded-xl font-bold text-xs bg-slate-800 text-slate-300 hover:bg-slate-700';
        } else {
            document.getElementById('content-pay').classList.add('hidden');
            document.getElementById('content-delay').classList.remove('hidden');
            document.getElementById('tab-pay').className = 'px-4 py-2 rounded-xl font-bold text-xs bg-slate-800 text-slate-300 hover:bg-slate-700';
            document.getElementById('tab-delay').className = 'px-4 py-2 rounded-xl font-bold text-xs bg-indigo-600 text-white shadow';
        }
    }

    function togglePaymentFields(method) {
        if (method === 'online') {
            document.getElementById('onlineFields').classList.remove('hidden');
            document.getElementById('cashFields').classList.add('hidden');
        } else {
            document.getElementById('onlineFields').classList.add('hidden');
            document.getElementById('cashFields').classList.remove('hidden');
        }
    }
</script>
@endsection
