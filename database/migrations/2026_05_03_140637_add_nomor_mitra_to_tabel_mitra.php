<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('tabel_mitra', function (Blueprint $table) {
            $table->string('nomor_mitra', 8)->nullable()->after('id_mitra')->unique();
        });

        // Generate nomor_mitra for existing records
        $mitras = DB::table('tabel_mitra')->orderBy('id_kecamatan')->orderBy('id_desa')->orderBy('id_mitra')->get();
        $counters = [];

        foreach ($mitras as $mitra) {
            $id_kecamatan = $mitra->id_kecamatan ?? '00'; // Fallback if null, but shouldn't be null
            $id_desa = $mitra->id_desa ?? '0000'; // Fallback

            $key = $id_kecamatan . '_' . $id_desa;

            if (!isset($counters[$key])) {
                $counters[$key] = 1;
            } else {
                $counters[$key]++;
            }

            $sequence = str_pad($counters[$key], 2, '0', STR_PAD_LEFT);
            $nomor_mitra = $id_kecamatan . $id_desa . $sequence;

            DB::table('tabel_mitra')->where('id_mitra', $mitra->id_mitra)->update(['nomor_mitra' => $nomor_mitra]);
        }
        
        // After updating all, we can make it NOT NULL
        DB::statement('ALTER TABLE tabel_mitra MODIFY nomor_mitra CHAR(8) NOT NULL');
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('tabel_mitra', function (Blueprint $table) {
            $table->dropColumn('nomor_mitra');
        });
    }
};
