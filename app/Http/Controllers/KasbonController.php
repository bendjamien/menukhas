<?php

namespace App\Http\Controllers;

use App\Models\Kasbon;
use App\Models\User;
use App\Models\PengaturanGaji;
use Illuminate\Http\Request;
use Carbon\Carbon;
use Illuminate\Support\Facades\Cache;
use App\Models\Setting;

class KasbonController extends Controller
{
    public function index()
    {
        $kasbons = Kasbon::with('user')->latest('tanggal')->paginate(10);
        return view('kasbon.index', compact('kasbons'));
    }

    public function create()
    {
        $users = User::where('role', '!=', 'owner')->where('status', true)->get();
        return view('kasbon.create', compact('users'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'user_id' => 'required|exists:users,id',
            'nominal' => 'required|numeric|min:1',
            'tanggal' => 'required|date',
            'keterangan' => 'nullable|string',
        ]);

        $kasbon = Kasbon::create([
            'user_id' => $request->user_id,
            'nominal' => $request->nominal,
            'tanggal' => $request->tanggal,
            'keterangan' => $request->keterangan,
            'status' => 'pending'
        ]);

        return redirect()->route('kasbon.index')
            ->with('toast_success', 'Kasbon dicatat.')
            ->with('print_kasbon_id', $kasbon->id);
    }

    public function cetakStruk(Kasbon $kasbon)
    {
        $kasbon->load('user');
        $pengaturanGaji = PengaturanGaji::where('user_id', $kasbon->user_id)->first();
        $gajiPokok = $pengaturanGaji ? $pengaturanGaji->gaji_pokok : 0;
        
        $totalKasbonBulanIni = Kasbon::where('user_id', $kasbon->user_id)
            ->where('status', 'pending')
            ->whereMonth('tanggal', $kasbon->tanggal->month)
            ->whereYear('tanggal', $kasbon->tanggal->year)
            ->sum('nominal');

        $sisaGaji = $gajiPokok - $totalKasbonBulanIni;

        $settings = Cache::rememberForever('all_settings', function () {
            return Setting::all()->pluck('value', 'key');
        });

        return view('kasbon.cetak-struk', compact('kasbon', 'totalKasbonBulanIni', 'sisaGaji', 'settings'));
    }
}
