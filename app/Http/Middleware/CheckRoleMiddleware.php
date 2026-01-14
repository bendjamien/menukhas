<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class CheckRoleMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next, ...$roles): Response
    {
        if (!Auth::check()) {
            return redirect()->route('login')->with('session_expired', true);
        }

        $userRole = Auth::user()->role;

        foreach ($roles as $role) {
            if ($userRole == $role) {
                return $next($request);
            }
        }

        abort(403, 'AKSES DITOLAK. ANDA TIDAK MEMILIKI HAK AKSES.');
    }
}