<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth; 
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Password;
use SimpleSoftwareIO\QrCode\Facades\QrCode; 

class UserController extends Controller
{
    public function index()
    {
        $users = User::latest()->paginate(10);
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
            'role' => ['required', Rule::in(['admin', 'kasir', 'owner'])],
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
            'role' => ['required', Rule::in(['admin', 'kasir', 'owner'])],
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

    public function approvePin(User $user)
    {
        if ($user->request_new_pin && $user->pending_pin) {
            $user->pin = $user->pending_pin;
            $user->pending_pin = null;
            $user->request_new_pin = false;
            $user->save();
            return redirect()->back()->with('toast_success', 'PIN berhasil disetujui dan diperbarui!');
        }
        return redirect()->back()->with('toast_danger', 'Tidak ada permintaan PIN untuk user ini.');
    }

    public function cetakKartu($id)
    {
        $user = User::findOrFail($id);
        
        $qrContent = 'ID-' . $user->id;
        
        $qrCode = QrCode::size(200)->generate($qrContent);

        return view('users.kartu', compact('user', 'qrCode'));
    }

    public function viewPin(Request $request, User $user)
    {
        $request->validate([
            'admin_password' => 'required'
        ]);

        if (!Hash::check($request->admin_password, Auth::user()->password)) {
            return response()->json(['status' => 'error', 'message' => 'Password Admin salah!'], 403);
        }

        return response()->json([
            'status' => 'success', 
            'pin' => $user->pin ? $user->pin : 'Belum diatur'
        ]);
    }

    public function resetPin(Request $request, User $user)
    {
        $request->validate([
            'admin_password' => 'required',
            'new_pin' => 'required|numeric|digits:6'
        ]);

        if (!Hash::check($request->admin_password, Auth::user()->password)) {
            return response()->json(['status' => 'error', 'message' => 'Password Admin salah!'], 403);
        }

        $user->update([
            'pin' => $request->new_pin,
            'pin_attempts' => 0,
            'is_pin_blocked' => false
        ]);

        return response()->json([
            'status' => 'success', 
            'message' => 'PIN berhasil direset!'
        ]);
    }
}