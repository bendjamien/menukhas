<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Setting;
use App\Models\Voucher; 
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Storage; // Wajib import ini

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
            'jam_masuk_kantor' => '08:00',
            'jam_pulang_kantor' => '17:00',
            'toleransi_telat' => '0',
            'company_logo' => null, // Key baru untuk logo
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
            'jam_masuk_kantor' => 'required',
            'jam_pulang_kantor' => 'required',
            'toleransi_telat' => 'required|numeric|min:0',
            // Validasi Logo (Gambar max 2MB)
            'company_logo' => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
        ]);

        // LOGIKA UPLOAD LOGO
        if ($request->hasFile('company_logo')) {
            // 1. Ambil logo lama dari DB
            $oldLogo = Setting::where('key', 'company_logo')->value('value');
            
            // 2. Hapus logo lama jika ada (biar server gak penuh)
            if ($oldLogo && Storage::disk('public')->exists($oldLogo)) {
                Storage::disk('public')->delete($oldLogo);
            }

            // 3. Simpan logo baru
            $path = $request->file('company_logo')->store('logos', 'public');
            
            // 4. Update nilai di array validated agar tersimpan ke DB
            $validated['company_logo'] = $path;
        } else {
            // Jika tidak upload, hapus key ini agar tidak menimpa data lama dengan null
            unset($validated['company_logo']);
        }

        foreach ($validated as $key => $value) {
            if ($key == 'ppn_tax_rate') {
                $value = $value / 100;
            }

            Setting::updateOrCreate(
                ['key' => $key],
                ['value' => $value]
            );

            Cache::forget($key);
        }
        
        Cache::forget('all_settings'); 

        return Redirect::route('pengaturan.index')
                         ->with('toast_success', 'Pengaturan berhasil diperbarui!');
    }
}