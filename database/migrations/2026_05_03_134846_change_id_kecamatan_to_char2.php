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
        // Drop foreign key constraints on tabel_desa
        DB::statement('ALTER TABLE tabel_desa DROP FOREIGN KEY tabel_desa_id_kecamatan_foreign');

        // Modify type to CHAR(2) for the master table
        DB::statement('ALTER TABLE tabel_kecamatan MODIFY id_kecamatan CHAR(2) NOT NULL');
        
        // Pad existing data in master table
        DB::statement("UPDATE tabel_kecamatan SET id_kecamatan = LPAD(id_kecamatan, 2, '0')");

        // Modify type for tabel_desa
        DB::statement('ALTER TABLE tabel_desa MODIFY id_kecamatan CHAR(2) NOT NULL');
        DB::statement("UPDATE tabel_desa SET id_kecamatan = LPAD(id_kecamatan, 2, '0')");

        // Modify type for tabel_petani (might contain 0 values if not set, depending on previous constraint)
        DB::statement('ALTER TABLE tabel_petani MODIFY id_kecamatan CHAR(2) NOT NULL');
        DB::statement("UPDATE tabel_petani SET id_kecamatan = LPAD(id_kecamatan, 2, '0')");

        // Modify type for tabel_mitra
        DB::statement('ALTER TABLE tabel_mitra MODIFY id_kecamatan CHAR(2) NOT NULL');
        DB::statement("UPDATE tabel_mitra SET id_kecamatan = LPAD(id_kecamatan, 2, '0')");

        // Re-add foreign key on tabel_desa
        DB::statement('ALTER TABLE tabel_desa ADD CONSTRAINT tabel_desa_id_kecamatan_foreign FOREIGN KEY (id_kecamatan) REFERENCES tabel_kecamatan(id_kecamatan) ON DELETE CASCADE');
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Removing padding is hard, but we can change back to unsignedBigInteger
        DB::statement('ALTER TABLE tabel_desa DROP FOREIGN KEY tabel_desa_id_kecamatan_foreign');
        
        // Change back to BIGINT
        DB::statement('ALTER TABLE tabel_kecamatan MODIFY id_kecamatan BIGINT UNSIGNED AUTO_INCREMENT NOT NULL');
        DB::statement('ALTER TABLE tabel_desa MODIFY id_kecamatan BIGINT UNSIGNED NOT NULL');
        DB::statement('ALTER TABLE tabel_petani MODIFY id_kecamatan BIGINT UNSIGNED NOT NULL');
        DB::statement('ALTER TABLE tabel_mitra MODIFY id_kecamatan BIGINT UNSIGNED NOT NULL');
        
        DB::statement('ALTER TABLE tabel_desa ADD CONSTRAINT tabel_desa_id_kecamatan_foreign FOREIGN KEY (id_kecamatan) REFERENCES tabel_kecamatan(id_kecamatan) ON DELETE CASCADE');
    }
};
