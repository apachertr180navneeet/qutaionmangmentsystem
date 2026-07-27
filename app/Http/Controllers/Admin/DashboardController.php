<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Quotation;
use App\Models\Customer;
use App\Models\Item;
use App\Models\FollowUp;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Exception;

class DashboardController extends Controller
{
    public function index()
    {
        try {
            $cacheKey = 'dashboard_data';
            $cacheMinutes = 5;

            $data = Cache::remember($cacheKey, $cacheMinutes * 60, function () {
                $statusCounts = Quotation::select('status', DB::raw('count(*) as total'))
                    ->groupBy('status')
                    ->pluck('total', 'status')
                    ->toArray();

                $draftCount = $statusCounts['draft'] ?? 0;
                $sentCount = $statusCounts['sent'] ?? 0;
                $approvedCount = $statusCounts['approved'] ?? 0;
                $expiredCount = $statusCounts['expired'] ?? 0;
                $rejectedCount = $statusCounts['rejected'] ?? 0;
                $totalQuotations = array_sum($statusCounts);

                $monthlyData = Quotation::select(
                        DB::raw('MONTH(created_at) as month'),
                        DB::raw('count(*) as total')
                    )
                    ->whereYear('created_at', now()->year)
                    ->groupBy(DB::raw('MONTH(created_at)'))
                    ->pluck('total', 'month')
                    ->toArray();

                $monthlyTrend = [];
                $monthlyLabels = [];
                for ($m = 1; $m <= 12; $m++) {
                    $monthlyLabels[] = date('M', mktime(0, 0, 0, $m, 1));
                    $monthlyTrend[] = $monthlyData[$m] ?? 0;
                }

                return compact(
                    'totalQuotations', 'draftCount', 'sentCount', 'approvedCount', 'expiredCount', 'rejectedCount',
                    'monthlyTrend', 'monthlyLabels'
                );
            });

            extract($data);

            $statusDistribution = [
                $draftCount, $sentCount, $approvedCount, $expiredCount, $rejectedCount
            ];

            $totalCustomers = Cache::remember('total_customers_count', $cacheMinutes * 60, fn() => Customer::count());
            $totalItems = Cache::remember('total_items_count', $cacheMinutes * 60, fn() => Item::count());
            $todayFollowups = FollowUp::whereDate('follow_up_date', today())->with('quotation.customer')->get();
            $recentQuotations = Cache::remember('recent_quotations', $cacheMinutes * 60, fn() =>
                Quotation::with('customer')->latest()->take(5)->get()
            );
            $latestCustomers = Cache::remember('latest_customers', $cacheMinutes * 60, fn() =>
                Customer::latest()->take(5)->get()
            );

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
