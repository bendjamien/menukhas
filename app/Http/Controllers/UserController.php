<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth; 
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Password;

class UserController extends Controller
{
    public function index()
    {
        $users = User::paginate(10);
        return view('users.index', compact('users'));
    }

    public function create()
    {
        return view('users.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'username' => 'required|string|max:255|unique:users,username',
            'email' => 'required|string|email|max:255|unique:users,email',
            'role' => ['required', Rule::in(['admin', 'kasir'])],
            'status' => 'required|boolean',
            'password' => ['required', 'confirmed', Password::min(8)],
        ]);

        User::create([
            'name' => $validated['name'],
            'username' => $validated['username'],
            'email' => $validated['email'],
            'role' => $validated['role'],
            'status' => $validated['status'],
            'password' => Hash::make($validated['password']),
        ]);

        return redirect()->route('users.index')->with('toast_success', 'User baru berhasil ditambahkan!');
    }

    public function edit(User $user)
    {
        return view('users.edit', compact('user'));
    }

    public function update(Request $request, User $user)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'username' => ['required', 'string', 'max:255', Rule::unique('users')->ignore($user->id)],
            'email' => ['required', 'string', 'email', 'max:255', Rule::unique('users')->ignore($user->id)],
            'role' => ['required', Rule::in(['admin', 'kasir'])],
            'status' => 'required|boolean',
            'password' => ['nullable', 'confirmed', Password::min(8)], 
        ]);

        $dataToUpdate = [
            'name' => $validated['name'],
            'username' => $validated['username'],
            'email' => $validated['email'],
            'role' => $validated['role'],
            'status' => $validated['status'],
        ];

        if (!empty($validated['password'])) {
            $dataToUpdate['password'] = Hash::make($validated['password']);
        }

        $user->update($dataToUpdate);

        return redirect()->route('users.index')->with('toast_success', 'Data user berhasil diperbarui!');
    }


    public function toggleStatus(User $user)
    {
        if ($user->id == Auth::id()) {
            return redirect()->back()->with('toast_danger', 'Anda tidak bisa menonaktifkan akun sendiri.');
        }

        $user->status = !$user->status;
        $user->save();

        $statusMsg = $user->status ? 'diaktifkan kembali.' : 'dinonaktifkan.';
        
        return redirect()->back()->with('toast_success', 'Akun ' . $user->name . ' berhasil ' . $statusMsg);
    }
}