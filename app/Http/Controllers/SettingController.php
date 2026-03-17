<?php

namespace App\Http\Controllers;

use App\Models\Setting;
use App\Models\ActivityLog;
use Illuminate\Http\Request;

class SettingController extends Controller
{
    public function index()
    {
        $settings = Setting::all()->pluck('value', 'key');
        return view('settings.index', compact('settings'));
    }

    public function update(Request $request)
    {
        $request->validate([
            'somity_name'          => 'required|string|max:255',
            'somity_name_en'       => 'nullable|string|max:255',
            'somity_address'       => 'nullable|string',
            'somity_address_en'    => 'nullable|string',
            'somity_phone'         => 'nullable|string|max:50',
            'somity_email'         => 'nullable|email',
            'tagline'              => 'nullable|string|max:255',
            'due_date_start'       => 'required|integer|between:1,28',
            'due_date_end'         => 'required|integer|between:1,28',
            'late_fee'             => 'required|numeric|min:0',
            'suspend_after_months' => 'required|integer|min:1|max:12',
            'entry_fee'            => 'required|numeric|min:0',
            'share_value'          => 'required|numeric|min:0',
            'max_shares'           => 'required|integer|min:1',
            'max_members'          => 'required|integer|min:20',
            'currency'             => 'required|string|max:5',
        ]);

        foreach ($request->except(['_token', '_method']) as $key => $value) {
            Setting::set($key, $value);
        }

        ActivityLog::log('update', 'সিস্টেম সেটিংস আপডেট করা হয়েছে');
        return back()->with('success', 'সেটিংস সংরক্ষণ হয়েছে।');
    }
}
