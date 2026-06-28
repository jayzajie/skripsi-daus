<?php

namespace Tests\Feature;

use App\Models\Masyarakat;
use App\Models\PermohonanSktm;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
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
                'masyarakat_id' => $masyarakat->id,
                'keperluan' => 'Bantuan pendidikan',
            ])
            ->assertRedirect();

        $this->assertDatabaseHas('permohonan_sktm', [
            'masyarakat_id' => $masyarakat->id,
            'status' => PermohonanSktm::STATUS_MENUNGGU,
        ]);
    }

    public function test_petugas_can_verify_permohonan(): void
    {
        $petugas = User::factory()->petugas()->create();
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
            'status' => PermohonanSktm::STATUS_MENUNGGU,
        ]);

        $this->actingAs($petugas)
            ->patch("/dashboard/permohonan-sktm/{$permohonan->id}/verifikasi", [
                'status' => PermohonanSktm::STATUS_DISETUJUI,
            ])
            ->assertRedirect();

        $this->assertDatabaseHas('permohonan_sktm', [
            'id' => $permohonan->id,
            'status' => PermohonanSktm::STATUS_DISETUJUI,
        ]);
    }
}
