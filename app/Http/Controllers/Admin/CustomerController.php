<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\CustomerRequest;
use App\Models\Customer;
use Illuminate\Http\Request;
use Exception;
use Maatwebsite\Excel\Facades\Excel;
use App\Imports\CustomerImport;
use App\Exports\CustomerTemplateExport;

class CustomerController extends Controller
{
    public function index(Request $request)
    {
        try {
            $query = Customer::query();

            if ($request->filled('search')) {
                $search = $request->search;
                $query->where(function ($q) use ($search) {
                    $q->where('company_name', 'like', "%{$search}%")
                      ->orWhere('contact_person', 'like', "%{$search}%")
                      ->orWhere('email', 'like', "%{$search}%")
                      ->orWhere('phone', 'like', "%{$search}%");
                });
            }

            $customers = $query->latest()->paginate(10);
            return view('admin.customer.index', compact('customers'));
        } catch (Exception $e) {
            return back()->with('error', $e->getMessage());
        }
    }

    public function create()
    {
        try {
            return view('admin.customer.create');
        } catch (Exception $e) {
            return back()->with('error', $e->getMessage());
        }
    }

    public function store(CustomerRequest $request)
    {
        try {
            $data = $request->validated();
            $data['created_by'] = auth()->id();
            Customer::create($data);
            return redirect()->route('admin.customers.index')->with('success', 'Customer created successfully.');
        } catch (Exception $e) {
            return back()->with('error', $e->getMessage())->withInput();
        }
    }

    public function show($id)
    {
        try {
            $customer = Customer::with('quotations')->findOrFail($id);
            return view('admin.customer.show', compact('customer'));
        } catch (Exception $e) {
            return back()->with('error', $e->getMessage());
        }
    }

    public function edit($id)
    {
        try {
            $customer = Customer::findOrFail($id);
            return view('admin.customer.edit', compact('customer'));
        } catch (Exception $e) {
            return back()->with('error', $e->getMessage());
        }
    }

    public function update(CustomerRequest $request, $id)
    {
        try {
            $customer = Customer::findOrFail($id);
            $customer->update($request->validated());
            return redirect()->route('admin.customers.index')->with('success', 'Customer updated successfully.');
        } catch (Exception $e) {
            return back()->with('error', $e->getMessage())->withInput();
        }
    }

    public function destroy($id)
    {
        try {
            $customer = Customer::findOrFail($id);
            $customer->delete();
            return redirect()->route('admin.customers.index')->with('success', 'Customer deleted successfully.');
        } catch (Exception $e) {
            return back()->with('error', $e->getMessage());
        }
    }

    public function import(Request $request)
    {
        $request->validate([
            'file' => 'required|mimes:csv,txt,xlsx,xls',
        ]);

        try {
            Excel::import(new CustomerImport, $request->file('file'));
            return redirect()->route('admin.customers.index')->with('success', 'Customers imported successfully.');
        } catch (Exception $e) {
            return back()->with('error', $e->getMessage());
        }
    }

    public function downloadTemplate()
    {
        return Excel::download(new CustomerTemplateExport, 'customer_import_template.xlsx');
    }
}
