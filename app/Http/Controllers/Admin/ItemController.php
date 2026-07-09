<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Item;
use Illuminate\Http\Request;
use Exception;

class ItemController extends Controller
{
    public function index(Request $request)
    {
        try {
            $query = Item::query();

            if ($request->filled('search')) {
                $search = $request->search;
                $query->where(function ($q) use ($search) {
                    $q->where('name', 'like', "%{$search}%")
                      ->orWhere('sku', 'like', "%{$search}%");
                });
            }

            $items = $query->latest()->paginate(10);
            return view('admin.item.index', compact('items'));
        } catch (Exception $e) {
            return back()->with('error', $e->getMessage());
        }
    }

    public function create()
    {
        try {
            return view('admin.item.create');
        } catch (Exception $e) {
            return back()->with('error', $e->getMessage());
        }
    }

    public function store(Request $request)
    {
        try {
            $data = $request->validate([
                'name' => 'required|string|max:255',
                'sku' => 'nullable|string|max:100|unique:items,sku',
                'description' => 'nullable|string',
                'unit' => 'required|string|max:50',
                'rate' => 'required|numeric|min:0',
                'tax_percentage' => 'nullable|numeric|min:0|max:100',
                'type' => 'nullable|string|max:50',
                'hsn_code' => 'nullable|string|max:50',
                'is_active' => 'required|boolean',
            ]);
            $data['created_by'] = auth()->id();
            Item::create($data);
            return redirect()->route('admin.items.index')->with('success', 'Item created successfully.');
        } catch (Exception $e) {
            return back()->with('error', $e->getMessage())->withInput();
        }
    }

    public function show($id)
    {
        try {
            $item = Item::findOrFail($id);
            return view('admin.item.show', compact('item'));
        } catch (Exception $e) {
            return back()->with('error', $e->getMessage());
        }
    }

    public function edit($id)
    {
        try {
            $item = Item::findOrFail($id);
            return view('admin.item.edit', compact('item'));
        } catch (Exception $e) {
            return back()->with('error', $e->getMessage());
        }
    }

    public function update(Request $request, $id)
    {
        try {
            $item = Item::findOrFail($id);
            $data = $request->validate([
                'name' => 'required|string|max:255',
                'sku' => 'nullable|string|max:100|unique:items,sku,' . $item->id,
                'description' => 'nullable|string',
                'unit' => 'required|string|max:50',
                'rate' => 'required|numeric|min:0',
                'tax_percentage' => 'nullable|numeric|min:0|max:100',
                'type' => 'nullable|string|max:50',
                'hsn_code' => 'nullable|string|max:50',
                'is_active' => 'required|boolean',
            ]);
            $item->update($data);
            return redirect()->route('admin.items.index')->with('success', 'Item updated successfully.');
        } catch (Exception $e) {
            return back()->with('error', $e->getMessage())->withInput();
        }
    }

    public function destroy($id)
    {
        try {
            $item = Item::findOrFail($id);
            $item->delete();
            return redirect()->route('admin.items.index')->with('success', 'Item deleted successfully.');
        } catch (Exception $e) {
            return back()->with('error', $e->getMessage());
        }
    }

    public function search(Request $request)
    {
        try {
            $search = $request->get('q', '');
            $items = Item::where('is_active', true)
                ->where(function ($query) use ($search) {
                    $query->where('name', 'like', "%{$search}%")
                          ->orWhere('sku', 'like', "%{$search}%");
                })
                ->limit(10)
                ->get(['id', 'name', 'sku', 'rate', 'tax_percentage', 'unit']);

            return response()->json($items);
        } catch (Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }
}
