<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\FollowUp;
use App\Models\Quotation;
use Illuminate\Http\Request;
use Exception;

class FollowUpController extends Controller
{
    public function index(Request $request)
    {
        try {
            $query = FollowUp::with(['quotation.customer', 'user']);

            if ($request->filled('status')) {
                $query->where('status', $request->status);
            }

            if ($request->filled('from_date')) {
                $query->where('follow_up_date', '>=', $request->from_date);
            }

            if ($request->filled('to_date')) {
                $query->where('follow_up_date', '<=', $request->to_date);
            }

            $followUps = $query->latest()->paginate(10);
            return view('admin.followup.index', compact('followUps'));
        } catch (Exception $e) {
            return back()->with('error', $e->getMessage());
        }
    }

    public function create()
    {
        try {
            $quotations = Quotation::with('customer')->latest()->get();
            return view('admin.followup.create', compact('quotations'));
        } catch (Exception $e) {
            return back()->with('error', $e->getMessage());
        }
    }

    public function edit($id)
    {
        try {
            $followUp = FollowUp::findOrFail($id);
            $quotations = Quotation::with('customer')->latest()->get();
            return view('admin.followup.edit', compact('followUp', 'quotations'));
        } catch (Exception $e) {
            return back()->with('error', $e->getMessage());
        }
    }

    public function store(Request $request)
    {
        try {
            $data = $request->validate([
                'quotation_id' => 'required|exists:quotations,id',
                'follow_up_date' => 'required|date',
                'follow_up_time' => 'nullable',
                'notes' => 'nullable|string',
                'status' => 'required|in:pending,completed,cancelled',
            ]);

            $data['user_id'] = auth()->id();
            FollowUp::create($data);

            return redirect()->back()->with('success', 'Follow-up created successfully.');
        } catch (Exception $e) {
            return back()->with('error', $e->getMessage())->withInput();
        }
    }

    public function update(Request $request, $id)
    {
        try {
            $followUp = FollowUp::findOrFail($id);

            $data = $request->validate([
                'quotation_id' => 'required|exists:quotations,id',
                'follow_up_date' => 'required|date',
                'follow_up_time' => 'nullable',
                'notes' => 'nullable|string',
                'status' => 'required|in:pending,completed,cancelled',
            ]);

            $followUp->update($data);

            return redirect()->back()->with('success', 'Follow-up updated successfully.');
        } catch (Exception $e) {
            return back()->with('error', $e->getMessage())->withInput();
        }
    }

    public function destroy($id)
    {
        try {
            $followUp = FollowUp::findOrFail($id);
            $followUp->delete();
            return redirect()->back()->with('success', 'Follow-up deleted successfully.');
        } catch (Exception $e) {
            return back()->with('error', $e->getMessage());
        }
    }

    public function today()
    {
        try {
            $followUps = FollowUp::with(['quotation.customer', 'user'])
                ->whereDate('follow_up_date', today())
                ->latest()
                ->get();

            if (request()->wantsJson()) {
                return response()->json($followUps);
            }

            return view('admin.followup.today', compact('followUps'));
        } catch (Exception $e) {
            return back()->with('error', $e->getMessage());
        }
    }

    public function upcoming()
    {
        try {
            $followUps = FollowUp::with(['quotation.customer', 'user'])
                ->whereDate('follow_up_date', '>', today())
                ->where('status', 'pending')
                ->latest('follow_up_date')
                ->get();

            return view('admin.followup.upcoming', compact('followUps'));
        } catch (Exception $e) {
            return back()->with('error', $e->getMessage());
        }
    }
}
