<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('penerbitan_sktm', function (Blueprint $table) {
            $table->date('masa_berlaku')->nullable()->after('tanggal_terbit');
        });
    }

    public function down(): void
    {
        Schema::table('penerbitan_sktm', function (Blueprint $table) {
            $table->dropColumn('masa_berlaku');
        });
    }
};
