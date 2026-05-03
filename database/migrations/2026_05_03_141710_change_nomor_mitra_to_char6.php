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
        // 1. Generate new 6-digit nomor_mitra
        $mitras = DB::table('tabel_mitra')->orderBy('id_desa')->orderBy('id_mitra')->get();
        $counters = [];

        foreach ($mitras as $mitra) {
            $id_desa = $mitra->id_desa ?? '0000'; 

            if (!isset($counters[$id_desa])) {
                $counters[$id_desa] = 1;
            } else {
                $counters[$id_desa]++;
            }

            $sequence = str_pad($counters[$id_desa], 2, '0', STR_PAD_LEFT);
            $new_nomor_mitra = $id_desa . $sequence; // 4 digits + 2 digits = 6 digits

            DB::table('tabel_mitra')->where('id_mitra', $mitra->id_mitra)->update(['nomor_mitra' => $new_nomor_mitra]);
        }
        
        // 2. Change column type to CHAR(6)
        DB::statement('ALTER TABLE tabel_mitra MODIFY nomor_mitra CHAR(6) NOT NULL');
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Change back to CHAR(8)
        DB::statement('ALTER TABLE tabel_mitra MODIFY nomor_mitra CHAR(8) NOT NULL');
    }
};
