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
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules;

class FeatureController extends Controller
{
    public function store(Request $request, string $section): RedirectResponse
    {
        $this->authorizeSection($request, $section);

        match ($section) {
            'data-pengguna' => $this->storeUser($request),
            'data-masyarakat', 'profil-saya' => $this->storeMasyarakat($request),
            'surat-masuk' => $this->storeSuratMasuk($request),
            'surat-keluar' => $this->storeSuratKeluar($request),
            'disposisi-surat' => $this->storeDisposisi($request),
            'permohonan-sktm', 'ajukan-sktm' => $this->storePermohonan($request),
            'dokumen-saya' => $this->storeDokumen($request),
            'penerbitan-sktm' => $this->storePenerbitan($request),
            'arsip-surat' => $this->storeArsip($request),
            default => abort(404),
        };

        return back()->with('status', 'Data berhasil disimpan.');
    }

    public function destroy(Request $request, string $section, int $id): RedirectResponse
    {
        $this->authorizeSection($request, $section);

        match ($section) {
            'data-pengguna' => User::whereKey($id)->whereKeyNot($request->user()->id)->delete(),
            'data-masyarakat' => Masyarakat::destroy($id),
            'surat-masuk' => SuratMasuk::destroy($id),
            'surat-keluar' => SuratKeluar::destroy($id),
            'disposisi-surat' => DisposisiSurat::destroy($id),
            'permohonan-sktm' => PermohonanSktm::destroy($id),
            'dokumen-saya' => DokumenSktm::destroy($id),
            'penerbitan-sktm' => PenerbitanSktm::destroy($id),
            'arsip-surat' => ArsipDokumen::destroy($id),
            default => abort(404),
        };

        return back()->with('status', 'Data berhasil dihapus.');
    }

    public function verify(Request $request, PermohonanSktm $permohonan): RedirectResponse
    {
        abort_unless($request->user()->hasRole([User::ROLE_ADMIN, User::ROLE_PETUGAS]), 403);

        $data = $request->validate([
            'status' => ['required', Rule::in([PermohonanSktm::STATUS_DISETUJUI, PermohonanSktm::STATUS_DITOLAK, PermohonanSktm::STATUS_DIVERIFIKASI])],
            'catatan' => ['nullable', 'required_if:status,'.PermohonanSktm::STATUS_DITOLAK, 'string'],
        ]);

        $permohonan->update($data);

        return back()->with('status', 'Status verifikasi berhasil diperbarui.');
    }

    private function authorizeSection(Request $request, string $section): void
    {
        abort_unless(in_array($section, app(DashboardController::class)->allowedSections($request->user()->role), true), 403);
    }

