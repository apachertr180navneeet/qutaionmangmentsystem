<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Quotation;
use App\Models\Customer;
use App\Models\Item;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Barryvdh\DomPDF\Facade\Pdf;
use Maatwebsite\Excel\Facades\Excel;
use Exception;

class ReportController extends Controller
{
    public function index()
    {
        try {
            $customers = $this->getActiveCustomers();
            return view('admin.report.index', compact('customers'));
        } catch (Exception $e) {
            return back()->with('error', $e->getMessage());
        }
    }

    public function customerWise(Request $request)
    {
        try {
            $customers = $this->getActiveCustomers();
            $quotations = collect();
            $customer = null;
            $totalGrandTotal = 0;

            if ($request->has('customer_id')) {
                $request->validate(['customer_id' => 'required|exists:customers,id']);
                $customer = Customer::findOrFail($request->customer_id);
                $baseQuery = Quotation::where('customer_id', $request->customer_id);
                $totalGrandTotal = (clone $baseQuery)->sum('grand_total');
                $quotations = $baseQuery->with('items')
                    ->latest()
                    ->paginate(25);
            }
            return view('admin.report.customer_wise', compact('quotations', 'customer', 'customers', 'totalGrandTotal'));
        } catch (Exception $e) {
            return back()->with('error', $e->getMessage());
        }
    }

    public function dateWise(Request $request)
    {
        try {
            $quotations = collect();
            $totalGrandTotal = 0;

            if ($request->has('from_date') && $request->has('to_date')) {
                $request->validate([
                    'from_date' => 'required|date',
                    'to_date' => 'required|date|after_or_equal:from_date',
                ]);

                $baseQuery = Quotation::whereDate('created_at', '>=', $request->from_date)
                    ->whereDate('created_at', '<=', $request->to_date);
                $totalGrandTotal = (clone $baseQuery)->sum('grand_total');
                $quotations = $baseQuery->with('customer')
                    ->latest()
                    ->paginate(25);
            }

            return view('admin.report.date_wise', compact('quotations', 'totalGrandTotal'));
        } catch (Exception $e) {
            return back()->with('error', $e->getMessage());
        }
    }

    public function statusWise(Request $request)
    {
        try {
            $quotations = collect();
            $totalGrandTotal = 0;

            if ($request->has('status')) {
                $request->validate(['status' => 'required|in:draft,sent,approved,expired,rejected']);

                $baseQuery = Quotation::where('status', $request->status);
                $totalGrandTotal = (clone $baseQuery)->sum('grand_total');
                $quotations = $baseQuery->with('customer')
                    ->latest()
                    ->paginate(25);
            }

            return view('admin.report.status_wise', compact('quotations', 'totalGrandTotal'));
        } catch (Exception $e) {
            return back()->with('error', $e->getMessage());
        }
    }

    public function monthly(Request $request)
    {
        try {
            $month = $request->input('month', now()->month);
            $year = $request->input('year', now()->year);
            $quotations = collect();
            $totalGrandTotal = 0;

            if ($request->has('month') && $request->has('year')) {
                $baseQuery = Quotation::whereMonth('created_at', $month)
                    ->whereYear('created_at', $year);
                $totalGrandTotal = (clone $baseQuery)->sum('grand_total');
                $quotations = $baseQuery->with('customer')
                    ->latest()
                    ->paginate(25);
            }

            return view('admin.report.monthly', compact('quotations', 'month', 'year', 'totalGrandTotal'));
        } catch (Exception $e) {
            return back()->with('error', $e->getMessage());
        }
    }

    public function itemWise(Request $request)
    {
        try {
            $items = $this->getActiveItems();
            $quotationItems = collect();
            $item = null;
            $totalQuantity = 0;
            $totalAmount = 0;

            if ($request->has('item_id')) {
                $request->validate(['item_id' => 'required|exists:items,id']);

                $item = Item::findOrFail($request->item_id);
                $baseQuery = \App\Models\QuotationItem::where('item_id', $request->item_id);
                $totalQuantity = (clone $baseQuery)->sum('quantity');
                $totalAmount = (clone $baseQuery)->sum('total');
                $quotationItems = $baseQuery->with('quotation.customer')
                    ->latest()
                    ->paginate(25);
            }

            return view('admin.report.item_wise', compact('quotationItems', 'item', 'items', 'totalQuantity', 'totalAmount'));
        } catch (Exception $e) {
            return back()->with('error', $e->getMessage());
        }
    }

