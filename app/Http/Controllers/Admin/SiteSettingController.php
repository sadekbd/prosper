<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\SiteSetting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class SiteSettingController extends Controller
{
    public function index()
    {
        
        $settingGroups = SiteSetting::orderBy('group')
                                    ->orderBy('id')
                                    ->get()
                                    ->groupBy('group');

        return view('admin.settings.index', compact('settingGroups'));
    }

    public function update(Request $request)
    {
        $inputs = $request->except(['_token', '_method']);

        foreach ($inputs as $key => $value) {
            $setting = SiteSetting::where('key', $key)->first();
            if (!$setting) continue;

            if ($setting->type === 'image' && $request->hasFile($key)) {
                if ($setting->value) {
                    Storage::disk('public')->delete($setting->value);
                }
                $value = $request->file($key)->store('uploads/settings', 'public');
            } elseif ($setting->type === 'image') {
                continue; // no new file — keep existing image
            }

            $setting->update(['value' => $value]);
        }

        SiteSetting::clearCache();

        return back()->with('success', 'Settings saved successfully.');
    }
}