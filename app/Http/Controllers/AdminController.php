<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;

class AdminController extends Controller
{
    public function index()
    {
        $user = User::where('status_akun','pending')->get();
        return view('admin.verifikasi', compact('users'));
    }
    public function approve($id)
    {
        $user = User::findOrFail($id);
        $user->update(['status_akun' => 'aktif']);

        return back()->with('success', 'Akun ' . $user->name . ' berhasil diaktifkan!');
    }
}
