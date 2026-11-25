<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('laporan_kendala') && !Schema::hasColumn('laporan_kendala', 'foto')) {
            Schema::table('laporan_kendala', function (Blueprint $table) {$table->string('foto')->nullable()->after('deskripsi');
            });
        }
    }

    public function down(): void
    {
        if (Schema::hasTable('laporan_kendala') && Schema::hasColumn('laporan_kendala', 'foto')) {
            Schema::table('laporan_kendala', function (Blueprint $table) {$table->dropColumn('foto');
            });
        }
    }
};