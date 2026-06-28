<?php

namespace Database\Seeders;

use App\Models\ArsipDokumen;
use App\Models\DisposisiSurat;
use App\Models\DokumenSktm;
use App\Models\Masyarakat;
use App\Models\PenerbitanSktm;
use App\Models\PermohonanSktm;
use App\Models\SuratKeluar;
use App\Models\SuratMasuk;
use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // User::factory(10)->create();

        $users = [
            ['name' => 'Admin', 'username' => 'admin', 'email' => 'admin@example.com', 'role' => User::ROLE_ADMIN],
            ['name' => 'Petugas', 'username' => 'petugas', 'email' => 'petugas@example.com', 'role' => User::ROLE_PETUGAS],
            ['name' => 'Masyarakat', 'username' => 'masyarakat', 'email' => 'masyarakat@example.com', 'role' => User::ROLE_MASYARAKAT],
        ];

        foreach ($users as $user) {
            User::updateOrCreate(
                ['email' => $user['email']],
                [
                    'name' => $user['name'],
                    'username' => $user['username'],
                    'role' => $user['role'],
                    'status' => 'aktif',
                    'email_verified_at' => now(),
                    'password' => Hash::make('password'),
                ],
            );
        }

        $masyarakatUser = User::where('email', 'masyarakat@example.com')->first();
        $admin = User::where('email', 'admin@example.com')->first();

        $masyarakat = Masyarakat::updateOrCreate(
            ['nik' => '6403010101010001'],
            [
                'user_id' => $masyarakatUser?->id,
                'nama_lengkap' => 'Budi Santoso',
                'tempat_lahir' => 'Marangkayu',
                'tanggal_lahir' => '1998-01-01',
                'jenis_kelamin' => 'Laki-laki',
                'alamat' => 'Jl. Poros Marangkayu RT 01 RW 02',
                'rt' => '01',
                'rw' => '02',
                'desa' => 'Marangkayu',
                'kecamatan' => 'Marangkayu',
                'no_hp' => '081234567890',
                'email' => 'masyarakat@example.com',
                'pekerjaan' => 'Wiraswasta',
            ],
        );

        $suratMasuk = SuratMasuk::updateOrCreate(
            ['nomor_agenda' => 'SM-001'],
            [
                'nomor_surat' => '470/001/DS-MRK/V/2024',
                'asal_surat' => 'Desa Marangkayu',
                'perihal' => 'Permohonan Data Warga',
                'tanggal_surat' => '2024-05-10',
                'tanggal_diterima' => '2024-05-11',
                'isi_ringkas' => 'Permohonan pembaruan data administrasi warga.',
                'status' => 'didisposisikan',
                'created_by' => $admin?->id,
            ],
        );

        SuratKeluar::updateOrCreate(
            ['nomor_agenda' => 'SK-001'],
            [
                'nomor_surat' => '470/021/Kec-MRK/V/2024',
                'tujuan_surat' => 'Desa Marangkayu',
                'perihal' => 'Balasan Permohonan Data Warga',
                'tanggal_surat' => '2024-05-12',
                'isi_ringkas' => 'Balasan dan tindak lanjut data warga.',
                'status' => 'dikirim',
                'created_by' => $admin?->id,
            ],
        );

        DisposisiSurat::updateOrCreate(
            ['nomor_disposisi' => 'DSP-001'],
            [
                'surat_masuk_id' => $suratMasuk->id,
                'tanggal_disposisi' => '2024-05-11',
                'tujuan_disposisi' => 'Seksi Pelayanan Umum',
                'isi_instruksi' => 'Tindak lanjuti dan siapkan data pendukung.',
                'batas_waktu' => '2024-05-15',
                'status' => 'diproses',
                'created_by' => $admin?->id,
            ],
        );

        $permohonan = PermohonanSktm::updateOrCreate(
            ['nomor_pengajuan' => 'SKTM-202405200001'],
            [
                'masyarakat_id' => $masyarakat->id,
                'nik' => $masyarakat->nik,
                'nama_pemohon' => $masyarakat->nama_lengkap,
                'alamat' => $masyarakat->alamat,
                'keperluan' => 'Persyaratan bantuan pendidikan.',
                'tanggal_pengajuan' => '2024-05-20',
                'status' => PermohonanSktm::STATUS_DITERBITKAN,
                'catatan' => 'Data lengkap dan valid.',
            ],
        );

        DokumenSktm::updateOrCreate(
            ['permohonan_sktm_id' => $permohonan->id, 'jenis_dokumen' => 'KTP'],
            ['nama_file' => 'ktp-budi.pdf', 'path_file' => 'dokumen/ktp-budi.pdf'],
        );

        PenerbitanSktm::updateOrCreate(
            ['nomor_surat' => '470/041/SKTM/Kec-MRK/V/2024'],
            [
                'permohonan_sktm_id' => $permohonan->id,
                'tanggal_terbit' => '2024-05-21',
                'pejabat_penandatangan' => 'Camat Marangkayu',
                'status' => 'diterbitkan',
                'diterbitkan_oleh' => $admin?->id,
            ],
        );

        ArsipDokumen::updateOrCreate(
            ['jenis_arsip' => 'SKTM', 'nomor_dokumen' => '470/041/SKTM/Kec-MRK/V/2024'],
            [
                'referensi_id' => $permohonan->id,
                'judul_dokumen' => 'Arsip SKTM Budi Santoso',
                'tanggal_dokumen' => '2024-05-21',
                'file_dokumen' => 'arsip/sktm-budi.pdf',
                'keterangan' => 'Arsip SKTM diterbitkan.',
                'created_by' => $admin?->id,
            ],
        );
    }
}
