<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
    Schema::create('laporan_kendala', function (Blueprint $table) {
    $table->increments('idLaporan');
    $table->unsignedInteger('idPelapor');
    $table->string('kategori', 50);
    $table->text('deskripsi');
    $table->enum('status', ['Baru', 'Diproses', 'Selesai', 'Tidak Valid'])->default('Baru');
    $table->text('tindakLanjut')->nullable();
    $table->dateTime('tanggalLapor');

    $table->foreign('idPelapor')->references('idUser')->on('user');
});
    }

    public function down(): void
    {
        Schema::dropIfExists('laporan_kendala');
    }
};