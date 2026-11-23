<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('perkuliahan', function (Blueprint $table) {
            $table->increments('idPerkuliahan');
            $table->string('kodeMatkul', 10);
            $table->string('namaMatkul', 100);
            $table->unsignedInteger('idDosen');
            $table->string('hari', 10);
            $table->time('jamMulai');
            $table->time('jamSelesai');
            $table->string('ruang', 20);
            $table->string('kelas', 5);

            $table->foreign('idDosen')->references('idUser')->on('user');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('perkuliahan');
    }
};