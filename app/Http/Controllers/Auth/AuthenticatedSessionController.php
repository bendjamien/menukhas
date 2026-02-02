<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class AuthenticatedSessionController extends Controller
{
    /**
     * Display the login view.
     */
    public function create()
{
    $logoPath = \App\Models\Setting::where('key', 'company_logo')->value('value');
    return view('auth.login', compact('logoPath'));
}

    /**
     * Handle an incoming authentication request.
     */
    public function store(LoginRequest $request): RedirectResponse
    {
        $request->authenticate();
        $request->session()->regenerate();

        // Sekarang baris ini akan berfungsi dengan benar
        return redirect()->intended(route('dashboard'))
                         ->with('success', 'Selamat datang kembali!'); 
    }

    /**
     * Destroy an authenticated session.
     */
    public function destroy(Request $request): RedirectResponse
    {
        Auth::guard('web')->logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        // Tambahkan ->with(...) di sini
        return redirect('/login')
               ->with('success', 'Anda telah berhasil logout.');
    }
}