    public function exportPdf(Request $request)
    {
        try {
            $request->validate(['report_type' => 'required|string|in:customer_wise,date_wise,status_wise,monthly,item_wise']);

            $quotations = collect();
            $title = 'Report';

            switch ($request->report_type) {
                case 'customer_wise':
                    $request->validate(['customer_id' => 'required|exists:customers,id']);
                    $customer = Customer::findOrFail($request->customer_id);
                    $quotations = Quotation::with('customer')
                        ->where('customer_id', $request->customer_id)->latest()->get();
                    $title = 'Customer Wise Report - ' . $customer->company_name;
                    break;
                case 'date_wise':
                    $request->validate([
                        'from_date' => 'required|date',
                        'to_date' => 'required|date|after_or_equal:from_date',
                    ]);
                    $quotations = Quotation::with('customer')
                        ->whereDate('created_at', '>=', $request->from_date)
                        ->whereDate('created_at', '<=', $request->to_date)
                        ->latest()->get();
                    $title = "Date Wise Report ({$request->from_date} to {$request->to_date})";
                    break;
                case 'status_wise':
                    $request->validate(['status' => 'required|in:draft,sent,approved,expired,rejected']);
                    $quotations = Quotation::with('customer')
                        ->where('status', $request->status)->latest()->get();
                    $title = 'Status Wise Report - ' . ucfirst($request->status);
                    break;
                case 'monthly':
                    $month = $request->input('month', now()->month);
                    $year = $request->input('year', now()->year);
                    $quotations = Quotation::with('customer')
                        ->whereMonth('created_at', $month)
                        ->whereYear('created_at', $year)->latest()->get();
                    $title = "Monthly Report - {$month}/{$year}";
                    break;
                case 'item_wise':
                    $request->validate(['item_id' => 'required|exists:items,id']);
                    $item = Item::findOrFail($request->item_id);
                    $quotations = Quotation::whereHas('items', function ($q) use ($request) {
                        $q->where('item_id', $request->item_id);
                    })->with('customer')->latest()->get();
                    $title = 'Item Wise Report - ' . $item->name;
                    break;
            }

            $pdf = Pdf::loadView('admin.report.pdf', compact('quotations', 'title'));
            return $pdf->download(str_replace(' ', '_', $title) . '.pdf');
        } catch (Exception $e) {
            return back()->with('error', $e->getMessage());
        }
    }

    public function exportExcel(Request $request)
    {
        try {
            $request->validate(['report_type' => 'required|string|in:customer_wise,date_wise,status_wise,monthly,item_wise']);

            $quotations = collect();

            switch ($request->report_type) {
                case 'customer_wise':
                    $request->validate(['customer_id' => 'required|exists:customers,id']);
                    $quotations = Quotation::with('customer')
                        ->where('customer_id', $request->customer_id)->latest()->get();
                    break;
                case 'date_wise':
                    $request->validate([
                        'from_date' => 'required|date',
                        'to_date' => 'required|date|after_or_equal:from_date',
                    ]);
                    $quotations = Quotation::with('customer')
                        ->whereDate('created_at', '>=', $request->from_date)
                        ->whereDate('created_at', '<=', $request->to_date)
                        ->latest()->get();
                    break;
                case 'status_wise':
                    $request->validate(['status' => 'required|in:draft,sent,approved,expired,rejected']);
                    $quotations = Quotation::with('customer')
                        ->where('status', $request->status)->latest()->get();
                    break;
                case 'monthly':
                    $month = $request->input('month', now()->month);
                    $year = $request->input('year', now()->year);
                    $quotations = Quotation::with('customer')
                        ->whereMonth('created_at', $month)
                        ->whereYear('created_at', $year)->latest()->get();
                    break;
                case 'item_wise':
                    $request->validate(['item_id' => 'required|exists:items,id']);
                    $quotations = Quotation::whereHas('items', function ($q) use ($request) {
                        $q->where('item_id', $request->item_id);
                    })->with('customer')->latest()->get();
                    break;
            }

            return Excel::download(new class($quotations) implements \Maatwebsite\Excel\Concerns\FromCollection, \Maatwebsite\Excel\Concerns\WithHeadings {
                private $data;
                public function __construct($data) { $this->data = $data; }
                public function collection() { return $this->data->map(function ($q) {
                    return [
                        'Quotation #' => $q->quotation_number,
                        'Customer' => $q->customer?->company_name ?? 'N/A',
                        'Date' => $q->created_at->format('d-m-Y'),
                        'Subtotal' => $q->subtotal,
                        'Discount' => $q->discount_amount,
                        'Tax' => $q->cgst_amount + $q->sgst_amount + $q->igst_amount,
                        'Grand Total' => $q->grand_total,
                        'Status' => ucfirst($q->status),
                    ];
                }); }
                public function headings(): array {
                    return ['Quotation #', 'Customer', 'Date', 'Subtotal', 'Discount', 'Tax', 'Grand Total', 'Status'];
                }
            }, 'report.xlsx');
        } catch (Exception $e) {
            return back()->with('error', $e->getMessage());
        }
    }

    protected function getActiveCustomers()
    {
        return Cache::remember('active_customers', 600, function () {
            return Customer::where('status', true)->orderBy('company_name')->get();
        });
    }

    protected function getActiveItems()
    {
        return Cache::remember('active_items', 600, function () {
            return Item::orderBy('name')->get();
        });
    }
}
