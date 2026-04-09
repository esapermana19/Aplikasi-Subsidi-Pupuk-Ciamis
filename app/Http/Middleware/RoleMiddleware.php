<?php

namespace App\Http\Middleware;

use Illuminate\Support\Facades\Auth;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use App\Models\User;

class RoleMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     * @param  string  ...$roles  <-- Tambahkan titik tiga (variadic parameter)
     */
    public function handle(Request $request, Closure $next, ...$roles): Response
    {
        // 1. Pastikan User sudah login
        if (!Auth::check()) {
            return redirect()->route('login');
        }

        /** @var \App\Models\User $user */
        $user = Auth::user();

        // 2. Cek apakah role user ada di dalam daftar roles yang dikirim dari Route
        // in_array sekarang akan mengecek array $roles (misal: ['admin', 'petani'])
        if (!in_array($user->role, $roles)) {
            abort(403, 'Maaf, Anda tidak memiliki akses ke halaman ini.');
        }

        return $next($request);
    }
}
