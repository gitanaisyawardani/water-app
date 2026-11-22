<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Setting;
use Illuminate\Http\Request;

class SettingController extends Controller
{
    public function index()
    {
        $settings = Setting::all()->keyBy('key');
        return view('admin.settings.index', compact('settings'));
    }

    public function update(Request $request)
    {
        $validated = $request->validate([
            'depo_name'      => 'required|string|max:255',
            'depo_address'   => 'required|string',
            'depo_latitude'  => 'required|numeric|between:-90,90',
            'depo_longitude' => 'required|numeric|between:-180,180',
            'rate_per_km'    => 'required|numeric|min:0',
            'price_7000'     => 'required|numeric|min:0',
            'price_8000'     => 'required|numeric|min:0',
            'price_16000'    => 'required|numeric|min:0',
            'price_20000'    => 'required|numeric|min:0',
        ]);

        foreach ($validated as $key => $value) {
            Setting::updateOrCreate(
                ['key' => $key],
                ['value' => $value]
            );
        }

        return redirect()->route('admin.settings.index')->with('success', 'Konfigurasi harga dasar air dan tarif KM berhasil disimpan!');
    }
}
