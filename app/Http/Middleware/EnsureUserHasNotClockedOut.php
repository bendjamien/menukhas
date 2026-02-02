<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Facades\Auth;
use App\Models\Absensi;
use Carbon\Carbon;

class EnsureUserHasNotClockedOut
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $user = Auth::user();

        if ($user) {
            $today = Carbon::now('Asia/Jakarta')->format('Y-m-d');
            
            // Cek apakah ada record absensi hari ini dan sudah pulang
            $alreadyClockedOut = Absensi::where('user_id', $user->id)
                ->where('tanggal', $today)
                ->whereNotNull('waktu_keluar')
                ->exists();

            if ($alreadyClockedOut) {
                // IZINKAN AKSES KE DASHBOARD, PROFILE, CHAT, DAN LOGOUT MESKI SUDAH PULANG
                $allowedRoutes = ['dashboard', 'profile.edit', 'profile.update', 'profile.destroy', 'logout', 'chat.send', 'absensi.store', 'absensi.clock_out'];
                if (in_array($request->route()->getName(), $allowedRoutes)) {
                    return $next($request);
                }

                // Jika request AJAX (seperti search produk, scan barcode, dll)
                if ($request->expectsJson()) {
                    return response()->json([
                        'message' => 'Sesi Terkunci. Anda sudah absen pulang hari ini.'
                    ], 403);
                }

                // Jika akses halaman biasa (POS, Laporan, dll), tampilkan halaman error/lock
                return response()->view('errors.locked_out', [], 403);
            }
        }

        return $next($request);
    }
}
