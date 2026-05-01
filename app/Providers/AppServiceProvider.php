<?php

namespace App\Providers;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        config(['app.locale' => 'id']);
        \Carbon\Carbon::setLocale('id');
        date_default_timezone_set('Asia/Jakarta');
        view()->composer('*', function ($view) {
            $count = \App\Models\User::where('status_akun', 'pending')->count();
            $view->with('pendingCount', $count);
        });

        View()->composer('*', function ($view) {
            // Menghitung permintaan pupuk yang masih 'pending'
            $pendingPermintaanCount = DB::table('tabel_permintaan')
                ->where('status_permintaan', 'pending')
                ->count();

            // (Opsional) Menghitung verifikasi jika Anda belum memasukkannya di provider
            // $pendingCount = DB::table('tabel_user')->where('status', 'pending')->count();

            $view->with('pendingPermintaanCount', $pendingPermintaanCount);
        });
    }
}
