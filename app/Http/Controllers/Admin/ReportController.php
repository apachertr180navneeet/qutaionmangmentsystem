<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Quotation;
use App\Models\Customer;
use App\Models\Item;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use App\Models\CompanySetting;
use App\Models\QuotationItem;
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

            if ($request->has('customer_id') && $request->customer_id != '') {
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

            if ($request->filled('from_date') && $request->filled('to_date')) {
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

            if ($request->filled('status')) {
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

            if ($request->filled('month') && $request->filled('year')) {
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

            if ($request->filled('item_id')) {
                $request->validate(['item_id' => 'required|exists:items,id']);

                $item = Item::findOrFail($request->item_id);
                $baseQuery = QuotationItem::where('item_id', $request->item_id);
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

            $company = CompanySetting::first();
            $report_type = $request->report_type;
            $quotations = collect();
            $quotationItems = collect();
            $title = 'Report';
            $subtitle = '';
            $totalGrandTotal = 0;
            $totalQuantity = 0;
            $totalAmount = 0;

            switch ($report_type) {
                case 'customer_wise':
                    $request->validate(['customer_id' => 'required|exists:customers,id']);
                    $customer = Customer::findOrFail($request->customer_id);
                    $quotations = Quotation::with('customer')
                        ->where('customer_id', $request->customer_id)
                        ->latest()
                        ->get();
                    $totalGrandTotal = $quotations->sum('grand_total');
                    $title = 'Customer Wise Report';
                    $subtitle = 'Customer: ' . $customer->company_name;
                    break;

                case 'date_wise':
                    $request->validate([
                        'from_date' => 'required|date',
                        'to_date' => 'required|date|after_or_equal:from_date',
                    ]);
                    $quotations = Quotation::with('customer')
                        ->whereDate('created_at', '>=', $request->from_date)
                        ->whereDate('created_at', '<=', $request->to_date)
                        ->latest()
                        ->get();
                    $totalGrandTotal = $quotations->sum('grand_total');
                    $subtitle = 'Period: ' . date('d-m-Y', strtotime($request->from_date)) . ' to ' . date('d-m-Y', strtotime($request->to_date));
                    $title = 'Date Wise Report';
                    break;

                case 'status_wise':
                    $request->validate(['status' => 'required|in:draft,sent,approved,expired,rejected']);
                    $quotations = Quotation::with('customer')
                        ->where('status', $request->status)
                        ->latest()
                        ->get();
                    $totalGrandTotal = $quotations->sum('grand_total');
                    $title = 'Status Wise Report';
                    $subtitle = 'Status: ' . ucfirst($request->status);
                    break;

                case 'monthly':
                    $month = $request->input('month', now()->month);
                    $year = $request->input('year', now()->year);
                    $quotations = Quotation::with('customer')
                        ->whereMonth('created_at', $month)
                        ->whereYear('created_at', $year)
                        ->latest()
                        ->get();
                    $totalGrandTotal = $quotations->sum('grand_total');
                    $monthName = date('F', mktime(0, 0, 0, (int)$month, 1));
                    $title = 'Monthly Report';
                    $subtitle = "Month: {$monthName} {$year}";
                    break;

                case 'item_wise':
                    $request->validate(['item_id' => 'required|exists:items,id']);
                    $item = Item::findOrFail($request->item_id);
                    $quotationItems = QuotationItem::where('item_id', $request->item_id)
                        ->with('quotation.customer')
                        ->latest()
                        ->get();
                    $totalQuantity = $quotationItems->sum('quantity');
                    $totalAmount = $quotationItems->sum('total');
                    $title = 'Item Wise Report';
                    $subtitle = 'Item: ' . $item->name . ($item->sku ? ' (' . $item->sku . ')' : '');
                    break;
            }

            $pdf = Pdf::setOptions(['isHtml5ParserEnabled' => true, 'isRemoteEnabled' => true])
                ->loadView('admin.report.pdf', compact(
                    'quotations',
                    'quotationItems',
                    'title',
                    'subtitle',
                    'company',
                    'report_type',
                    'totalGrandTotal',
                    'totalQuantity',
                    'totalAmount'
                ));

            $filename = str_replace([' ', '/', '\\', ':', '*', '?', '"', '<', '>', '|'], '_', $title . '_' . date('Ymd_His')) . '.pdf';
            return $pdf->download($filename);
        } catch (Exception $e) {
            return back()->with('error', $e->getMessage());
        }
    }

    public function exportExcel(Request $request)
    {
        try {
            $request->validate(['report_type' => 'required|string|in:customer_wise,date_wise,status_wise,monthly,item_wise']);

            if ($request->report_type === 'item_wise') {
                $request->validate(['item_id' => 'required|exists:items,id']);
                $item = Item::findOrFail($request->item_id);
                $quotationItems = QuotationItem::where('item_id', $request->item_id)
                    ->with('quotation.customer')
                    ->latest()
                    ->get();

                return Excel::download(new class($quotationItems) implements \Maatwebsite\Excel\Concerns\FromCollection, \Maatwebsite\Excel\Concerns\WithHeadings {
                    private $data;
                    public function __construct($data) { $this->data = $data; }
                    public function collection() {
                        return $this->data->map(function ($qi, $index) {
                            return [
                                '#' => $index + 1,
                                'Quotation #' => $qi->quotation?->quotation_number ?? 'N/A',
                                'Customer' => $qi->quotation?->customer?->company_name ?? 'N/A',
                                'Date' => $qi->quotation?->date ? date('d-m-Y', strtotime($qi->quotation->date)) : ($qi->quotation?->created_at ? $qi->quotation->created_at->format('d-m-Y') : 'N/A'),
                                'Quantity' => $qi->quantity,
                                'Rate' => $qi->rate,
                                'Total' => $qi->total,
                            ];
                        });
                    }
                    public function headings(): array {
                        return ['#', 'Quotation #', 'Customer', 'Date', 'Quantity', 'Rate', 'Total'];
                    }
                }, 'item_wise_report_' . date('Ymd_His') . '.xlsx');
            }

            $quotations = collect();
            switch ($request->report_type) {
                case 'customer_wise':
                    $request->validate(['customer_id' => 'required|exists:customers,id']);
                    $quotations = Quotation::with('customer')
                        ->where('customer_id', $request->customer_id)
                        ->latest()
                        ->get();
                    break;
                case 'date_wise':
                    $request->validate([
                        'from_date' => 'required|date',
                        'to_date' => 'required|date|after_or_equal:from_date',
                    ]);
                    $quotations = Quotation::with('customer')
                        ->whereDate('created_at', '>=', $request->from_date)
                        ->whereDate('created_at', '<=', $request->to_date)
                        ->latest()
                        ->get();
                    break;
                case 'status_wise':
                    $request->validate(['status' => 'required|in:draft,sent,approved,expired,rejected']);
                    $quotations = Quotation::with('customer')
                        ->where('status', $request->status)
                        ->latest()
                        ->get();
                    break;
                case 'monthly':
                    $month = $request->input('month', now()->month);
                    $year = $request->input('year', now()->year);
                    $quotations = Quotation::with('customer')
                        ->whereMonth('created_at', $month)
                        ->whereYear('created_at', $year)
                        ->latest()
                        ->get();
                    break;
            }

            return Excel::download(new class($quotations) implements \Maatwebsite\Excel\Concerns\FromCollection, \Maatwebsite\Excel\Concerns\WithHeadings {
                private $data;
                public function __construct($data) { $this->data = $data; }
                public function collection() {
                    return $this->data->map(function ($q, $index) {
                        return [
                            '#' => $index + 1,
                            'Quotation #' => $q->quotation_number,
                            'Customer' => $q->customer?->company_name ?? 'N/A',
                            'Date' => $q->date ? date('d-m-Y', strtotime($q->date)) : ($q->created_at ? $q->created_at->format('d-m-Y') : 'N/A'),
                            'Subtotal' => $q->subtotal,
                            'Discount' => $q->discount_amount,
                            'Tax' => $q->cgst_amount + $q->sgst_amount + $q->igst_amount,
                            'Grand Total' => $q->grand_total,
                            'Status' => ucfirst($q->status),
                        ];
                    });
                }
                public function headings(): array {
                    return ['#', 'Quotation #', 'Customer', 'Date', 'Subtotal', 'Discount', 'Tax', 'Grand Total', 'Status'];
                }
            }, 'quotation_report_' . date('Ymd_His') . '.xlsx');
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
