<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Setting;
use App\Models\Voucher; 
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Redirect;

class PengaturanController extends Controller
{
    public function index()
    {
        $keys = [
            'ppn_tax_rate' => '0.11', 
            'company_name' => 'MenuKhas',
            'company_website' => 'www.menukhas.com',
            'company_email' => 'menukhas@gmail.com',
            'company_phone' => '+62 5722136836',
            'company_address' => 'Cianjur, Jawa Barat, Indonesia - 43284',
            'company_tax_id' => '00XXXX1234X0XX',
        ];

        $settings = [];
        foreach ($keys as $key => $defaultValue) {
            $setting = Setting::firstOrCreate(['key' => $key], ['value' => $defaultValue]);
            
            if ($key == 'ppn_tax_rate') {
                $settings[$key] = (float) $setting->value * 100;
            } else {
                $settings[$key] = $setting->value;
            }
        }

        $vouchers = Voucher::orderBy('id', 'desc')->get();

        return view('pengaturan.index', compact('settings', 'vouchers'));
    }

    public function update(Request $request)
    {
        $validated = $request->validate([
            'ppn_tax_rate' => 'required|numeric|min:0|max:100',
            'company_name' => 'required|string|max:100',
            'company_website' => 'nullable|string|max:100',
            'company_email' => 'nullable|email|max:100',
            'company_phone' => 'nullable|string|max:30',
            'company_address' => 'nullable|string|max:255',
            'company_tax_id' => 'nullable|string|max:100',
        ]);

        foreach ($validated as $key => $value) {
            
            if ($key == 'ppn_tax_rate') {
                $value = $value / 100;
            }

            Setting::updateOrCreate(
                ['key' => $key],
                ['value' => $value]
            );

            Cache::forget($key);
            Cache::forget('all_settings'); 
        }

        return Redirect::route('pengaturan.index')
                         ->with('toast_success', 'Pengaturan berhasil diperbarui!');
    }
}