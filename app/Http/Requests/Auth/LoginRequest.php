<?php

namespace App\Http\Requests\Auth;

use Illuminate\Auth\Events\Lockout;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;

class LoginRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     */
    public function rules(): array
    {
        return [
            'email' => ['required', 'string', 'email'],
            'password' => ['required', 'string'],
            'role_check' => ['nullable', 'string'], // Validasi input hidden role
        ];
    }

    /**
     * Attempt to authenticate the request's credentials.
     *
     * @throws \Illuminate\Validation\ValidationException
     */
    public function authenticate(): void
    {
        $this->ensureIsNotRateLimited();

        // 1. Coba Login Standar (Cek Email & Password)
        if (! Auth::attempt($this->only('email', 'password'), $this->boolean('remember'))) {
            RateLimiter::hit($this->throttleKey());

            throw ValidationException::withMessages([
                'email' => trans('auth.failed'),
            ]);
        }

        // Ambil data user yang baru saja login
        $user = Auth::user();
        
        // 2. CEK STATUS AKTIF/NONAKTIF
        if ($user->status == 0) {
            Auth::logout(); // Logout paksa
            $this->session()->invalidate();
            $this->session()->regenerateToken();

            throw ValidationException::withMessages([
                'email' => 'Akun dinonaktifkan. Hubungi Admin.',
            ]);
        }

        // 3. CEK KESESUAIAN ROLE (FITUR BARU)
        // Mengambil data dari hidden input <input name="role_check">
        $selectedRole = $this->input('role_check'); 

        // Jika user memilih tombol (ada selectedRole) DAN rolenya tidak cocok
        if ($selectedRole && $user->role !== $selectedRole) {
            
            Auth::logout(); // Logout paksa
            $this->session()->invalidate();
            $this->session()->regenerateToken();

            throw ValidationException::withMessages([
                'email' => 'Akses Ditolak! Akun ini adalah ' . ucfirst($user->role) . ', silakan login di menu yang benar.',
            ]);
        }

        // 4. Jika semua lolos, bersihkan rate limiter dan kirim notif sukses
        session()->flash('toast_success', 'Login berhasil! Selamat datang, ' . $user->name);
        RateLimiter::clear($this->throttleKey());
    }

    /**
     * Ensure the login request is not rate limited.
     *
     * @throws \Illuminate\Validation\ValidationException
     */
    public function ensureIsNotRateLimited(): void
    {
        if (! RateLimiter::tooManyAttempts($this->throttleKey(), 5)) {
            return;
        }

        event(new Lockout($this));

        $seconds = RateLimiter::availableIn($this->throttleKey());

        throw ValidationException::withMessages([
            'email' => trans('auth.throttle', [
                'seconds' => $seconds,
                'minutes' => ceil($seconds / 60),
            ]),
        ]);
    }

    /**
     * Get the rate limiting throttle key for the request.
     */
    public function throttleKey(): string
    {
        return Str::transliterate(Str::lower($this->input('email')).'|'.$this->ip());
    }
}