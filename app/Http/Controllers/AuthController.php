<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Petani;
use App\Models\Mitra;
use App\Models\Kecamatan;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;

class AuthController extends Controller
{
    // Proses Registrasi
    public function register(Request $request)
    {
        $rules = [
            'name' => 'required|string|max:50',
            'email' => 'required|email|unique:tabel_users,email',
            'nik' => 'required|string|size:16|unique:tabel_petani,nik|unique:tabel_mitra,nik',
            'password' => 'required|string|min:8|confirmed',
            'role' => 'required|in:Petani,Mitra',
            'alamat' => 'required|string|max:100',
            'id_kecamatan' => 'required|exists:tabel_kecamatan,id_kecamatan',
            'id_desa' => 'required|exists:tabel_desa,id_desa',
        ];

        // Validasi tambahan berdasarkan role
        if ($request->role === 'Mitra') {
            $rules['nama_mitra'] = 'required|string|max:50';
            $rules['no_rek'] = 'required|string|max:20';
        } else {
            $rules['jenis_kelamin'] = 'required|in:L,P';
        }

        $request->validate($rules);

        // Filter NIK Ciamis (3207)
        if (substr($request->nik, 0, 4) !== '3207') {
            throw ValidationException::withMessages(['nik' => 'Hanya untuk penduduk berdomisili Ciamis (NIK diawali 3207)']);
        }

        // Gunakan Database Transaction agar data tersimpan di kedua tabel atau tidak sama sekali
        DB::transaction(function () use ($request) {
            // 1. Simpan ke tabel_users untuk kebutuhan Login
            $user = User::create([
                'email' => $request->email,
                'password' => Hash::make($request->password), // Gunakan Hash untuk keamanan
                'role' => $request->role,
                'status_akun' => 'pending',
            ]);

            // 2. Simpan ke tabel profil masing-masing
            if ($request->role === 'Petani') {
                Petani::create([
                    'id_user' => $user->id_user,
                    'nik' => $request->nik,
                    'nama_petani' => $request->name,
                    'jenis_kelamin' => $request->jenis_kelamin,
                    'id_kecamatan' => $request->id_kecamatan,
                    'id_desa' => $request->id_desa,
                    'alamat_petani' => $request->alamat,
                ]);
            } elseif ($request->role === 'Mitra') {
                Mitra::create([
                    'id_user' => $user->id_user,
                    'nama_mitra' => $request->nama_mitra,
                    'nama_pemilik' => $request->name,
                    'nik' => $request->nik,
                    'id_kecamatan' => $request->id_kecamatan,
                    'id_desa' => $request->id_desa,
                    'alamat_mitra' => $request->alamat,
                    'no_rek' => $request->no_rek,
                    'saldo_app' => 0,
                ]);
            }
        });

        return redirect()->route('login')->with('success', 'Registrasi berhasil, silakan tunggu verifikasi admin.');
    }

    // Proses Login
    public function login(Request $request)
    {
        // Login sekarang menggunakan email karena nik ada di tabel profil terpisah
        $credentials = $request->validate([
            'email' => 'required|email',
            'password' => 'required',
        ]);

        if (Auth::attempt($credentials)) {
            $user = Auth::user();

            // Cek apakah akun aktif
            if ($user->status_akun !== 'aktif') {
                $status = $user->status_akun;
                Auth::logout();

                $message = match ($status) {
                    'pending' => 'Akun Anda sedang menunggu verifikasi admin.',
                    'ditolak' => 'Mohon maaf, pengajuan akun Anda ditolak.',
                    'nonaktif' => 'Akun Anda telah dinonaktifkan.',
                    default => 'Status akun tidak valid.'
                };

                throw ValidationException::withMessages(['email' => $message]);
            }

            $request->session()->regenerate();
            return redirect()->route('dashboard');
        }

        throw ValidationException::withMessages([
            'email' => 'Email atau password yang Anda masukkan salah.',
        ]);
    }

    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        return redirect('/');
    }

    public function create() // atau showRegistrationForm()
    {
        // Mengambil semua data kecamatan dari database
        $kecamatans = Kecamatan::all();

        // Kirim variabel ke view register
        return view('auth.register', compact('kecamatans'));
    }
}
