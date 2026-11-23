<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('user', function (Blueprint $table) {
            $table->increments('idUser');                  // PK, auto increment
            $table->string('username', 30)->unique();
            $table->string('nama', 100);
            $table->string('email', 100)->unique();
            $table->string('password', 255);               // disimpan ter-hash
            $table->enum('role', ['Mahasiswa', 'Dosen', 'Admin']);
            $table->string('fotoProfil', 255)->nullable();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('user');
    }
};