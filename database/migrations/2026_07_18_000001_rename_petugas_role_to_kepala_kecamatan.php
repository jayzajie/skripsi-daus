<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        DB::table('users')
            ->where('role', 'petugas')
            ->where('email', 'petugas@example.com')
            ->update([
                'name' => 'Kepala Kecamatan',
                'username' => 'kepala_kecamatan',
                'email' => 'kepala.kecamatan@example.com',
            ]);

        DB::table('users')->where('role', 'petugas')->update(['role' => 'kepala_kecamatan']);
    }

    public function down(): void
    {
        DB::table('users')
            ->where('role', 'kepala_kecamatan')
            ->where('email', 'kepala.kecamatan@example.com')
            ->update([
                'name' => 'Petugas',
                'username' => 'petugas',
                'email' => 'petugas@example.com',
            ]);

        DB::table('users')->where('role', 'kepala_kecamatan')->update(['role' => 'petugas']);
    }
};
