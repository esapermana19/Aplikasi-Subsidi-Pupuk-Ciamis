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
        // 1. Kumpulan Aturan Validasi
        $rules = [
            'name'         => 'required|string|max:50',
            'email'        => 'required|email|unique:tabel_users,email',
            'nik'          => 'required|string|size:16|unique:tabel_petani,nik|unique:tabel_mitra,nik',
            'password'     => 'required|string|min:8|confirmed',
            'role'         => 'required|in:Petani,Mitra',
            'alamat'       => 'required|string|max:100',
            'id_kecamatan' => 'required|exists:tabel_kecamatan,id_kecamatan',
            'id_desa'      => 'required|exists:tabel_desa,id_desa',
        ];

        // Validasi tambahan berdasarkan role
        if ($request->role === 'Mitra') {
            $rules['nama_mitra'] = 'required|string|max:50';
            $rules['no_rek']     = 'required|string|max:20';
        } else {
            $rules['jenis_kelamin'] = 'required|in:L,P';
            $rules['no_kk']         = 'required|string|size:16|unique:tabel_petani,no_kk';
        }

        // 2. Kumpulan Pesan Validasi Kustom
        $messages = [
            // Name
            'name.required'         => 'Nama lengkap wajib diisi.',
            'name.max'              => 'Nama lengkap maksimal 50 karakter.',

            // Email
            'email.required'        => 'Alamat email wajib diisi.',
            'email.email'           => 'Format email tidak valid (contoh: nama@email.com).',
            'email.unique'          => 'Email ini sudah terdaftar. Silakan gunakan email lain.',

            // No KK
            'no_kk.required'        => 'Nomor Kartu Keluarga (KK) wajib diisi.',
            'no_kk.size'            => 'Nomor KK harus tepat 16 digit angka.',
            'no_kk.unique'          => 'Nomor KK ini sudah terdaftar. Satu KK hanya boleh mendaftar satu kali.',

            // NIK
            'nik.required'          => 'Nomor Induk Kependudukan (NIK) wajib diisi.',
            'nik.size'              => 'NIK harus tepat 16 digit angka.',
            'nik.unique'            => 'NIK ini sudah terdaftar di sistem. Silakan login atau hubungi admin.',

            // Password
            'password.required'     => 'Password wajib diisi.',
            'password.min'          => 'Password minimal harus 8 karakter.',
            'password.confirmed'    => 'Konfirmasi password tidak cocok dengan password yang dimasukkan.',

            // Role
            'role.required'         => 'Peran pendaftar (Role) wajib dipilih.',
            'role.in'               => 'Pilihan peran tidak valid.',

            // Alamat
            'alamat.required'       => 'Alamat lengkap wajib diisi.',
            'alamat.max'            => 'Alamat maksimal 100 karakter.',

            // Wilayah
            'id_kecamatan.required' => 'Kecamatan wajib dipilih.',
            'id_kecamatan.exists'   => 'Kecamatan yang dipilih tidak valid di sistem kami.',
            'id_desa.required'      => 'Desa wajib dipilih.',
            'id_desa.exists'        => 'Desa yang dipilih tidak valid di sistem kami.',

            // Tambahan Role Mitra
            'nama_mitra.required'   => 'Nama Mitra/Kios wajib diisi.',
            'nama_mitra.max'        => 'Nama Mitra maksimal 50 karakter.',
            'no_rek.required'       => 'Nomor rekening wajib diisi.',
            'no_rek.max'            => 'Nomor rekening maksimal 20 karakter.',

            // Tambahan Role Petani
            'jenis_kelamin.required' => 'Jenis kelamin wajib dipilih.',
            'jenis_kelamin.in'      => 'Pilihan jenis kelamin tidak valid.',
        ];

        // Eksekusi validasi beserta pesan kustomnya
        $request->validate($rules, $messages);

        // Filter NIK Ciamis (3207)
        if (substr($request->nik, 0, 4) !== '3207') {
            throw ValidationException::withMessages([
                'nik' => 'Pendaftaran hanya untuk penduduk berdomisili Kabupaten Ciamis (NIK diawali dengan 3207).'
            ]);
        }

        // Gunakan Database Transaction agar data tersimpan di kedua tabel atau tidak sama sekali
        DB::transaction(function () use ($request) {
            // 1. Simpan ke tabel_users untuk kebutuhan Login
            $user = User::create([
                'email'       => $request->email,
                'password'    => Hash::make($request->password), // Gunakan Hash untuk keamanan
                'role'        => $request->role,
                'status_akun' => 'pending',
            ]);

            // 2. Simpan ke tabel profil masing-masing
            if ($request->role === 'Petani') {
                Petani::create([
                    'id_user'       => $user->id_user,
                    'nik'           => $request->nik,
                    'no_kk'         => $request->no_kk, // Simpan no_kk ke database
                    'nama_petani'   => $request->name,
                    'jenis_kelamin' => $request->jenis_kelamin,
                    'id_kecamatan'  => $request->id_kecamatan,
                    'id_desa'       => $request->id_desa,
                    'alamat_petani' => $request->alamat,
                ]);
            } elseif ($request->role === 'Mitra') {
                Mitra::create([
                    'id_user'      => $user->id_user,
                    'nama_mitra'   => $request->nama_mitra,
                    'nama_pemilik' => $request->name,
                    'nik'          => $request->nik,
                    'id_kecamatan' => $request->id_kecamatan,
                    'id_desa'      => $request->id_desa,
                    'alamat_mitra' => $request->alamat,
                    'no_rek'       => $request->no_rek,
                    'saldo_app'    => 0,
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

            // 1. Cek apakah akun aktif
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

            // 2. Regenerasi session untuk keamanan
            $request->session()->regenerate();

            // 3. Logika Pengalihan Berdasarkan Role
            if ($user->role === 'admin' || $user->role === 'superadmin') {
                return redirect()->route('admin.dashboard');
            } elseif ($user->role === 'petani') {
                return redirect()->route('petani.dashboard');
            } elseif ($user->role === 'mitra') {
                return redirect()->route('mitra.dashboard');
            }

            // default
            return redirect('/');
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
