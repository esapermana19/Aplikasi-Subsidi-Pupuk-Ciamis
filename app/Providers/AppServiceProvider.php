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

            // Menghitung permintaan penarikan yang masih 'pending'
            $pendingPenarikanCount = DB::table('tabel_penarikan')
                ->where('status', 'pending')
                ->count();

            // Menghitung data rekonsiliasi yang tidak match (selisih)
            // Menggunakan Raw Query agar performa tetap ringan (menghindari N+1 Query)
            $mismatchQuery = DB::selectOne("
                SELECT COUNT(*) as selisih
                FROM tabel_mitra m
                WHERE m.saldo_app != (
                    COALESCE((SELECT SUM(total) FROM tabel_transaksi t WHERE t.id_mitra = m.id_mitra AND t.status_pembayaran = 'success'), 0) -
                    COALESCE((SELECT SUM(jml_transfer) FROM tabel_penarikan p WHERE p.id_mitra = m.id_mitra AND p.status = 'success'), 0)
                )
            ");
            $mismatchRekonsiliasiCount = $mismatchQuery->selisih ?? 0;

            $view->with('pendingPermintaanCount', $pendingPermintaanCount);
            $view->with('pendingPenarikanCount', $pendingPenarikanCount);
            $view->with('mismatchRekonsiliasiCount', $mismatchRekonsiliasiCount);
        });
    }
}
