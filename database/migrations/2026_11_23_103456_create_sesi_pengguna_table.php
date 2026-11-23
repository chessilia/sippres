<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
    Schema::create('sesi_pengguna', function (Blueprint $table) {
    $table->string('idSesi', 64)->primary();
    $table->unsignedInteger('idUser');
    $table->dateTime('waktuLogin');
    $table->dateTime('waktuLogout')->nullable();

    $table->foreign('idUser')->references('idUser')->on('user');
});
    }

    public function down(): void
    {
        Schema::dropIfExists('sesi_pengguna');
    }
};