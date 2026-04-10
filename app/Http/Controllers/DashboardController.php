<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        return match($user->role) {
            'admin','superadmin' => view('admin.dashboard'),
            'mitra' => view('mitra.dashboard'),
            'petani' => view('petani.dashboard'),
            default => abort(403),
        };
    }
}
