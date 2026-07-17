<?php

namespace Tests\Feature;

use App\Models\Masyarakat;
use App\Models\PenerbitanSktm;
use App\Models\PermohonanSktm;
use App\Models\SuratKeluar;
use App\Models\SuratMasuk;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class FeatureImplementationTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_can_create_surat_masuk(): void
    {
        $admin = User::factory()->admin()->create();

        $this->actingAs($admin)
            ->post('/dashboard/surat-masuk', [
                'nomor_agenda' => 'SM-TST-001',
                'nomor_surat' => '470/001/TST',
                'asal_surat' => 'Desa Test',
                'perihal' => 'Permohonan Data',
                'tanggal_surat' => '2026-06-01',
                'tanggal_diterima' => '2026-06-02',
                'status' => 'baru',
            ])
            ->assertRedirect();

        $this->assertDatabaseHas('surat_masuk', [
            'nomor_agenda' => 'SM-TST-001',
            'status' => 'baru',
        ]);
    }

    public function test_masyarakat_cannot_access_surat_masuk(): void
    {
        $masyarakat = User::factory()->create();

        $this->actingAs($masyarakat)
            ->get('/dashboard/surat-masuk')
            ->assertForbidden();
    }

    public function test_masyarakat_can_create_own_profile_and_permohonan(): void
    {
        $user = User::factory()->create();

        $this->actingAs($user)
            ->post('/dashboard/profil-saya', [
                'nik' => '6403010101010099',
                'nama_lengkap' => 'Pemohon Test',
                'alamat' => 'Alamat Test',
                'desa' => 'Marangkayu',
            ])
            ->assertRedirect();

        $masyarakat = Masyarakat::where('user_id', $user->id)->firstOrFail();

        $this->actingAs($user)
            ->post('/dashboard/ajukan-sktm', [
                'keperluan' => 'Bantuan pendidikan',
            ])
            ->assertRedirect();

        $this->assertDatabaseHas('permohonan_sktm', [
            'masyarakat_id' => $masyarakat->id,
            'status' => PermohonanSktm::STATUS_MENUNGGU,
        ]);
    }

    public function test_masyarakat_ajukan_sktm_form_uses_own_profile(): void
    {
        $user = User::factory()->create();
        Masyarakat::create([
            'user_id' => $user->id,
            'nik' => '6403010101010022',
            'nama_lengkap' => 'Pemohon Mandiri',
            'alamat' => 'Alamat Mandiri',
        ]);

        $this->actingAs($user)
            ->get('/dashboard/ajukan-sktm')
            ->assertOk()
            ->assertDontSee('Pilih masyarakat');
    }

    public function test_kepala_kecamatan_can_decide_verified_application_but_cannot_edit_it(): void
    {
        $kepalaKecamatan = User::factory()->kepalaKecamatan()->create();
        $user = User::factory()->create();
        $masyarakat = Masyarakat::create([
            'user_id' => $user->id,
            'nik' => '6403010101010088',
            'nama_lengkap' => 'Pemohon Verifikasi',
            'alamat' => 'Alamat Verifikasi',
        ]);
        $permohonan = PermohonanSktm::create([
            'nomor_pengajuan' => 'SKTM-TST-001',
            'masyarakat_id' => $masyarakat->id,
            'nik' => $masyarakat->nik,
            'nama_pemohon' => $masyarakat->nama_lengkap,
            'alamat' => $masyarakat->alamat,
            'keperluan' => 'Bantuan kesehatan',
            'tanggal_pengajuan' => '2026-06-01',
            'status' => PermohonanSktm::STATUS_DIVERIFIKASI,
        ]);

        $this->actingAs($kepalaKecamatan)
            ->patch("/dashboard/permohonan-sktm/{$permohonan->id}/verifikasi", [
                'status' => PermohonanSktm::STATUS_DISETUJUI,
            ])
            ->assertRedirect();

        $this->assertDatabaseHas('permohonan_sktm', [
            'id' => $permohonan->id,
            'status' => PermohonanSktm::STATUS_DISETUJUI,
        ]);

        PermohonanSktm::whereKey($permohonan->id)->update(['status' => PermohonanSktm::STATUS_DIVERIFIKASI]);

        $this->actingAs($kepalaKecamatan)
            ->patch("/dashboard/permohonan-sktm/{$permohonan->id}/verifikasi", [
                'status' => PermohonanSktm::STATUS_DITOLAK,
                'catatan' => 'Berkas tidak memenuhi syarat.',
            ])
            ->assertRedirect();

        $this->assertDatabaseHas('permohonan_sktm', [
            'id' => $permohonan->id,
            'status' => PermohonanSktm::STATUS_DITOLAK,
        ]);

        $this->actingAs($kepalaKecamatan)
            ->patch("/dashboard/permohonan-sktm/{$permohonan->id}", [
                'keperluan' => 'Perubahan tanpa izin',
                'status' => PermohonanSktm::STATUS_DIVERIFIKASI,
            ])
            ->assertForbidden();

        $this->actingAs($kepalaKecamatan)
            ->patch("/dashboard/permohonan-sktm/{$permohonan->id}/verifikasi", [
                'status' => PermohonanSktm::STATUS_DISETUJUI,
            ])
            ->assertConflict();
    }

    public function test_admin_can_create_surat_keluar_draft_from_surat_masuk(): void
    {
        $admin = User::factory()->admin()->create();
        $suratMasuk = SuratMasuk::create([
            'nomor_agenda' => 'SM-REPLY-001',
            'nomor_surat' => '470/001/IN',
            'asal_surat' => 'Desa Marangkayu',
            'perihal' => 'Permohonan Data',
            'tanggal_surat' => '2026-06-01',
            'tanggal_diterima' => '2026-06-02',
            'isi_ringkas' => 'Mohon data warga.',
            'status' => 'baru',
            'created_by' => $admin->id,
        ]);

        $this->actingAs($admin)
            ->post("/dashboard/surat-masuk/{$suratMasuk->id}/balasan")
            ->assertRedirect('/dashboard/surat-keluar');

        $this->assertDatabaseHas('surat_keluar', [
            'surat_masuk_id' => $suratMasuk->id,
            'tujuan_surat' => 'Desa Marangkayu',
            'perihal' => 'Balasan: Permohonan Data',
            'status' => 'draft',
        ]);
        $this->assertDatabaseHas('surat_masuk', [
            'id' => $suratMasuk->id,
            'status' => 'diproses',
        ]);
    }

    public function test_masyarakat_cannot_create_surat_keluar_reply(): void
    {
        $masyarakat = User::factory()->create();
        $admin = User::factory()->admin()->create();
        $suratMasuk = SuratMasuk::create([
            'nomor_agenda' => 'SM-REPLY-002',
            'nomor_surat' => '470/002/IN',
            'asal_surat' => 'Desa Santan',
            'perihal' => 'Permohonan Bantuan',
            'tanggal_surat' => '2026-06-01',
            'tanggal_diterima' => '2026-06-02',
            'status' => 'baru',
            'created_by' => $admin->id,
        ]);

        $this->actingAs($masyarakat)
            ->post("/dashboard/surat-masuk/{$suratMasuk->id}/balasan")
            ->assertForbidden();

        $this->assertSame(0, SuratKeluar::count());
    }

    public function test_admin_can_update_surat_keluar_draft(): void
    {
        $admin = User::factory()->admin()->create();
        $suratKeluar = SuratKeluar::create([
            'nomor_agenda' => 'SK-EDIT-001',
            'nomor_surat' => 'DRAFT-001',
            'tujuan_surat' => 'Desa Lama',
            'perihal' => 'Draft Lama',
            'tanggal_surat' => '2026-06-01',
            'status' => 'draft',
            'created_by' => $admin->id,
        ]);

        $this->actingAs($admin)
            ->patch("/dashboard/surat-keluar/{$suratKeluar->id}", [
                'nomor_agenda' => 'SK-EDIT-001',
                'nomor_surat' => '470/099/OUT',
                'tujuan_surat' => 'Desa Baru',
                'perihal' => 'Balasan Final',
                'tanggal_surat' => '2026-06-03',
                'status' => 'dikirim',
            ])
            ->assertRedirect();

        $this->assertDatabaseHas('surat_keluar', [
            'id' => $suratKeluar->id,
            'nomor_surat' => '470/099/OUT',
            'status' => 'dikirim',
        ]);
    }

    public function test_admin_can_update_surat_masuk(): void
    {
        $admin = User::factory()->admin()->create();
        $surat = SuratMasuk::create([
            'nomor_agenda' => 'SM-EDIT-001',
            'nomor_surat' => '470/001/OLD',
            'asal_surat' => 'Desa Lama',
            'perihal' => 'Lama',
            'tanggal_surat' => '2026-06-01',
            'tanggal_diterima' => '2026-06-02',
            'status' => 'baru',
            'created_by' => $admin->id,
        ]);

        $this->actingAs($admin)
            ->patch("/dashboard/surat-masuk/{$surat->id}", [
                'nomor_agenda' => 'SM-EDIT-001',
                'nomor_surat' => '470/001/NEW',
                'asal_surat' => 'Desa Baru',
                'perihal' => 'Baru',
                'tanggal_surat' => '2026-06-01',
                'tanggal_diterima' => '2026-06-02',
                'status' => 'selesai',
            ])
            ->assertRedirect();

        $this->assertDatabaseHas('surat_masuk', ['id' => $surat->id, 'status' => 'selesai']);
    }

    public function test_admin_can_update_masyarakat_and_permohonan(): void
    {
        $admin = User::factory()->admin()->create();
        $masyarakat = Masyarakat::create([
            'nik' => '6403010101010011',
            'nama_lengkap' => 'Nama Lama',
            'alamat' => 'Alamat Lama',
        ]);
        $permohonan = PermohonanSktm::create([
            'nomor_pengajuan' => 'SKTM-EDIT-001',
            'masyarakat_id' => $masyarakat->id,
            'nik' => $masyarakat->nik,
            'nama_pemohon' => $masyarakat->nama_lengkap,
            'alamat' => $masyarakat->alamat,
            'keperluan' => 'Lama',
            'tanggal_pengajuan' => '2026-06-01',
            'status' => PermohonanSktm::STATUS_MENUNGGU,
        ]);

        $this->actingAs($admin)
            ->patch("/dashboard/masyarakat/{$masyarakat->id}", [
                'nik' => '6403010101010011',
                'nama_lengkap' => 'Nama Baru',
                'alamat' => 'Alamat Baru',
            ])
            ->assertRedirect();

        $this->actingAs($admin)
            ->patch("/dashboard/permohonan-sktm/{$permohonan->id}", [
                'keperluan' => 'Baru',
                'status' => PermohonanSktm::STATUS_DIVERIFIKASI,
            ])
            ->assertRedirect();

        $this->assertDatabaseHas('masyarakat', ['id' => $masyarakat->id, 'nama_lengkap' => 'Nama Baru']);
        $this->assertDatabaseHas('permohonan_sktm', ['id' => $permohonan->id, 'status' => PermohonanSktm::STATUS_DIVERIFIKASI]);
    }

    public function test_laporan_page_shows_summary(): void
    {
        $admin = User::factory()->admin()->create();
        SuratMasuk::create([
            'nomor_agenda' => 'SM-LAP-001',
            'nomor_surat' => '470/001/LAP',
            'asal_surat' => 'Desa Laporan',
            'perihal' => 'Data',
            'tanggal_surat' => '2026-06-01',
            'tanggal_diterima' => '2026-06-02',
            'status' => 'baru',
            'created_by' => $admin->id,
        ]);

        $this->actingAs($admin)
            ->get('/dashboard/laporan')
            ->assertOk()
            ->assertSee('Surat Masuk')
            ->assertSee('Status Surat Masuk');
    }

    public function test_laporan_can_be_exported(): void
    {
        $admin = User::factory()->admin()->create();

        $this->actingAs($admin)
            ->get('/dashboard/laporan/export?format=excel')
            ->assertOk()
            ->assertHeader('content-type', 'application/vnd.ms-excel; charset=UTF-8');

        $this->actingAs($admin)
            ->get('/dashboard/laporan/export?format=pdf')
            ->assertOk()
            ->assertHeader('content-type', 'application/pdf');
    }

    public function test_penerbitan_sktm_can_be_printed(): void
    {
        $admin = User::factory()->admin()->create();
        $user = User::factory()->create();
        $masyarakat = Masyarakat::create([
            'user_id' => $user->id,
            'nik' => '6403010101010044',
            'nama_lengkap' => 'Pemohon Cetak',
            'alamat' => 'Alamat Cetak',
        ]);
        $permohonan = PermohonanSktm::create([
            'nomor_pengajuan' => 'SKTM-CETAK-001',
            'masyarakat_id' => $masyarakat->id,
            'nik' => $masyarakat->nik,
            'nama_pemohon' => $masyarakat->nama_lengkap,
            'alamat' => $masyarakat->alamat,
            'keperluan' => 'Bantuan',
            'tanggal_pengajuan' => '2026-06-01',
            'status' => PermohonanSktm::STATUS_DITERBITKAN,
        ]);
        $penerbitan = PenerbitanSktm::create([
            'permohonan_sktm_id' => $permohonan->id,
            'nomor_surat' => '470/001/SKTM',
            'tanggal_terbit' => '2026-06-03',
            'masa_berlaku' => '2026-12-03',
            'pejabat_penandatangan' => 'Camat Marangkayu',
            'status' => 'diterbitkan',
            'diterbitkan_oleh' => $admin->id,
        ]);

        $this->actingAs($user)
            ->get("/dashboard/penerbitan-sktm/{$penerbitan->id}/cetak")
            ->assertOk()
            ->assertSee('SURAT KETERANGAN TIDAK MAMPU')
            ->assertSee('Pemohon Cetak');

        $this->actingAs($user)
            ->get("/dashboard/penerbitan-sktm/{$penerbitan->id}/download")
            ->assertOk()
            ->assertHeader('content-type', 'application/pdf');
    }

    public function test_masyarakat_search_cannot_see_other_users_permohonan(): void
    {
        $owner = User::factory()->create();
        $viewer = User::factory()->create();
        $masyarakat = Masyarakat::create([
            'user_id' => $owner->id,
            'nik' => '6403010101010077',
            'nama_lengkap' => 'Pemohon Rahasia',
            'alamat' => 'Alamat Rahasia',
        ]);

        PermohonanSktm::create([
            'nomor_pengajuan' => 'SKTM-SECRET-001',
            'masyarakat_id' => $masyarakat->id,
            'nik' => $masyarakat->nik,
            'nama_pemohon' => 'Nama Bocor',
            'alamat' => $masyarakat->alamat,
            'keperluan' => 'Bantuan',
            'tanggal_pengajuan' => '2026-06-01',
            'status' => PermohonanSktm::STATUS_MENUNGGU,
        ]);

        $this->actingAs($viewer)
            ->get('/dashboard/status-pengajuan?q=Bocor')
            ->assertOk()
            ->assertDontSee('Nama Bocor');
    }

    public function test_masyarakat_cannot_delete_other_users_document(): void
    {
        $owner = User::factory()->create();
        $attacker = User::factory()->create();
        $masyarakat = Masyarakat::create([
            'user_id' => $owner->id,
            'nik' => '6403010101010066',
            'nama_lengkap' => 'Pemilik Dokumen',
            'alamat' => 'Alamat Dokumen',
        ]);
        $permohonan = PermohonanSktm::create([
            'nomor_pengajuan' => 'SKTM-DOC-001',
            'masyarakat_id' => $masyarakat->id,
            'nik' => $masyarakat->nik,
            'nama_pemohon' => $masyarakat->nama_lengkap,
            'alamat' => $masyarakat->alamat,
            'keperluan' => 'Bantuan',
            'tanggal_pengajuan' => '2026-06-01',
            'status' => PermohonanSktm::STATUS_MENUNGGU,
        ]);
        $dokumen = $permohonan->dokumen()->create([
            'jenis_dokumen' => 'KTP',
            'nama_file' => 'owner.pdf',
            'path_file' => 'owner.pdf',
        ]);

        $this->actingAs($attacker)
            ->delete("/dashboard/dokumen-saya/{$dokumen->id}")
            ->assertForbidden();

        $this->assertDatabaseHas('dokumen_sktm', ['id' => $dokumen->id]);
    }

    public function test_masyarakat_can_upload_document_file(): void
    {
        Storage::fake('public');

        $user = User::factory()->create();
        $masyarakat = Masyarakat::create([
            'user_id' => $user->id,
            'nik' => '6403010101010055',
            'nama_lengkap' => 'Pemohon Upload',
            'alamat' => 'Alamat Upload',
        ]);
        $permohonan = PermohonanSktm::create([
            'nomor_pengajuan' => 'SKTM-UPLOAD-001',
            'masyarakat_id' => $masyarakat->id,
            'nik' => $masyarakat->nik,
            'nama_pemohon' => $masyarakat->nama_lengkap,
            'alamat' => $masyarakat->alamat,
            'keperluan' => 'Bantuan',
            'tanggal_pengajuan' => '2026-06-01',
            'status' => PermohonanSktm::STATUS_MENUNGGU,
        ]);

        $this->actingAs($user)
            ->post('/dashboard/dokumen-saya', [
                'permohonan_sktm_id' => $permohonan->id,
                'jenis_dokumen' => 'KTP',
                'dokumen_file' => UploadedFile::fake()->create('ktp.pdf', 64, 'application/pdf'),
            ])
            ->assertRedirect();

        $dokumen = $permohonan->dokumen()->firstOrFail();

        $this->assertSame('ktp.pdf', $dokumen->nama_file);
        Storage::disk('public')->assertExists($dokumen->path_file);
    }

    public function test_uploaded_document_link_is_visible(): void
    {
        $user = User::factory()->create();
        $masyarakat = Masyarakat::create([
            'user_id' => $user->id,
            'nik' => '6403010101010033',
            'nama_lengkap' => 'Pemohon File',
            'alamat' => 'Alamat File',
        ]);
        $permohonan = PermohonanSktm::create([
            'nomor_pengajuan' => 'SKTM-FILE-001',
            'masyarakat_id' => $masyarakat->id,
            'nik' => $masyarakat->nik,
            'nama_pemohon' => $masyarakat->nama_lengkap,
            'alamat' => $masyarakat->alamat,
            'keperluan' => 'Bantuan',
            'tanggal_pengajuan' => '2026-06-01',
            'status' => PermohonanSktm::STATUS_MENUNGGU,
        ]);
        $permohonan->dokumen()->create([
            'jenis_dokumen' => 'KTP',
            'nama_file' => 'ktp.pdf',
            'path_file' => 'dokumen-sktm/ktp.pdf',
        ]);

        $this->actingAs($user)
            ->get('/dashboard/dokumen-saya')
            ->assertOk()
            ->assertSee('File');
    }
}
