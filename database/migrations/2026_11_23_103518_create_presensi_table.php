<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
    Schema::create('presensi', function (Blueprint $table) {
    $table->increments('idPresensi');
    $table->unsignedInteger('idMahasiswa');
    $table->unsignedInteger('idSesi');
    $table->dateTime('waktu');
    $table->string('lokasi', 50)->nullable();
    $table->enum('status', ['Hadir', 'Izin', 'Sakit', 'Alpa']);
    $table->string('keterangan', 255)->nullable();

    $table->unique(['idMahasiswa', 'idSesi']);
    $table->foreign('idMahasiswa')->references('idUser')->on('user');
    $table->foreign('idSesi')->references('idSesi')->on('sesi_presensi');
});
    }

    public function down(): void
    {
        Schema::dropIfExists('presensi');
    }
};