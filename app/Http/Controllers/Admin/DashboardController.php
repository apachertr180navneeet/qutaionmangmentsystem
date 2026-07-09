<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Quotation;
use App\Models\Customer;
use App\Models\Item;
use App\Models\FollowUp;
use Exception;

class DashboardController extends Controller
{
    public function index()
    {
        try {
            $totalQuotations = Quotation::count();
            $draftCount = Quotation::where('status', 'draft')->count();
            $sentCount = Quotation::where('status', 'sent')->count();
            $approvedCount = Quotation::where('status', 'approved')->count();
            $expiredCount = Quotation::where('status', 'expired')->count();
            $rejectedCount = Quotation::where('status', 'rejected')->count();
            $totalCustomers = Customer::count();
            $totalItems = Item::count();
            $todayFollowups = FollowUp::whereDate('follow_up_date', today())->with('quotation.customer')->get();
            $recentQuotations = Quotation::with('customer')->latest()->take(5)->get();
            $latestCustomers = Customer::latest()->take(5)->get();

            $statusDistribution = [
                $draftCount, $sentCount, $approvedCount, $expiredCount, $rejectedCount
            ];

            $monthlyTrend = [];
            $monthlyLabels = [];
            for ($m = 1; $m <= 12; $m++) {
                $monthlyLabels[] = date('M', mktime(0, 0, 0, $m, 1));
                $monthlyTrend[] = Quotation::whereYear('created_at', now()->year)
                    ->whereMonth('created_at', $m)->count();
            }

            return view('admin.dashboard.index', compact(
                'totalQuotations', 'draftCount', 'sentCount', 'approvedCount', 'expiredCount', 'rejectedCount',
                'totalCustomers', 'totalItems', 'todayFollowups', 'recentQuotations', 'latestCustomers',
                'monthlyTrend', 'monthlyLabels', 'statusDistribution'
            ));
        } catch (Exception $e) {
            return back()->with('error', $e->getMessage());
        }
    }
}
