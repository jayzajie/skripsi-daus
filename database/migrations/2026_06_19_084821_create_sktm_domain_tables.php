<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('masyarakat', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->nullable()->constrained()->nullOnDelete();
            $table->string('nik', 32)->unique();
            $table->string('nama_lengkap');
            $table->string('tempat_lahir')->nullable();
            $table->date('tanggal_lahir')->nullable();
            $table->string('jenis_kelamin', 20)->nullable();
            $table->text('alamat')->nullable();
            $table->string('rt', 8)->nullable();
            $table->string('rw', 8)->nullable();
            $table->string('desa')->nullable();
            $table->string('kecamatan')->default('Marangkayu');
            $table->string('no_hp', 32)->nullable();
            $table->string('email')->nullable();
            $table->string('pekerjaan')->nullable();
            $table->string('status_data')->default('aktif');
            $table->timestamps();
        });

        Schema::create('surat_masuk', function (Blueprint $table) {
            $table->id();
            $table->string('nomor_agenda')->unique();
            $table->string('nomor_surat');
            $table->string('asal_surat');
            $table->string('perihal');
            $table->date('tanggal_surat');
            $table->date('tanggal_diterima');
            $table->text('isi_ringkas')->nullable();
            $table->string('file_surat')->nullable();
            $table->string('status')->default('baru');
            $table->text('keterangan')->nullable();
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
        });

        Schema::create('surat_keluar', function (Blueprint $table) {
            $table->id();
            $table->string('nomor_agenda')->unique();
            $table->string('nomor_surat');
            $table->string('tujuan_surat');
            $table->string('perihal');
            $table->date('tanggal_surat');
            $table->text('isi_ringkas')->nullable();
            $table->string('file_surat')->nullable();
            $table->string('status')->default('draft');
            $table->text('keterangan')->nullable();
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
        });

        Schema::create('disposisi_surat', function (Blueprint $table) {
            $table->id();
            $table->foreignId('surat_masuk_id')->constrained('surat_masuk')->cascadeOnDelete();
            $table->string('nomor_disposisi')->unique();
            $table->date('tanggal_disposisi');
            $table->string('tujuan_disposisi');
            $table->text('isi_instruksi');
            $table->date('batas_waktu')->nullable();
            $table->string('status')->default('menunggu');
            $table->text('catatan')->nullable();
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
        });

        Schema::create('permohonan_sktm', function (Blueprint $table) {
            $table->id();
            $table->string('nomor_pengajuan')->unique();
            $table->foreignId('masyarakat_id')->constrained('masyarakat')->cascadeOnDelete();
            $table->string('nik', 32);
            $table->string('nama_pemohon');
            $table->text('alamat');
            $table->text('keperluan');
            $table->date('tanggal_pengajuan');
            $table->string('status')->default('menunggu');
            $table->text('catatan')->nullable();
            $table->timestamps();
        });

        Schema::create('dokumen_sktm', function (Blueprint $table) {
            $table->id();
            $table->foreignId('permohonan_sktm_id')->constrained('permohonan_sktm')->cascadeOnDelete();
            $table->string('jenis_dokumen');
            $table->string('nama_file');
            $table->string('path_file');
            $table->timestamps();
        });

        Schema::create('penerbitan_sktm', function (Blueprint $table) {
            $table->id();
            $table->foreignId('permohonan_sktm_id')->constrained('permohonan_sktm')->cascadeOnDelete();
            $table->string('nomor_surat')->unique();
            $table->date('tanggal_terbit');
            $table->string('pejabat_penandatangan');
            $table->string('file_pdf')->nullable();
            $table->string('status')->default('siap_diterbitkan');
            $table->foreignId('diterbitkan_oleh')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
        });

        Schema::create('arsip_dokumen', function (Blueprint $table) {
            $table->id();
            $table->string('jenis_arsip');
            $table->unsignedBigInteger('referensi_id')->nullable();
            $table->string('judul_dokumen');
            $table->string('nomor_dokumen')->nullable();
            $table->date('tanggal_dokumen')->nullable();
            $table->string('file_dokumen')->nullable();
            $table->text('keterangan')->nullable();
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('arsip_dokumen');
        Schema::dropIfExists('penerbitan_sktm');
        Schema::dropIfExists('dokumen_sktm');
        Schema::dropIfExists('permohonan_sktm');
        Schema::dropIfExists('disposisi_surat');
        Schema::dropIfExists('surat_keluar');
        Schema::dropIfExists('surat_masuk');
        Schema::dropIfExists('masyarakat');
    }
};
