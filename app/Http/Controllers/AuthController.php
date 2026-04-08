<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;

class AuthController extends Controller
{
    //Proses Regist
    public function register(Request $request)
    {
        $rules = [
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users',
            'nik_nip' => 'required|string|size:16|unique:users',
            'alamat' => 'required|string',
            'password' => 'required|string|min:8|confirmed',
            'role' => 'required|in:petani,mitra',
        ];

        // Jika mitra, nama_mitra wajib diisi
        if ($request->role === 'mitra') {
            $rules['nama_mitra'] = 'required|string|max:255';
        }

        $request->validate($rules);

        // Filter Ciamis tetap ada
        if (substr($request->nik_nip, 0, 4) !== '3207') {
            throw ValidationException::withMessages(['nik_nip' => 'Hanya untuk NIK Ciamis (3207)']);
        }

        User::create([
            'name' => $request->name,
            'nama_mitra' => $request->nama_mitra, // Akan null jika petani
            'email' => $request->email,
            'nik_nip' => $request->nik_nip,
            'alamat' => $request->alamat,
            'password' => $request->password,
            'role' => $request->role,
            'status_akun' => 'pending',
        ]);

        return redirect()->route('login')->with('success', 'Registrasi berhasil, tunggu verifikasi.');
    }

    //Proses Login
    public function login(Request $request)
    {
        $credentials = $request->validate([
            'nik_nip' => 'required|string',
            'password' => 'required',
        ]);
        //Pengecekan NIK dan Password
        if (Auth::attempt($credentials)) {
            $user = Auth::user();
            //Cek Status Akun Aktif
            if ($user->status_akun !== 'aktif') {
                Auth::logout();
                $message = ($user->status_akun === 'pending')
                    ? 'Akun Anda Sedang Menunggu Verifikasi.'
                    : 'Akun Anda Ditolak. Alasan: ' . $user->alasan_penolakan;
                throw ValidationException::withMessages(['nik_nip' => $message]);
            }
            $request->session()->regenerate();
            return redirect()->intended('dashboard');
        }
        throw ValidationException::withMessages([
            'nik_nip' => 'NIK atau Password salah.',
        ]);
    }
}
