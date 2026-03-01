<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use App\Models\Shift;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class EnsureShiftIsOpened
{
    public function handle(Request $request, Closure $next): Response
    {
        $user = Auth::user();

        // Owner & Admin tidak perlu shift
        if ($user && ($user->role === 'owner' || $user->role === 'admin')) {
            return $next($request);
        }

        if ($user) {
            $hasOpenShift = Shift::where('user_id', $user->id)
                                ->where('status', 'open')
                                ->exists();

            if (!$hasOpenShift) {
                return redirect()->route('shift.open.index')
                                 ->with('toast_danger', 'Anda harus Membuka Shift (Laci Kasir) sebelum bertransaksi.');
            }
        }

        return $next($request);
    }
}
