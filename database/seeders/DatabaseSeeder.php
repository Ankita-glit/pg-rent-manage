<?php

namespace Database\Seeders;

use App\Models\DelayRequest;
use App\Models\Expense;
use App\Models\Floor;
use App\Models\PgForm;
use App\Models\RentInvoice;
use App\Models\RentPayment;
use App\Models\RenterFormSubmission;
use App\Models\Room;
use App\Models\Staff;
use App\Models\StaffSalary;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // 1. Create Admin Account
        $admin = User::create([
            'name' => 'Super Admin',
            'email' => 'admin@pgmanage.com',
            'password' => Hash::make('password123'),
            'role' => 'admin',
            'phone' => '+91 98765 43210',
            'emergency_contact' => '+91 98765 43211',
            'status' => 'active',
        ]);

        // 2. Create Floors
        $groundFloor = Floor::create([
            'floor_number' => 0,
            'name' => 'Ground Floor',
            'description' => 'Reception, Dining Hall & Standard Deluxe Rooms',
        ]);

        $firstFloor = Floor::create([
            'floor_number' => 1,
            'name' => '1st Floor',
            'description' => 'Premium Single & Double Occupancy AC Rooms',
        ]);

        $secondFloor = Floor::create([
            'floor_number' => 2,
            'name' => '2nd Floor',
            'description' => 'Executive Suites & Terrace Access Rooms',
        ]);

        // 3. Create Rooms with different prices
        $room101 = Room::create([
            'floor_id' => $groundFloor->id,
            'room_number' => 'G-101',
            'bed_capacity' => 2,
            'monthly_price' => 6000.00,
            'status' => 'occupied',
            'description' => 'Non-AC Double Sharing with attached bath',
        ]);

        $room102 = Room::create([
            'floor_id' => $groundFloor->id,
            'room_number' => 'G-102',
            'bed_capacity' => 3,
            'monthly_price' => 5000.00,
            'status' => 'occupied',
            'description' => 'Triple Sharing Economy Room',
        ]);

        $room201 = Room::create([
            'floor_id' => $firstFloor->id,
            'room_number' => 'F-201',
            'bed_capacity' => 2,
            'monthly_price' => 8500.00,
            'status' => 'occupied',
            'description' => 'AC Deluxe Double Sharing with Work Desk',
        ]);

        $room202 = Room::create([
            'floor_id' => $firstFloor->id,
            'room_number' => 'F-202',
            'bed_capacity' => 1,
            'monthly_price' => 12000.00,
            'status' => 'occupied',
            'description' => 'AC Premium Single Private Room',
        ]);

        $room301 = Room::create([
            'floor_id' => $secondFloor->id,
            'room_number' => 'S-301',
            'bed_capacity' => 2,
            'monthly_price' => 9500.00,
            'status' => 'occupied',
            'description' => 'AC Luxury Room with Balcony View',
        ]);

        $room302 = Room::create([
            'floor_id' => $secondFloor->id,
            'room_number' => 'S-302',
            'bed_capacity' => 2,
            'monthly_price' => 9500.00,
            'status' => 'available',
            'description' => 'AC Luxury Room with Balcony View',
        ]);

        // 4. Create Staff Members
        $caretaker = Staff::create([
            'name' => 'Ramesh Kumar',
            'designation' => 'Resident Warden & Caretaker',
            'phone' => '+91 91234 56789',
            'email' => 'ramesh@pgmanage.com',
            'monthly_salary' => 18000.00,
            'joining_date' => Carbon::create('2025', '01', '10'),
            'status' => 'active',
        ]);

        $cleaningStaff = Staff::create([
            'name' => 'Suresh Sharma',
            'designation' => 'Housekeeping & Maintenance',
            'phone' => '+91 91234 56790',
            'email' => 'suresh@pgmanage.com',
            'monthly_salary' => 12000.00,
            'joining_date' => Carbon::create('2025', '02', '01'),
            'status' => 'active',
        ]);

        $security = Staff::create([
            'name' => 'Bahadur Singh',
            'designation' => 'Night Security Guard',
            'phone' => '+91 91234 56791',
            'email' => null,
            'monthly_salary' => 14000.00,
            'joining_date' => Carbon::create('2025', '01', '15'),
            'status' => 'active',
        ]);

        // Staff Salary Payments
        StaffSalary::create([
            'staff_id' => $caretaker->id,
            'amount' => 18000.00,
            'payment_date' => Carbon::now()->startOfMonth()->addDays(2),
            'payment_method' => 'bank_transfer',
            'notes' => 'Salary payout for ' . Carbon::now()->format('F Y'),
        ]);

        StaffSalary::create([
            'staff_id' => $cleaningStaff->id,
            'amount' => 12000.00,
            'payment_date' => Carbon::now()->startOfMonth()->addDays(2),
            'payment_method' => 'cash',
            'notes' => 'Cash salary paid by Admin',
        ]);

        // 5. Create Renters (Accounts created by Admin)
        $renter1 = User::create([
            'name' => 'Rahul Verma',
            'email' => 'rahul@example.com',
            'password' => Hash::make('password123'),
            'role' => 'renter',
            'phone' => '+91 98111 22233',
            'emergency_contact' => '+91 98111 99999 (Father)',
            'id_type' => 'Aadhaar Card',
            'id_number' => '4532 8901 2345',
            'id_proof_path' => 'id_proofs/sample_aadhaar.png',
            'joining_date' => Carbon::create('2025', '06', '01'),
            'status' => 'active',
            'assigned_room_id' => $room201->id,
            'bed_number' => 'Bed 1 (Window Side)',
            'monthly_rent' => 8500.00,
            'security_deposit' => 10000.00,
            'rent_due_day' => 5,
        ]);

        $renter2 = User::create([
            'name' => 'Ananya Roy',
            'email' => 'ananya@example.com',
            'password' => Hash::make('password123'),
            'role' => 'renter',
            'phone' => '+91 98222 33344',
            'emergency_contact' => '+91 98222 88888 (Mother)',
            'id_type' => 'PAN Card',
            'id_number' => 'ABCDE1234F',
            'joining_date' => Carbon::create('2025', '07', '15'),
            'status' => 'active',
            'assigned_room_id' => $room202->id,
            'bed_number' => 'Bed 1 (Single)',
            'monthly_rent' => 12000.00,
            'security_deposit' => 15000.00,
            'rent_due_day' => 5,
        ]);

        $renter3 = User::create([
            'name' => 'Vikas Patel',
            'email' => 'vikas@example.com',
            'password' => Hash::make('password123'),
            'role' => 'renter',
            'phone' => '+91 98333 44455',
            'emergency_contact' => '+91 98333 77777 (Brother)',
            'id_type' => 'Aadhaar Card',
            'id_number' => '8901 2345 6789',
            'joining_date' => Carbon::create('2025', '08', '01'),
            'status' => 'active',
            'assigned_room_id' => $room101->id,
            'bed_number' => 'Bed 1',
            'monthly_rent' => 6000.00,
            'security_deposit' => 8000.00,
            'rent_due_day' => 7,
        ]);

        $renter4 = User::create([
            'name' => 'Priya Sharma',
            'email' => 'priya@example.com',
            'password' => Hash::make('password123'),
            'role' => 'renter',
            'phone' => '+91 98444 55566',
            'emergency_contact' => '+91 98444 66666 (Father)',
            'id_type' => 'Passport',
            'id_number' => 'Z9876543',
            'joining_date' => Carbon::create('2025', '08', '10'),
            'status' => 'active',
            'assigned_room_id' => $room301->id,
            'bed_number' => 'Bed 2',
            'monthly_rent' => 9500.00,
            'security_deposit' => 10000.00,
            'rent_due_day' => 5,
        ]);

        // 6. Admin Published Forms for Renters
        $form1 = PgForm::create([
            'title' => 'Police Verification & Tenant Information Form 2026',
            'description' => 'Please fill out your local office/college address, permanent home address, and emergency contact details for city police tenant verification.',
            'template_file_path' => 'admin_forms/police_verification_form_2026.png',
            'due_date' => Carbon::now()->addDays(15),
            'is_active' => true,
        ]);

        RenterFormSubmission::create([
            'pg_form_id' => $form1->id,
            'renter_id' => $renter1->id,
            'response_notes' => 'Permanent Address: 42 MG Road, Sector 4, Bangalore. Office: Tech Park Bldg 3.',
            'status' => 'approved',
            'admin_feedback' => 'Verified by Admin.',
        ]);

        // 7. Rent Invoices & Payments

        // Current Month Invoice (Paid via Online Screenshot Proof) - Renter 1
        $inv1 = RentInvoice::create([
            'renter_id' => $renter1->id,
            'room_id' => $renter1->assigned_room_id,
            'month_year' => Carbon::now()->format('Y-m'),
            'amount' => 8500.00,
            'due_date' => Carbon::now()->startOfMonth()->addDays(4),
            'status' => 'paid',
            'paid_at' => Carbon::now()->startOfMonth()->addDays(3),
        ]);

        RentPayment::create([
            'rent_invoice_id' => $inv1->id,
            'payment_method' => 'online',
            'amount' => 8500.00,
            'payment_date' => Carbon::now()->startOfMonth()->addDays(3),
            'payment_time' => '11:45',
            'transaction_id' => 'UPI/984712039481/GPay',
            'notes' => 'Paid via GPay UPI transfer.',
            'status' => 'approved',
            'verified_by' => $admin->id,
        ]);

        // Current Month Invoice (Pending Approval via Cash Submission) - Renter 3
        $inv3 = RentInvoice::create([
            'renter_id' => $renter3->id,
            'room_id' => $renter3->assigned_room_id,
            'month_year' => Carbon::now()->format('Y-m'),
            'amount' => 6000.00,
            'due_date' => Carbon::now()->startOfMonth()->addDays(6),
            'status' => 'pending',
        ]);

        RentPayment::create([
            'rent_invoice_id' => $inv3->id,
            'payment_method' => 'cash',
            'amount' => 6000.00,
            'payment_date' => Carbon::now()->startOfMonth()->addDays(4),
            'payment_time' => '17:30',
            'cash_receiver_name' => 'Ramesh Kumar (Caretaker)',
            'notes' => 'Handed cash payment to caretaker at reception desk.',
            'status' => 'pending_approval',
        ]);

        // Current Month Invoice (Rejected Payment Proof with Description) - Renter 4
        $inv4 = RentInvoice::create([
            'renter_id' => $renter4->id,
            'room_id' => $renter4->assigned_room_id,
            'month_year' => Carbon::now()->format('Y-m'),
            'amount' => 9500.00,
            'due_date' => Carbon::now()->startOfMonth()->addDays(4),
            'status' => 'pending',
        ]);

        RentPayment::create([
            'rent_invoice_id' => $inv4->id,
            'payment_method' => 'online',
            'amount' => 9500.00,
            'payment_date' => Carbon::now()->startOfMonth()->addDays(2),
            'payment_time' => '14:20',
            'transaction_id' => 'INVALID_UTR_123',
            'notes' => 'Tried paying online.',
            'status' => 'rejected',
            'rejection_reason' => 'Transaction UTR ID not found on bank statement. Uploaded screenshot is blurry. Please upload a clear receipt with valid UTR number.',
            'verified_by' => $admin->id,
        ]);

        // Current Month Invoice (Delay Requested) - Renter 2
        $inv2 = RentInvoice::create([
            'renter_id' => $renter2->id,
            'room_id' => $renter2->assigned_room_id,
            'month_year' => Carbon::now()->format('Y-m'),
            'amount' => 12000.00,
            'due_date' => Carbon::now()->startOfMonth()->addDays(4),
            'status' => 'delay_requested',
        ]);

        DelayRequest::create([
            'rent_invoice_id' => $inv2->id,
            'renter_id' => $renter2->id,
            'requested_date' => Carbon::now()->startOfMonth()->addDays(15),
            'reason' => 'Salary for this month is delayed from company HR until the 14th.',
            'status' => 'pending',
        ]);

        // Expenses
        Expense::create([
            'category' => 'repair_maintenance',
            'title' => 'Geyser Repair & Plumbing Work in Room G-101',
            'amount' => 2500.00,
            'expense_date' => Carbon::now()->startOfMonth()->addDays(1),
            'notes' => 'Replaced heating element and fixed bathroom valve.',
        ]);

        Expense::create([
            'category' => 'electricity_utility',
            'title' => 'Electricity Bill for Main Building',
            'amount' => 14200.00,
            'expense_date' => Carbon::now()->startOfMonth()->addDays(3),
            'notes' => 'State electricity board online payment receipt.',
        ]);
    }
}
