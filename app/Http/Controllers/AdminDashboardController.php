<?php

namespace App\Http\Controllers;

use App\Models\DelayRequest;
use App\Models\Expense;
use App\Models\Floor;
use App\Models\RentInvoice;
use App\Models\RentPayment;
use App\Models\Room;
use App\Models\Staff;
use App\Models\StaffSalary;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;

class AdminDashboardController extends Controller
{
    public function index()
    {
        $currentMonth = Carbon::now()->format('Y-m');
        $currentMonthName = Carbon::now()->format('F Y');

        // Stats
        $totalFloors = Floor::count();
        $totalRooms = Room::count();
        $totalBedsCapacity = Room::sum('bed_capacity');
        $totalOccupiedBeds = User::where('role', 'renter')->where('status', 'active')->whereNotNull('assigned_room_id')->count();
        $occupancyRate = $totalBedsCapacity > 0 ? round(($totalOccupiedBeds / $totalBedsCapacity) * 100, 1) : 0;

        $totalRenters = User::where('role', 'renter')->where('status', 'active')->count();

        // Current Month Financials
        $monthlyIncome = RentPayment::where('status', 'approved')
            ->whereMonth('payment_date', Carbon::now()->month)
            ->whereYear('payment_date', Carbon::now()->year)
            ->sum('amount');

        $monthlyDirectExpenses = Expense::whereMonth('expense_date', Carbon::now()->month)
            ->whereYear('expense_date', Carbon::now()->year)
            ->sum('amount');

        $monthlyStaffSalaries = StaffSalary::whereMonth('payment_date', Carbon::now()->month)
            ->whereYear('payment_date', Carbon::now()->year)
            ->sum('amount');

        $totalMonthlyExpenses = $monthlyDirectExpenses + $monthlyStaffSalaries;
        $netProfitLoss = $monthlyIncome - $totalMonthlyExpenses;

        // Actionable counts
        $pendingPaymentsCount = RentPayment::where('status', 'pending_approval')->count();
        $pendingDelayRequestsCount = DelayRequest::where('status', 'pending')->count();
        $overdueInvoicesCount = RentInvoice::where('status', 'overdue')->count();

        // Monthly Financial Trend Data (Last 6 Months)
        $chartMonths = [];
        $chartIncome = [];
        $chartExpenses = [];
        $chartProfit = [];

        for ($i = 5; $i >= 0; $i--) {
            $dt = Carbon::now()->subMonths($i);
            $mLabel = $dt->format('M Y');
            $chartMonths[] = $mLabel;

            $inc = RentPayment::where('status', 'approved')
                ->whereMonth('payment_date', $dt->month)
                ->whereYear('payment_date', $dt->year)
                ->sum('amount');

            $expDir = Expense::whereMonth('expense_date', $dt->month)
                ->whereYear('expense_date', $dt->year)
                ->sum('amount');

            $expSal = StaffSalary::whereMonth('payment_date', $dt->month)
                ->whereYear('payment_date', $dt->year)
                ->sum('amount');

            $expTotal = $expDir + $expSal;

            $chartIncome[] = (float)$inc;
            $chartExpenses[] = (float)$expTotal;
            $chartProfit[] = (float)($inc - $expTotal);
        }

        // Recent Activity
        $recentPayments = RentPayment::with(['invoice.renter', 'invoice.room'])
            ->latest()
            ->take(5)
            ->get();

        $recentDelayRequests = DelayRequest::with(['renter', 'invoice'])
            ->latest()
            ->take(5)
            ->get();

        return view('admin.dashboard', compact(
            'currentMonthName',
            'totalFloors',
            'totalRooms',
            'totalBedsCapacity',
            'totalOccupiedBeds',
            'occupancyRate',
            'totalRenters',
            'monthlyIncome',
            'totalMonthlyExpenses',
            'monthlyDirectExpenses',
            'monthlyStaffSalaries',
            'netProfitLoss',
            'pendingPaymentsCount',
            'pendingDelayRequestsCount',
            'overdueInvoicesCount',
            'chartMonths',
            'chartIncome',
            'chartExpenses',
            'chartProfit',
            'recentPayments',
            'recentDelayRequests'
        ));
    }
}
