<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\QuotationRequest;
use App\Models\Quotation;
use App\Models\QuotationItem;
use App\Models\Customer;
use App\Models\Item;
use App\Models\EmailLog;
use App\Models\CompanySetting;
use Illuminate\Http\Request;
use Barryvdh\DomPDF\Facade\Pdf;
use Mail, DB, Exception;

class QuotationController extends Controller
{
    public function index(Request $request)
    {
        try {
            $query = Quotation::with(['customer', 'items']);

            if ($request->filled('search')) {
                $search = $request->search;
                $query->where(function ($q) use ($search) {
                    $q->where('quotation_number', 'like', "%{$search}%")
                      ->orWhereHas('customer', function ($cq) use ($search) {
                          $cq->where('company_name', 'like', "%{$search}%");
                      });
                });
            }

            if ($request->filled('status')) {
                $query->where('status', $request->status);
            }

            $quotations = $query->latest()->paginate(10);
            return view('admin.quotation.index', compact('quotations'));
        } catch (Exception $e) {
            return back()->with('error', $e->getMessage());
        }
    }

    public function create()
    {
        try {
            $customers = Customer::where('status', true)->orderBy('company_name')->get();
            $items = Item::where('is_active', true)->orderBy('name')->get();

            $year = now()->format('Y');
            $lastQuotation = Quotation::where('quotation_number', 'like', "Q-{$year}-%")
                ->orderBy('quotation_number', 'desc')
                ->first();

            if ($lastQuotation) {
                $lastNumber = (int) substr($lastQuotation->quotation_number, -4);
                $newNumber = str_pad($lastNumber + 1, 4, '0', STR_PAD_LEFT);
            } else {
                $newNumber = '0001';
            }
            $quotation_number = "Q-{$year}-{$newNumber}";

            return view('admin.quotation.create', compact('customers', 'items', 'quotation_number'));
        } catch (Exception $e) {
            return back()->with('error', $e->getMessage());
        }
    }

    public function store(QuotationRequest $request)
    {
        try {
            DB::beginTransaction();

            $data = $request->validated();
            $data['user_id'] = auth()->id();
            $data['created_by'] = auth()->id();
            $data['status'] = 'draft';

            $data['discount_type'] = $data['discount_type'] ?? 'percentage';
            $data['discount_value'] = $data['discount_value'] ?? 0;
            $data['cgst_percentage'] = $data['cgst_percentage'] ?? 0;
            $data['sgst_percentage'] = $data['sgst_percentage'] ?? 0;
            $data['igst_percentage'] = $data['igst_percentage'] ?? 0;

            $subtotal = 0;
            foreach ($data['items'] as $item) {
                $lineTotal = $item['quantity'] * $item['rate'];
                $subtotal += $lineTotal;
            }
            $data['subtotal'] = $subtotal;

            if ($data['discount_type'] === 'percentage') {
                $data['discount_amount'] = ($subtotal * $data['discount_value']) / 100;
            } else {
                $data['discount_amount'] = $data['discount_value'];
            }

            $afterDiscount = $subtotal - $data['discount_amount'];
            $data['tax_rate'] = 0;

            if ($data['tax_type'] === 'cgst_sgst') {
                $cgstPerc = $data['cgst_percentage'] ?? 0;
                $sgstPerc = $data['sgst_percentage'] ?? 0;
                $data['cgst_amount'] = ($afterDiscount * $cgstPerc) / 100;
                $data['sgst_amount'] = ($afterDiscount * $sgstPerc) / 100;
                $data['igst_amount'] = 0;
                $data['tax_rate'] = $cgstPerc + $sgstPerc;
            } elseif ($data['tax_type'] === 'igst') {
                $igstPerc = $data['igst_percentage'] ?? 0;
                $data['igst_amount'] = ($afterDiscount * $igstPerc) / 100;
                $data['cgst_amount'] = 0;
                $data['sgst_amount'] = 0;
                $data['tax_rate'] = $igstPerc;
            } else {
                $data['cgst_amount'] = 0;
                $data['sgst_amount'] = 0;
                $data['igst_amount'] = 0;
            }

            $totalTax = $data['cgst_amount'] + $data['sgst_amount'] + $data['igst_amount'];
            $grandTotal = $afterDiscount + $totalTax;
            $data['round_off'] = round($grandTotal) - $grandTotal;
            $data['grand_total'] = round($grandTotal);

            $quotation = Quotation::create($data);

            foreach ($data['items'] as $index => $itemData) {
                $itemSubtotal = $itemData['quantity'] * $itemData['rate'];

                QuotationItem::create([
                    'quotation_id' => $quotation->id,
                    'item_id' => $itemData['item_id'] ?? null,
                    'item_name' => $itemData['item_name'],
                    'quantity' => $itemData['quantity'],
                    'rate' => $itemData['rate'],
                    'total' => $itemSubtotal,
                    'sort_order' => $index + 1,
                ]);
            }

            DB::commit();
            return redirect()->route('admin.quotations.index')->with('success', 'Quotation created successfully.');
        } catch (Exception $e) {
            DB::rollBack();
            return back()->with('error', $e->getMessage())->withInput();
        }
    }

