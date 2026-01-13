<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Absensi;

class LaporanAbsensiController extends Controller
{
    public function index()
    {
        $absensis = Absensi::with('user')->latest()->paginate(10);

        return view('laporan.absensi', compact('absensis'));
    }
}