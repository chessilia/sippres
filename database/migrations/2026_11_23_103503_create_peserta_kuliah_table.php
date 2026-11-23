<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('peserta_kuliah', function (Blueprint $table) {
            $table->increments('idPeserta');
            $table->unsignedInteger('idMahasiswa');
            $table->unsignedInteger('idPerkuliahan');

            $table->unique(['idMahasiswa', 'idPerkuliahan']);
            $table->foreign('idMahasiswa')->references('idUser')->on('user');
            $table->foreign('idPerkuliahan')->references('idPerkuliahan')->on('perkuliahan');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('peserta_kuliah');
    }
};