    public function show($id)
    {
        try {
            $quotation = Quotation::with(['customer', 'items', 'creator'])->findOrFail($id);
            return view('admin.quotation.show', compact('quotation'));
        } catch (Exception $e) {
            return back()->with('error', $e->getMessage());
        }
    }

    public function edit($id)
    {
        try {
            $quotation = Quotation::with('items')->findOrFail($id);
            $customers = Customer::where('status', true)->orderBy('company_name')->get();
            $items = Item::where('is_active', true)->orderBy('name')->get();
            return view('admin.quotation.edit', compact('quotation', 'customers', 'items'));
        } catch (Exception $e) {
            return back()->with('error', $e->getMessage());
        }
    }

    public function update(QuotationRequest $request, $id)
    {
        try {
            DB::beginTransaction();

            $quotation = Quotation::findOrFail($id);
            $data = $request->validated();

            $data['discount_type'] = $data['discount_type'] ?? 'percentage';
            $data['discount_value'] = $data['discount_value'] ?? 0;
            $data['cgst_percentage'] = $data['cgst_percentage'] ?? 0;
            $data['sgst_percentage'] = $data['sgst_percentage'] ?? 0;
            $data['igst_percentage'] = $data['igst_percentage'] ?? 0;

            $subtotal = 0;
            foreach ($data['items'] as $item) {
                $lineTotal = $item['quantity'] * $item['rate'];
                $subtotal += $lineTotal;
            }
            $data['subtotal'] = $subtotal;

            if ($data['discount_type'] === 'percentage') {
                $data['discount_amount'] = ($subtotal * $data['discount_value']) / 100;
            } else {
                $data['discount_amount'] = $data['discount_value'];
            }

            $afterDiscount = $subtotal - $data['discount_amount'];

            if ($data['tax_type'] === 'cgst_sgst') {
                $cgstPerc = $data['cgst_percentage'] ?? 0;
                $sgstPerc = $data['sgst_percentage'] ?? 0;
                $data['cgst_amount'] = ($afterDiscount * $cgstPerc) / 100;
                $data['sgst_amount'] = ($afterDiscount * $sgstPerc) / 100;
                $data['igst_amount'] = 0;
                $data['tax_rate'] = $cgstPerc + $sgstPerc;
            } elseif ($data['tax_type'] === 'igst') {
                $igstPerc = $data['igst_percentage'] ?? 0;
                $data['igst_amount'] = ($afterDiscount * $igstPerc) / 100;
                $data['cgst_amount'] = 0;
                $data['sgst_amount'] = 0;
                $data['tax_rate'] = $igstPerc;
            } else {
                $data['cgst_amount'] = 0;
                $data['sgst_amount'] = 0;
                $data['igst_amount'] = 0;
            }

            $totalTax = $data['cgst_amount'] + $data['sgst_amount'] + $data['igst_amount'];
            $grandTotal = $afterDiscount + $totalTax;
            $data['round_off'] = round($grandTotal) - $grandTotal;
            $data['grand_total'] = round($grandTotal);

            $quotation->update($data);

            $quotation->items()->delete();

            foreach ($data['items'] as $index => $itemData) {
                $itemSubtotal = $itemData['quantity'] * $itemData['rate'];

                QuotationItem::create([
                    'quotation_id' => $quotation->id,
                    'item_id' => $itemData['item_id'] ?? null,
                    'item_name' => $itemData['item_name'],
                    'quantity' => $itemData['quantity'],
                    'rate' => $itemData['rate'],
                    'total' => $itemSubtotal,
                    'sort_order' => $index + 1,
                ]);
            }

            DB::commit();
            return redirect()->route('admin.quotations.index')->with('success', 'Quotation updated successfully.');
        } catch (Exception $e) {
            DB::rollBack();
            return back()->with('error', $e->getMessage())->withInput();
        }
    }

