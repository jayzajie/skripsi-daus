<?php

namespace App\Http\Controllers;

use App\Models\ArsipDokumen;
use App\Models\DisposisiSurat;
use App\Models\DokumenSktm;
use App\Models\Masyarakat;
use App\Models\PenerbitanSktm;
use App\Models\PermohonanSktm;
use App\Models\SuratKeluar;
use App\Models\SuratMasuk;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function index(Request $request): View
    {
        return $this->show($request, 'dashboard');
    }

    public function show(Request $request, string $section): View
    {
        abort_unless(in_array($section, $this->allowedSections($request->user()->role), true), 403);

        return view('dashboard', [
            'section' => $section,
            'statsFromDb' => $this->stats($request->user()),
            'module' => $this->moduleData($request, $section),
        ]);
    }

    public function allowedSections(string $role): array
    {
        return match ($role) {
            User::ROLE_ADMIN => [
                'dashboard',
                'data-pengguna',
                'data-masyarakat',
                'surat-masuk',
                'surat-keluar',
                'disposisi-surat',
                'arsip-surat',
                'permohonan-sktm',
                'verifikasi-sktm',
                'penerbitan-sktm',
                'laporan',
            ],
            User::ROLE_KEPALA_KECAMATAN => [
                'dashboard',
                'verifikasi-sktm',
                'laporan',
            ],
            default => [
                'dashboard',
                'profil-saya',
                'ajukan-sktm',
                'status-pengajuan',
                'dokumen-saya',
                'sktm-terbit',
            ],
        };
    }

    private function stats(User $user): array
    {
        $permohonanQuery = PermohonanSktm::query();

        if ($user->hasRole(User::ROLE_MASYARAKAT)) {
            $permohonanQuery->whereHas('masyarakat', fn ($query) => $query->where('user_id', $user->id));
        }

        return [
            'total_pengguna' => User::count(),
            'total_masyarakat' => Masyarakat::count(),
            'surat_masuk' => SuratMasuk::count(),
            'surat_keluar' => SuratKeluar::count(),
            'disposisi_surat' => DisposisiSurat::count(),
            'permohonan_sktm' => (clone $permohonanQuery)->count(),
            'sktm_menunggu' => (clone $permohonanQuery)->where('status', PermohonanSktm::STATUS_MENUNGGU)->count(),
            'sktm_diverifikasi' => (clone $permohonanQuery)->where('status', PermohonanSktm::STATUS_DIVERIFIKASI)->count(),
            'sktm_disetujui' => (clone $permohonanQuery)->where('status', PermohonanSktm::STATUS_DISETUJUI)->count(),
            'sktm_ditolak' => (clone $permohonanQuery)->where('status', PermohonanSktm::STATUS_DITOLAK)->count(),
            'sktm_diterbitkan' => (clone $permohonanQuery)->where('status', PermohonanSktm::STATUS_DITERBITKAN)->count(),
            'penerbitan_sktm' => PenerbitanSktm::count(),
            'dokumen_sktm' => $user->hasRole(User::ROLE_MASYARAKAT)
                ? DokumenSktm::whereHas('permohonanSktm.masyarakat', fn ($query) => $query->where('user_id', $user->id))->count()
                : DokumenSktm::count(),
        ];
    }

    private function moduleData(Request $request, string $section): array
    {
        $user = $request->user();
        $search = $request->string('q')->toString();
        $status = $request->string('status')->toString();
        $tahun = $request->string('tahun')->toString();

        return match ($section) {
            'data-pengguna' => [
                'type' => 'users',
                'records' => User::query()
                    ->when($search, fn ($query) => $query->where(fn ($inner) => $inner
                        ->where('name', 'like', "%{$search}%")
                        ->orWhere('email', 'like', "%{$search}%")))
                    ->when($status, fn ($query) => $query->where('status', $status))
                    ->latest()
                    ->paginate(10)
                    ->withQueryString(),
            ],
            'data-masyarakat', 'profil-saya' => [
                'type' => 'masyarakat',
                'records' => Masyarakat::query()
                    ->when($user->hasRole(User::ROLE_MASYARAKAT), fn ($query) => $query->where('user_id', $user->id))
                    ->when($search, fn ($query) => $query->where(fn ($inner) => $inner
                        ->where('nama_lengkap', 'like', "%{$search}%")
                        ->orWhere('nik', 'like', "%{$search}%")))
                    ->latest()
                    ->paginate(10)
                    ->withQueryString(),
            ],
            'surat-masuk' => [
                'type' => 'surat-masuk',
                'records' => SuratMasuk::query()
                    ->when($search, fn ($query) => $query->where(fn ($inner) => $inner
                        ->where('nomor_surat', 'like', "%{$search}%")
                        ->orWhere('asal_surat', 'like', "%{$search}%")
                        ->orWhere('perihal', 'like', "%{$search}%")))
                    ->when($status, fn ($query) => $query->where('status', $status))
                    ->latest()
                    ->paginate(10)
                    ->withQueryString(),
            ],
            'surat-keluar' => [
                'type' => 'surat-keluar',
                'records' => SuratKeluar::with('suratMasuk')
                    ->when($search, fn ($query) => $query->where(fn ($inner) => $inner
                        ->where('nomor_surat', 'like', "%{$search}%")
                        ->orWhere('tujuan_surat', 'like', "%{$search}%")
                        ->orWhere('perihal', 'like', "%{$search}%")))
                    ->when($status, fn ($query) => $query->where('status', $status))
                    ->latest()
                    ->paginate(10)
                    ->withQueryString(),
            ],
            'disposisi-surat' => [
                'type' => 'disposisi-surat',
                'records' => DisposisiSurat::with('suratMasuk')
                    ->when($status, fn ($query) => $query->where('status', $status))
                    ->latest()
                    ->paginate(10)
                    ->withQueryString(),
                'suratMasuk' => SuratMasuk::latest()->limit(50)->get(),
            ],
            'permohonan-sktm', 'ajukan-sktm', 'status-pengajuan' => [
                'type' => 'permohonan-sktm',
                'records' => PermohonanSktm::with('masyarakat')
                    ->when($user->hasRole(User::ROLE_MASYARAKAT), fn ($query) => $query->whereHas('masyarakat', fn ($inner) => $inner->where('user_id', $user->id)))
                    ->when($search, fn ($query) => $query->where(fn ($inner) => $inner
                        ->where('nomor_pengajuan', 'like', "%{$search}%")
                        ->orWhere('nama_pemohon', 'like', "%{$search}%")
                        ->orWhere('nik', 'like', "%{$search}%")))
                    ->when($status, fn ($query) => $query->where('status', $status))
                    ->latest()
                    ->paginate(10)
                    ->withQueryString(),
                'masyarakat' => Masyarakat::query()
                    ->when($user->hasRole(User::ROLE_MASYARAKAT), fn ($query) => $query->where('user_id', $user->id))
                    ->get(),
            ],
            'verifikasi-sktm' => [
                'type' => 'verifikasi-sktm',
                'records' => PermohonanSktm::with('masyarakat', 'dokumen')
                    ->where('status', $user->hasRole(User::ROLE_KEPALA_KECAMATAN)
                        ? PermohonanSktm::STATUS_DIVERIFIKASI
                        : PermohonanSktm::STATUS_MENUNGGU)
                    ->when($status, fn ($query) => $query->where('status', $status))
                    ->latest()
                    ->paginate(10)
                    ->withQueryString(),
            ],
            'penerbitan-sktm', 'sktm-terbit' => [
                'type' => 'penerbitan-sktm',
                'records' => PenerbitanSktm::with('permohonanSktm.masyarakat')
                    ->when($user->hasRole(User::ROLE_MASYARAKAT), fn ($query) => $query->whereHas('permohonanSktm.masyarakat', fn ($inner) => $inner->where('user_id', $user->id)))
                    ->when($tahun, fn ($query) => $query->whereYear('tanggal_terbit', $tahun))
                    ->latest()
                    ->paginate(10)
                    ->withQueryString(),
                'permohonan' => PermohonanSktm::where('status', PermohonanSktm::STATUS_DISETUJUI)->get(),
            ],
            'dokumen-saya' => [
                'type' => 'dokumen-saya',
                'records' => DokumenSktm::with('permohonanSktm.masyarakat')
                    ->when($user->hasRole(User::ROLE_MASYARAKAT), fn ($query) => $query->whereHas('permohonanSktm.masyarakat', fn ($inner) => $inner->where('user_id', $user->id)))
                    ->latest()
                    ->paginate(10)
                    ->withQueryString(),
                'permohonan' => PermohonanSktm::whereHas('masyarakat', fn ($query) => $query->where('user_id', $user->id))->get(),
            ],
            'arsip-surat' => [
                'type' => 'arsip-surat',
                'records' => ArsipDokumen::latest()->paginate(10)->withQueryString(),
            ],
            'laporan' => [
                'type' => 'laporan',
                'records' => collect(),
                'summary' => [
                    'Surat Masuk' => SuratMasuk::count(),
                    'Surat Keluar' => SuratKeluar::count(),
                    'Disposisi Surat' => DisposisiSurat::count(),
                    'Permohonan Surat Keterangan Tidak Mampu' => PermohonanSktm::count(),
                    'Surat Keterangan Tidak Mampu Terbit' => PenerbitanSktm::count(),
                    'Arsip Dokumen' => ArsipDokumen::count(),
                ],
                'sktmStatus' => PermohonanSktm::query()
                    ->selectRaw('status, count(*) as total')
                    ->groupBy('status')
                    ->pluck('total', 'status'),
                'suratMasukStatus' => SuratMasuk::query()
                    ->selectRaw('status, count(*) as total')
                    ->groupBy('status')
                    ->pluck('total', 'status'),
                'suratKeluarStatus' => SuratKeluar::query()
                    ->selectRaw('status, count(*) as total')
                    ->groupBy('status')
                    ->pluck('total', 'status'),
            ],
            default => ['type' => 'dashboard', 'records' => collect()],
        };
    }
}
