<?php

namespace App\Http\Controllers;

use App\Http\Requests\ProfileUpdateRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Redirect;
use Illuminate\View\View;

class ProfileController extends Controller
{
    public function edit(Request $request): View
    {
        return view('profile.edit', [
            'user' => $request->user(),
        ]);
    }
    public function update(ProfileUpdateRequest $request): RedirectResponse
    {
        $request->user()->fill($request->validated());


        $request->user()->save();
        return Redirect::route('profile.edit')->with('toast_success', 'Profil berhasil diperbarui!');
    }

    public function requestPinChange(Request $request): RedirectResponse
    {
        $request->validate([
            'pin' => ['required', 'digits:6', 'numeric', 'unique:users,pin'],
        ]);

        $user = $request->user();
        $user->pending_pin = $request->pin;
        $user->request_new_pin = true;
        $user->save();

        return Redirect::route('profile.edit')->with('toast_success', 'Permintaan ganti PIN berhasil dikirim ke Admin!');
    }

    public function destroy(Request $request): RedirectResponse
    {
        $request->validateWithBag('userDeletion', [
            'password' => ['required', 'current_password'],
        ]);

        $user = $request->user();

        Auth::logout();

        $user->delete();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return Redirect::to('/');
    }
}