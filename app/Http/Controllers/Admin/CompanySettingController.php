<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\CompanySetting;
use Illuminate\Http\Request;
use File, Exception;

class CompanySettingController extends Controller
{
    public function index()
    {
        try {
            $setting = CompanySetting::first();
            return view('admin.setting.index', compact('setting'));
        } catch (Exception $e) {
            return back()->with('error', $e->getMessage());
        }
    }

    public function update(Request $request)
    {
        try {
            $data = $request->validate([
                'company_name' => 'required|string|max:255',
                'email' => 'required|email|max:255',
                'phone' => 'required|string|max:20',
                'address' => 'nullable|string',
                'city' => 'nullable|string|max:100',
                'state' => 'nullable|string|max:100',
                'zip_code' => 'nullable|string|max:20',
                'country' => 'nullable|string|max:100',
                'gst_number' => 'nullable|string|max:50',
                'pan_number' => 'nullable|string|max:50',
                'terms_conditions' => 'nullable|string',
                'signature' => 'nullable|string',
                'logo' => 'nullable|image|mimes:jpeg,jpg,png,gif|max:2048',
            ]);

            if ($request->hasFile('logo')) {
                $file = $request->file('logo');
                $filename = time() . '_' . $file->getClientOriginalName();
                $folder = 'uploads/company/';
                $path = public_path($folder);
                if (!File::exists($path)) {
                    File::makeDirectory($path, $mode = 0777, true, true);
                }
                $file->move($path, $filename);
                $data['logo'] = $folder . $filename;
            }

            $setting = CompanySetting::first();
            if ($setting) {
                $setting->update($data);
            } else {
                CompanySetting::create($data);
            }

            return redirect()->back()->with('success', 'Settings updated successfully.');
        } catch (Exception $e) {
            return back()->with('error', $e->getMessage())->withInput();
        }
    }
}
