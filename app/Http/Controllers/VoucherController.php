<?php

namespace App\Http\Controllers;

use App\Models\Voucher;
use Illuminate\Http\Request;

class VoucherController extends Controller
{
    public function store(Request $request)
    {
        $request->validate([
            'kode' => 'required|unique:vouchers,kode|uppercase|alpha_num',
            'tipe' => 'required|in:persen,nominal',
            'nilai' => 'required|numeric|min:0',
        ]);

        Voucher::create($request->all());

        return redirect()->route('pengaturan.index')->with('toast_success', 'Voucher berhasil dibuat!');
    }

    public function destroy($id)
    {
        Voucher::destroy($id);
        return redirect()->route('pengaturan.index')->with('toast_success', 'Voucher dihapus!');
    }
    
    public function toggleStatus($id)
    {
        $voucher = Voucher::find($id);
        $voucher->update(['is_active' => !$voucher->is_active]);
        return redirect()->route('pengaturan.index');
    }
}