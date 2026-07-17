<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class DashboardRoleTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_dashboard_shows_admin_menus(): void
    {
        $user = User::factory()->admin()->create();

        $this->actingAs($user)
            ->get('/dashboard')
            ->assertOk()
            ->assertSee('Manajemen User')
            ->assertSee('Inventarisasi Surat')
            ->assertSee('Pelayanan Surat Keterangan Tidak Mampu')
            ->assertSee('Laporan');
    }

    public function test_kepala_kecamatan_dashboard_matches_assigned_access(): void
    {
        $user = User::factory()->kepalaKecamatan()->create();

        $this->actingAs($user)
            ->get('/dashboard')
            ->assertOk()
            ->assertDontSee('Manajemen User')
            ->assertDontSee('/dashboard/surat-masuk', false)
            ->assertSee('Pengajuan Surat Keterangan Tidak Mampu')
            ->assertSee('Laporan');
    }

    public function test_masyarakat_dashboard_shows_sktm_self_service_menus(): void
    {
        $user = User::factory()->create();

        $this->actingAs($user)
            ->get('/dashboard')
            ->assertOk()
            ->assertSee('Ajukan Surat Keterangan Tidak Mampu')
            ->assertSee('Dokumen Saya')
            ->assertSee('Status Pengajuan')
            ->assertDontSee('Manajemen User');
    }

    public function test_masyarakat_cannot_open_admin_sections_directly(): void
    {
        $user = User::factory()->create();

        $this->actingAs($user)
            ->get('/dashboard/data-pengguna')
            ->assertForbidden();
    }

    public function test_kepala_kecamatan_cannot_open_inventory_sections_directly(): void
    {
        $user = User::factory()->kepalaKecamatan()->create();

        $this->actingAs($user)
            ->get('/dashboard/surat-masuk')
            ->assertForbidden();
    }
}