    public function destroy($id)
    {
        try {
            $quotation = Quotation::findOrFail($id);
            $quotation->delete();
            return redirect()->route('admin.quotations.index')->with('success', 'Quotation deleted successfully.');
        } catch (Exception $e) {
            return back()->with('error', $e->getMessage());
        }
    }

    public function duplicate($id)
    {
        try {
            DB::beginTransaction();

            $original = Quotation::with('items')->findOrFail($id);
            $newData = $original->replicate();
            $newData->initializeHasUuid();
            $newData->quotation_number = null;
            $newData->revision_number = ($original->revision_number ?? 0) + 1;
            $newData->parent_id = $original->parent_id ?? $original->id;
            $newData->status = 'draft';
            $newData->created_by = auth()->id();
            $newData->save();

            foreach ($original->items as $item) {
                $newItem = $item->replicate();
                $newItem->quotation_id = $newData->id;
                $newItem->save();
            }

            DB::commit();
            return redirect()->route('admin.quotations.index')->with('success', 'Quotation duplicated successfully.');
        } catch (Exception $e) {
            DB::rollBack();
            return back()->with('error', $e->getMessage());
        }
    }


    public function email(Request $request, $id)
    {
        try {
            $request->validate([
                'recipient_email' => 'required|email',
                'subject' => 'required|string|max:255',
                'message' => 'nullable|string',
            ]);

            $quotation = Quotation::with(['customer', 'items'])->findOrFail($id);
            $company = CompanySetting::first();

            $pdf = Pdf::setOptions(['isHtml5ParserEnabled' => true, 'isRemoteEnabled' => true])
                ->loadView('admin.quotation.pdf', compact('quotation', 'company'));

            $data = [
                'quotation' => $quotation,
                'messageContent' => $request->message,
                'company' => $company,
            ];

            Mail::send('admin.emails.quotation', $data, function ($message) use ($request, $pdf, $quotation) {
                $message->to($request->recipient_email)
                    ->subject($request->subject)
                    ->attachData($pdf->output(), 'quotation-' . $quotation->quotation_number . '.pdf');
            });

            EmailLog::create([
                'quotation_id' => $quotation->id,
                'user_id' => auth()->id(),
                'recipient_email' => $request->recipient_email,
                'recipient_name' => $quotation->customer?->contact_person ?? '',
                'subject' => $request->subject,
                'message' => $request->message,
                'status' => 'sent',
                'sent_at' => now(),
            ]);

            return redirect()->back()->with('success', 'Email sent successfully.');
        } catch (Exception $e) {
            return back()->with('error', $e->getMessage());
        }
    }

    public function pdf($id)
    {
        try {
            $quotation = Quotation::with(['customer', 'items'])->findOrFail($id);
            $company = CompanySetting::first();

            $pdf = Pdf::setOptions(['isHtml5ParserEnabled' => true, 'isRemoteEnabled' => true])
                ->loadView('admin.quotation.pdf', compact('quotation', 'company'));

            return $pdf->download('quotation-' . $quotation->quotation_number . '.pdf');
        } catch (Exception $e) {
            return back()->with('error', $e->getMessage());
        }
    }

    public function updateStatus(Request $request, $id)
    {
        try {
            $request->validate([
                'status' => 'required|in:draft,sent,approved,expired,rejected'
            ]);

            $quotation = Quotation::findOrFail($id);
            $quotation->status = $request->status;
            $quotation->save();

            return response()->json([
                'success' => true,
                'message' => 'Status updated successfully.',
                'status' => $quotation->status
            ]);
        } catch (Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 500);
        }
    }
}
