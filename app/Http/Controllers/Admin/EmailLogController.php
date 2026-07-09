<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\EmailLog;
use Illuminate\Http\Request;
use Exception;

class EmailLogController extends Controller
{
    public function index(Request $request)
    {
        try {
            $query = EmailLog::with(['quotation', 'user']);

            if ($request->filled('status')) {
                $query->where('status', $request->status);
            }

            $emailLogs = $query->latest()->paginate(10);
            return view('admin.email_log.index', compact('emailLogs'));
        } catch (Exception $e) {
            return back()->with('error', $e->getMessage());
        }
    }
}
