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
                'slogan' => 'nullable|string|max:255',
                'email' => 'required|email|max:255',
                'phone' => 'required|string|max:20',
                'address' => 'nullable|string',
                'city' => 'nullable|string|max:100',
                'state' => 'nullable|string|max:100',
                'zip_code' => 'nullable|string|max:20',
                'country' => 'nullable|string|max:100',
                'gst_number' => 'nullable|string|max:50',
                'pan_number' => 'nullable|string|max:50',
                'discount_amount' => 'nullable|numeric|min:0',
                'terms_conditions' => 'nullable|string',
                'signature' => 'nullable|string',
                'logo' => 'nullable|image|mimes:jpeg,jpg,png,gif,webp,svg|max:4096',
                'jg_logo' => 'nullable|image|mimes:jpeg,jpg,png,gif,webp,svg|max:4096',
            ]);

            if ($request->hasFile('logo')) {
                $file = $request->file('logo');
                $filename = time() . '_' . \Illuminate\Support\Str::random(20) . '.' . $file->getClientOriginalExtension();
                $folder = 'uploads/company/';
                $path = public_path($folder);
                if (!File::exists($path)) {
                    File::makeDirectory($path, $mode = 0755, true, true);
                }
                $file->move($path, $filename);
                $data['logo'] = $folder . $filename;
            }

            if ($request->hasFile('jg_logo')) {
                $file = $request->file('jg_logo');
                $filename = 'jg_' . time() . '_' . \Illuminate\Support\Str::random(20) . '.' . $file->getClientOriginalExtension();
                $folder = 'uploads/company/';
                $path = public_path($folder);
                if (!File::exists($path)) {
                    File::makeDirectory($path, $mode = 0755, true, true);
                }
                $file->move($path, $filename);
                $data['jg_logo'] = $folder . $filename;
            }

            $setting = CompanySetting::first();
            if ($setting) {
                $setting->update($data);
            } else {
                CompanySetting::create($data);
            }

            \Illuminate\Support\Facades\Cache::forget('company_settings');

            return redirect()->back()->with('success', 'Settings updated successfully.');
        } catch (Exception $e) {
            return back()->with('error', $e->getMessage())->withInput();
        }
    }
}
