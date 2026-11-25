<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('sesi_presensi', function (Blueprint $table) {
            $table->date('tanggal')->nullable()->after('kodeQR');
            $table->text('catatan')->nullable()->after('radius');
        });

        // Isi otomatis kolom tanggal untuk data lama berdasarkan waktuMulai
        DB::statement('UPDATE sesi_presensi SET tanggal = DATE(waktuMulai) WHERE tanggal IS NULL');
    }

    public function down(): void
    {
        Schema::table('sesi_presensi', function (Blueprint $table) {
            $table->dropColumn(['tanggal', 'catatan']);
        });
    }
};