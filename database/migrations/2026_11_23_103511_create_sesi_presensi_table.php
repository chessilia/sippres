<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
    Schema::create('sesi_presensi', function (Blueprint $table) {
    $table->increments('idSesi');
    $table->unsignedInteger('idPerkuliahan');
    $table->string('kodeQR', 255)->unique();
    $table->dateTime('waktuMulai');
    $table->unsignedSmallInteger('durasi');                 // dalam menit
    $table->enum('status', ['Aktif', 'Selesai'])->default('Aktif');
    $table->decimal('latitude', 10, 8);
    $table->decimal('longitude', 11, 8);
    $table->unsignedSmallInteger('radius');                 // dalam meter

    $table->foreign('idPerkuliahan')->references('idPerkuliahan')->on('perkuliahan');
});
    }

    public function down(): void
    {
        Schema::dropIfExists('sesi_presensi');
    }
};