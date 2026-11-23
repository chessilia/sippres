<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
    Schema::create('notifikasi', function (Blueprint $table) {
    $table->increments('idNotifikasi');
    $table->unsignedInteger('idPenerima');
    $table->string('jenis', 30);
    $table->string('isi', 255);
    $table->dateTime('waktu');
    $table->boolean('statusBaca')->default(false);

    $table->foreign('idPenerima')->references('idUser')->on('user');
});
    }

    public function down(): void
    {
        Schema::dropIfExists('notifikasi');
    }
};