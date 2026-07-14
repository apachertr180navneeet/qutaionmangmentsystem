<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Quotation;
use App\Models\Customer;
use App\Models\Item;
use Illuminate\Http\Request;
use Barryvdh\DomPDF\Facade\Pdf;
use Maatwebsite\Excel\Facades\Excel;
use Exception;

class ReportController extends Controller
{
    public function index()
    {
        try {
            $customers = Customer::where('status', true)->orderBy('company_name')->get();
            return view('admin.report.index', compact('customers'));
        } catch (Exception $e) {
            return back()->with('error', $e->getMessage());
        }
    }

    public function customerWise(Request $request)
    {
        try {
            $customers = Customer::where('status', true)->orderBy('company_name')->get();
            $quotations = collect();
            $customer = null;

            if ($request->has('customer_id')) {
                $request->validate(['customer_id' => 'required|exists:customers,id']);
                $customer = Customer::findOrFail($request->customer_id);
                $quotations = Quotation::with('items')
                    ->where('customer_id', $request->customer_id)
                    ->latest()
                    ->get();
            }
            return view('admin.report.customer_wise', compact('quotations', 'customer', 'customers'));
        } catch (Exception $e) {
            return back()->with('error', $e->getMessage());
        }
    }

    public function dateWise(Request $request)
    {
        try {
            $quotations = collect();

            if ($request->has('from_date') && $request->has('to_date')) {
                $request->validate([
                    'from_date' => 'required|date',
                    'to_date' => 'required|date|after_or_equal:from_date',
                ]);

                $quotations = Quotation::with('customer')
                    ->whereDate('created_at', '>=', $request->from_date)
                    ->whereDate('created_at', '<=', $request->to_date)
                    ->latest()
                    ->get();
            }

            return view('admin.report.date_wise', compact('quotations'));
        } catch (Exception $e) {
            return back()->with('error', $e->getMessage());
        }
    }

    public function statusWise(Request $request)
    {
        try {
            $quotations = collect();

            if ($request->has('status')) {
                $request->validate(['status' => 'required|in:draft,sent,approved,expired,rejected']);

                $quotations = Quotation::with('customer')
                    ->where('status', $request->status)
                    ->latest()
                    ->get();
            }

            return view('admin.report.status_wise', compact('quotations'));
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

            if ($request->has('month') && $request->has('year')) {
                $quotations = Quotation::with('customer')
                    ->whereMonth('created_at', $month)
                    ->whereYear('created_at', $year)
                    ->latest()
                    ->get();
            }

            return view('admin.report.monthly', compact('quotations', 'month', 'year'));
        } catch (Exception $e) {
            return back()->with('error', $e->getMessage());
        }
    }

    public function itemWise(Request $request)
    {
        try {
            $items = Item::orderBy('name')->get();
            $quotationItems = collect();
            $item = null;

            if ($request->has('item_id')) {
                $request->validate(['item_id' => 'required|exists:items,id']);

                $item = Item::findOrFail($request->item_id);
                $quotationItems = \App\Models\QuotationItem::with('quotation.customer')
                    ->where('item_id', $request->item_id)
                    ->latest()
                    ->get();
            }

            return view('admin.report.item_wise', compact('quotationItems', 'item', 'items'));
        } catch (Exception $e) {
            return back()->with('error', $e->getMessage());
        }
    }

    public function exportPdf(Request $request)
    {
        try {
            $request->validate(['report_type' => 'required|string']);

            $quotations = collect();
            $title = 'Report';

            switch ($request->report_type) {
                case 'customer_wise':
                    $customer = Customer::findOrFail($request->customer_id);
                    $quotations = Quotation::with('customer')
                        ->where('customer_id', $request->customer_id)->latest()->get();
                    $title = 'Customer Wise Report - ' . $customer->company_name;
                    break;
                case 'date_wise':
                    $quotations = Quotation::with('customer')
                        ->whereDate('created_at', '>=', $request->from_date)
                        ->whereDate('created_at', '<=', $request->to_date)
                        ->latest()->get();
                    $title = "Date Wise Report ({$request->from_date} to {$request->to_date})";
                    break;
                case 'status_wise':
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
            $request->validate(['report_type' => 'required|string']);

            $quotations = collect();

            switch ($request->report_type) {
                case 'customer_wise':
                    $quotations = Quotation::with('customer')
                        ->where('customer_id', $request->customer_id)->latest()->get();
                    break;
                case 'date_wise':
                    $quotations = Quotation::with('customer')
                        ->whereDate('created_at', '>=', $request->from_date)
                        ->whereDate('created_at', '<=', $request->to_date)
                        ->latest()->get();
                    break;
                case 'status_wise':
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
}
