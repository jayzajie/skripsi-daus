<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('surat_keluar', function (Blueprint $table) {
            $table->foreignId('surat_masuk_id')->nullable()->after('id')->constrained('surat_masuk')->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('surat_keluar', function (Blueprint $table) {
            $table->dropConstrainedForeignId('surat_masuk_id');
        });
    }
};
