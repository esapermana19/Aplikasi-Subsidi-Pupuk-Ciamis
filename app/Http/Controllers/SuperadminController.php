<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Models\Admin;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;

use App\Models\LogActivity;
use Illuminate\Support\Facades\Auth;

class SuperadminController extends Controller
{
    private function logActivity($aktivitas, $fitur, $detailPerubahan = null)
    {
        LogActivity::create([
            'id_user' => Auth::id(),
            'aktivitas' => $aktivitas,
            'fitur' => $fitur,
            'detail_perubahan' => $detailPerubahan ? json_encode($detailPerubahan) : null,
            'ip_address' => request()->ip(),
            'user_agent' => request()->userAgent()
        ]);
    }

    public function index(Request $request)
    {
        $query = Admin::with('user');

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('nama_admin', 'like', "%$search%")
                  ->orWhere('nip', 'like', "%$search%")
                  ->orWhereHas('user', function($u) use ($search) {
                      $u->where('email', 'like', "%$search%");
                  });
            });
        }

        if ($request->filled('status')) {
            $status = $request->status;
            $query->whereHas('user', function($q) use ($status) {
                $q->where('status_akun', $status);
            });
        }

        $admins = $query->latest()->paginate(10);

        return view('superadmin.manageadmin', compact('admins'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'nama_admin' => 'required|string|max:50',
            'nip' => 'required|string|size:18|unique:tabel_admin,nip',
            'email' => 'required|email|unique:tabel_users,email',
            'no_hp' => 'nullable|string|max:15|unique:tabel_users,no_hp',
        ], [
            'nip.unique' => 'NIP ini sudah terdaftar sebagai Admin.',
            'nip.size' => 'NIP harus berjumlah 18 karakter.',
            'email.unique' => 'Email ini sudah terdaftar di sistem.',
            'no_hp.unique' => 'Nomor HP ini sudah terdaftar di sistem.',
        ]);

        DB::transaction(function () use ($request) {
            $user = User::create([
                'email' => $request->email,
                'password' => Hash::make('adminasup123'),
                'role' => 'admin',
                'status_akun' => 'aktif',
                'no_hp' => $request->no_hp,
            ]);

            Admin::create([
                'id_user' => $user->id_user,
                'nip' => $request->nip,
                'nama_admin' => $request->nama_admin,
            ]);

            $this->logActivity(
                "Menambah Admin Baru: {$request->nama_admin} ({$request->email})",
                'Manajemen Admin',
                ['nama_admin' => $request->nama_admin, 'nip' => $request->nip, 'email' => $request->email]
            );
        });

        return redirect()->back()->with('success', 'Admin berhasil ditambahkan');
    }

    public function update(Request $request, $id)
    {
        $admin = Admin::findOrFail($id);
        $user = $admin->user;

        $request->validate([
            'nama_admin' => 'required|string|max:50',
            'nip' => 'required|string|size:18|unique:tabel_admin,nip,' . $id . ',id_admin',
            'email' => 'required|email|unique:tabel_users,email,' . $user->id_user . ',id_user',
            'no_hp' => 'nullable|string|max:15|unique:tabel_users,no_hp,' . $user->id_user . ',id_user',
            'password' => 'nullable|min:6',
        ], [
            'nip.unique' => 'NIP ini sudah terdaftar sebagai Admin lain.',
            'nip.size' => 'NIP harus berjumlah 18 karakter.',
            'email.unique' => 'Email ini sudah terdaftar di sistem.',
            'no_hp.unique' => 'Nomor HP ini sudah terdaftar di sistem.',
        ]);

        DB::transaction(function () use ($request, $admin, $user) {
            $userData = [
                'email' => $request->email,
                'no_hp' => $request->no_hp,
            ];
            if ($request->filled('password')) {
                $userData['password'] = Hash::make($request->password);
            }
            $user->update($userData);

            $admin->update([
                'nip' => $request->nip,
                'nama_admin' => $request->nama_admin,
            ]);

            $this->logActivity(
                "Mengubah Data Admin: {$request->nama_admin} ({$user->email})",
                'Manajemen Admin',
                ['id_admin' => $admin->id_admin, 'nama_admin' => $request->nama_admin]
            );
        });

        return redirect()->back()->with('success', 'Data admin berhasil diperbarui');
    }

    public function destroy($id)
    {
        $admin = Admin::findOrFail($id);
        $user = $admin->user;

        DB::transaction(function () use ($admin, $user, $id) {
            $nama = $admin->nama_admin;
            $admin->delete();
            $user->delete();

            $this->logActivity(
                "Menghapus Admin: {$nama} ({$user->email})",
                'Manajemen Admin',
                ['nama_admin' => $nama, 'id_admin' => $id]
            );
        });

        return redirect()->back()->with('success', 'Admin berhasil dihapus');
    }

    public function update_status(Request $request, $id)
    {
        $user = User::findOrFail($id);
        $oldStatus = $user->status_akun;
        $user->update(['status_akun' => $request->status]);

        $this->logActivity(
            "Mengubah Status Akun Admin {$user->email} menjadi " . ucfirst($request->status),
            'Manajemen Admin',
            ['email' => $user->email, 'status_lama' => $oldStatus, 'status_baru' => $request->status]
        );

        return redirect()->back()->with('success', 'Status akun berhasil diperbarui');
    }

}
