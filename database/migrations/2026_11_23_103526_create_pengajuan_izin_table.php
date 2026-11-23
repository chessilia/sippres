<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
    Schema::create('pengajuan_izin', function (Blueprint $table) {
    $table->increments('idIzin');
    $table->unsignedInteger('idMahasiswa');
    $table->unsignedInteger('idPerkuliahan');
    $table->unsignedTinyInteger('pertemuan');               // 1 sampai 16
    $table->string('alasan', 255);
    $table->string('fileBukti', 255)->nullable();
    $table->enum('status', ['Menunggu', 'Disetujui', 'Ditolak'])->default('Menunggu');
    $table->dateTime('tanggalPengajuan')->useCurrent();

    $table->foreign('idMahasiswa')->references('idUser')->on('user');
    $table->foreign('idPerkuliahan')->references('idPerkuliahan')->on('perkuliahan');
});
    }

    public function down(): void
    {
        Schema::dropIfExists('pengajuan_izin');
    }
};