<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Item;
use Illuminate\Http\Request;
use Exception;
use Maatwebsite\Excel\Facades\Excel;
use App\Imports\ItemImport;
use App\Exports\ItemTemplateExport;

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
                'image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            ]);
            
            $data['is_active'] = true;
            
            if ($request->hasFile('image')) {
                $image = $request->file('image');
                $filename = time() . '_' . \Illuminate\Support\Str::random(20) . '.' . $image->getClientOriginalExtension();
                $image->move(public_path('uploads/items'), $filename);
                $data['image'] = 'uploads/items/' . $filename;
            }
            
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
                'image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            ]);
            
            if ($request->hasFile('image')) {
                $image = $request->file('image');
                $filename = time() . '_' . \Illuminate\Support\Str::random(20) . '.' . $image->getClientOriginalExtension();
                $image->move(public_path('uploads/items'), $filename);
                $data['image'] = 'uploads/items/' . $filename;
            }
            
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

    public function import(Request $request)
    {
        ini_set('max_execution_time', 600);
        ini_set('memory_limit', '512M');

        $request->validate([
            'file' => 'required|mimes:csv,txt,xlsx,xls',
        ]);

        try {
            Excel::import(new ItemImport, $request->file('file'));
            return redirect()->route('admin.items.index')->with('success', 'Items imported successfully.');
        } catch (Exception $e) {
            return back()->with('error', $e->getMessage());
        }
    }

    public function updateImage(Request $request, $id)
    {
        try {
            $request->validate([
                'image' => 'required|image|mimes:jpeg,png,jpg,gif,webp|max:2048',
            ]);

            $item = Item::findOrFail($id);

            if ($request->hasFile('image')) {
                $image = $request->file('image');
                $filename = time() . '_' . \Illuminate\Support\Str::random(20) . '.' . $image->getClientOriginalExtension();
                $image->move(public_path('uploads/items'), $filename);
                $relativePath = 'uploads/items/' . $filename;

                $item->update(['image' => $relativePath]);

                return response()->json([
                    'success' => true,
                    'message' => 'Item image updated successfully.',
                    'image_url' => asset($relativePath)
                ]);
            }

            return response()->json(['success' => false, 'message' => 'No image file uploaded.'], 400);
        } catch (Exception $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }

    public function downloadTemplate()
    {
        return Excel::download(new ItemTemplateExport, 'item_import_template.xlsx');
    }

    public function syncImages(Request $request)
    {
        try {
            \Illuminate\Support\Facades\Artisan::call('products:sync-images', [
                '--chunk' => 100,
            ]);

            $output = \Illuminate\Support\Facades\Artisan::output();

            return response()->json([
                'success' => true,
                'message' => 'Product image sync completed successfully!',
                'output' => $output,
            ]);
        } catch (Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to sync product images: ' . $e->getMessage(),
            ], 500);
        }
    }
}
