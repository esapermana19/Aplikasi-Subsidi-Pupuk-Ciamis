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
        // 1. Fetch old data to create a mapping map[old_id] = new_id
        $desas = DB::table('tabel_desa')->orderBy('id_kecamatan')->orderBy('id_desa')->get();
        $mapping = [];
        $counters = [];

        foreach ($desas as $desa) {
            $id_kecamatan = $desa->id_kecamatan; // already zero padded CHAR(2)
            if (!isset($counters[$id_kecamatan])) {
                $counters[$id_kecamatan] = 1;
            } else {
                $counters[$id_kecamatan]++;
            }

            $sequence = str_pad($counters[$id_kecamatan], 2, '0', STR_PAD_LEFT);
            $new_id = $id_kecamatan . $sequence;
            
            $mapping[$desa->id_desa] = $new_id;
        }

        // 2. Change column types to CHAR(4)
        DB::statement('ALTER TABLE tabel_desa MODIFY id_desa CHAR(4) NOT NULL');
        DB::statement('ALTER TABLE tabel_petani MODIFY id_desa CHAR(4) NULL');
        DB::statement('ALTER TABLE tabel_mitra MODIFY id_desa CHAR(4) NULL');

        // 3. Update the values based on mapping
        foreach ($mapping as $old_id => $new_id) {
            // Note: the old_id might have been casted to string by the ALTER TABLE.
            // e.g., INT 1 became CHAR '1'
            $old_id_str = (string)$old_id;

            DB::table('tabel_desa')->where('id_desa', $old_id_str)->update(['id_desa' => $new_id]);
            DB::table('tabel_petani')->where('id_desa', $old_id_str)->update(['id_desa' => $new_id]);
            DB::table('tabel_mitra')->where('id_desa', $old_id_str)->update(['id_desa' => $new_id]);
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Change back to BIGINT
        DB::statement('ALTER TABLE tabel_desa MODIFY id_desa BIGINT UNSIGNED AUTO_INCREMENT NOT NULL');
        DB::statement('ALTER TABLE tabel_petani MODIFY id_desa BIGINT UNSIGNED NOT NULL');
        DB::statement('ALTER TABLE tabel_mitra MODIFY id_desa BIGINT UNSIGNED NOT NULL');
    }
};