    private function storeUser(Request $request): void
    {
        abort_unless($request->user()->hasRole(User::ROLE_ADMIN), 403);

        $data = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'username' => ['nullable', 'string', 'max:255', 'unique:users,username'],
            'email' => ['required', 'email', 'max:255', 'unique:users,email'],
            'role' => ['required', Rule::in(User::ROLES)],
            'status' => ['required', Rule::in(['aktif', 'nonaktif'])],
            'password' => ['required', Rules\Password::defaults()],
        ]);

        $data['password'] = Hash::make($data['password']);

        User::create($data);
    }

    private function storeMasyarakat(Request $request): void
    {
        $user = $request->user();
        $data = $request->validate([
            'nik' => ['required', 'string', 'max:32', 'unique:masyarakat,nik'],
            'nama_lengkap' => ['required', 'string', 'max:255'],
            'tempat_lahir' => ['nullable', 'string', 'max:255'],
            'tanggal_lahir' => ['nullable', 'date'],
            'jenis_kelamin' => ['nullable', 'string', 'max:20'],
            'alamat' => ['nullable', 'string'],
            'rt' => ['nullable', 'string', 'max:8'],
            'rw' => ['nullable', 'string', 'max:8'],
            'desa' => ['nullable', 'string', 'max:255'],
            'kecamatan' => ['nullable', 'string', 'max:255'],
            'no_hp' => ['nullable', 'string', 'max:32'],
            'email' => ['nullable', 'email', 'max:255'],
            'pekerjaan' => ['nullable', 'string', 'max:255'],
        ]);

        $data['kecamatan'] = $data['kecamatan'] ?? 'Marangkayu';
        $data['user_id'] = $user->hasRole(User::ROLE_MASYARAKAT) ? $user->id : null;

        Masyarakat::create($data);
    }

    private function storeSuratMasuk(Request $request): void
    {
        $data = $request->validate([
            'nomor_agenda' => ['required', 'string', 'max:255', 'unique:surat_masuk,nomor_agenda'],
            'nomor_surat' => ['required', 'string', 'max:255'],
            'asal_surat' => ['required', 'string', 'max:255'],
            'perihal' => ['required', 'string', 'max:255'],
            'tanggal_surat' => ['required', 'date'],
            'tanggal_diterima' => ['required', 'date'],
            'isi_ringkas' => ['nullable', 'string'],
            'status' => ['required', Rule::in(['baru', 'diproses', 'didisposisikan', 'diarsipkan'])],
            'keterangan' => ['nullable', 'string'],
        ]);

        $data['created_by'] = $request->user()->id;

        SuratMasuk::create($data);
    }

    private function storeSuratKeluar(Request $request): void
    {
        $data = $request->validate([
            'nomor_agenda' => ['required', 'string', 'max:255', 'unique:surat_keluar,nomor_agenda'],
            'nomor_surat' => ['required', 'string', 'max:255'],
            'tujuan_surat' => ['required', 'string', 'max:255'],
            'perihal' => ['required', 'string', 'max:255'],
            'tanggal_surat' => ['required', 'date'],
            'isi_ringkas' => ['nullable', 'string'],
            'status' => ['required', Rule::in(['draft', 'diterbitkan', 'dikirim', 'diarsipkan'])],
            'keterangan' => ['nullable', 'string'],
        ]);

        $data['created_by'] = $request->user()->id;

        SuratKeluar::create($data);
    }

    private function storeDisposisi(Request $request): void
    {
        $data = $request->validate([
            'surat_masuk_id' => ['required', 'exists:surat_masuk,id'],
            'nomor_disposisi' => ['required', 'string', 'max:255', 'unique:disposisi_surat,nomor_disposisi'],
            'tanggal_disposisi' => ['required', 'date'],
            'tujuan_disposisi' => ['required', 'string', 'max:255'],
            'isi_instruksi' => ['required', 'string'],
            'batas_waktu' => ['nullable', 'date'],
            'status' => ['required', Rule::in(['menunggu', 'diproses', 'selesai'])],
            'catatan' => ['nullable', 'string'],
        ]);

        $data['created_by'] = $request->user()->id;

        DisposisiSurat::create($data);
        SuratMasuk::whereKey($data['surat_masuk_id'])->update(['status' => 'didisposisikan']);
    }

    private function storePermohonan(Request $request): void
    {
        $user = $request->user();
        $data = $request->validate([
            'masyarakat_id' => ['required', 'exists:masyarakat,id'],
            'keperluan' => ['required', 'string'],
            'catatan' => ['nullable', 'string'],
        ]);

        $masyarakat = Masyarakat::findOrFail($data['masyarakat_id']);
        abort_if($user->hasRole(User::ROLE_MASYARAKAT) && $masyarakat->user_id !== $user->id, 403);

        PermohonanSktm::create([
            'nomor_pengajuan' => 'SKTM-'.now()->format('YmdHis'),
            'masyarakat_id' => $masyarakat->id,
            'nik' => $masyarakat->nik,
            'nama_pemohon' => $masyarakat->nama_lengkap,
            'alamat' => $masyarakat->alamat ?: '-',
            'keperluan' => $data['keperluan'],
            'tanggal_pengajuan' => now()->toDateString(),
            'status' => PermohonanSktm::STATUS_MENUNGGU,
            'catatan' => $data['catatan'] ?? null,
        ]);
    }

    private function storeDokumen(Request $request): void
    {
        $data = $request->validate([
            'permohonan_sktm_id' => ['required', 'exists:permohonan_sktm,id'],
            'jenis_dokumen' => ['required', Rule::in(['KTP', 'Kartu Keluarga', 'Surat Pengantar RT', 'Dokumen Pendukung Lain'])],
            'nama_file' => ['required', 'string', 'max:255'],
            'path_file' => ['required', 'string', 'max:255'],
        ]);

        $permohonan = PermohonanSktm::with('masyarakat')->findOrFail($data['permohonan_sktm_id']);
        abort_if($request->user()->hasRole(User::ROLE_MASYARAKAT) && $permohonan->masyarakat->user_id !== $request->user()->id, 403);

        DokumenSktm::create($data);
    }

    private function storePenerbitan(Request $request): void
    {
        abort_unless($request->user()->hasRole([User::ROLE_ADMIN, User::ROLE_PETUGAS]), 403);

        $data = $request->validate([
            'permohonan_sktm_id' => ['required', 'exists:permohonan_sktm,id'],
            'nomor_surat' => ['required', 'string', 'max:255', 'unique:penerbitan_sktm,nomor_surat'],
            'tanggal_terbit' => ['required', 'date'],
            'pejabat_penandatangan' => ['required', 'string', 'max:255'],
        ]);

        $data['diterbitkan_oleh'] = $request->user()->id;
        $data['status'] = 'diterbitkan';

        PenerbitanSktm::create($data);
        PermohonanSktm::whereKey($data['permohonan_sktm_id'])->update(['status' => PermohonanSktm::STATUS_DITERBITKAN]);
    }

    private function storeArsip(Request $request): void
    {
        $data = $request->validate([
            'jenis_arsip' => ['required', 'string', 'max:255'],
            'judul_dokumen' => ['required', 'string', 'max:255'],
            'nomor_dokumen' => ['nullable', 'string', 'max:255'],
            'tanggal_dokumen' => ['nullable', 'date'],
            'file_dokumen' => ['nullable', 'string', 'max:255'],
            'keterangan' => ['nullable', 'string'],
        ]);

        $data['created_by'] = $request->user()->id;

        ArsipDokumen::create($data);
    }
}
