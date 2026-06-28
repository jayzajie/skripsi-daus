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
            ->assertSee('Inventori Surat')
            ->assertSee('Pelayanan SKTM')
            ->assertSee('Laporan');
    }

    public function test_petugas_dashboard_hides_admin_only_menu(): void
    {
        $user = User::factory()->petugas()->create();

        $this->actingAs($user)
            ->get('/dashboard')
            ->assertOk()
            ->assertDontSee('Manajemen User')
            ->assertSee('Surat Masuk')
            ->assertSee('Verifikasi');
    }

    public function test_masyarakat_dashboard_shows_sktm_self_service_menus(): void
    {
        $user = User::factory()->create();

        $this->actingAs($user)
            ->get('/dashboard')
            ->assertOk()
            ->assertSee('Ajukan SKTM')
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
}
