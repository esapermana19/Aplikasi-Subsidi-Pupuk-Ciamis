<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;

class ProfileController extends Controller
{
    /**
     * Tampilkan halaman profil
     */
    public function index()
    {
        $user = Auth::user();
        $activeMenu = 'profile';
        
        return view('profile.index', compact('user', 'activeMenu'));
    }

    /**
     * Update informasi profil (Email & Nama)
     */
    public function update(Request $request)
    {
        $user = Auth::user();
        $role = strtolower($user->role);

        $rules = [
            'email' => 'required|email|unique:tabel_users,email,' . $user->id_user . ',id_user',
        ];

        // Aturan khusus berdasarkan role
        if ($role === 'mitra') {
            $rules['nama_mitra'] = 'required|string|max:255';
            $rules['nama_pemilik'] = 'required|string|max:255';
        } else {
            $rules['name'] = 'required|string|max:255';
        }

        $request->validate($rules);

        // Update email di tabel_users
        $user->update([
            'email' => $request->email,
        ]);

        // Update nama di tabel profil terkait
        if ($role === 'admin' || $role === 'superadmin') {
            if ($user->admin) {
                $user->admin->update(['nama_admin' => $request->name]);
            }
        } elseif ($role === 'petani') {
            if ($user->petani) {
                $user->petani->update(['nama_petani' => $request->name]);
            }
        } elseif ($role === 'mitra') {
            if ($user->mitra) {
                $user->mitra->update([
                    'nama_mitra' => $request->nama_mitra,
                    'nama_pemilik' => $request->nama_pemilik,
                ]);
            }
        }

        return back()->with('success', 'Profil berhasil diperbarui!');
    }

    /**
     * Update password user
     */
    public function updatePassword(Request $request)
    {
        $request->validate([
            'current_password' => 'required|current_password',
            'password' => ['required', 'confirmed', Password::defaults()],
        ], [
            'current_password.current_password' => 'Password saat ini tidak cocok.',
            'password.confirmed' => 'Konfirmasi password baru tidak cocok.',
        ]);

        Auth::user()->update([
            'password' => Hash::make($request->password),
        ]);

        return back()->with('success', 'Password berhasil diubah!');
    }
}
