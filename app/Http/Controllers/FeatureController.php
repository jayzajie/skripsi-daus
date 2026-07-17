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
use Illuminate\Support\Facades\Storage;
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
            'data-masyarakat', 'profil-saya' => $this->destroyMasyarakat($request, $id),
            'surat-masuk' => SuratMasuk::destroy($id),
            'surat-keluar' => SuratKeluar::destroy($id),
            'disposisi-surat' => DisposisiSurat::destroy($id),
            'permohonan-sktm', 'ajukan-sktm', 'status-pengajuan' => $this->destroyPermohonan($request, $id),
            'dokumen-saya' => $this->destroyDokumen($request, $id),
            'penerbitan-sktm' => PenerbitanSktm::destroy($id),
            'arsip-surat' => ArsipDokumen::destroy($id),
            default => abort(404),
        };

        return back()->with('status', 'Data berhasil dihapus.');
    }

    public function verify(Request $request, PermohonanSktm $permohonan): RedirectResponse
    {
        abort_unless($request->user()->hasRole([User::ROLE_ADMIN, User::ROLE_KEPALA_KECAMATAN]), 403);

        if ($request->user()->hasRole(User::ROLE_KEPALA_KECAMATAN)) {
            abort_unless($permohonan->status === PermohonanSktm::STATUS_DIVERIFIKASI, 409);
        }

        $statuses = $request->user()->hasRole(User::ROLE_KEPALA_KECAMATAN)
            ? [PermohonanSktm::STATUS_DISETUJUI, PermohonanSktm::STATUS_DITOLAK]
            : [PermohonanSktm::STATUS_DIVERIFIKASI];

        $data = $request->validate([
            'status' => ['required', Rule::in($statuses)],
            'catatan' => ['nullable', 'required_if:status,'.PermohonanSktm::STATUS_DITOLAK, 'string'],
        ]);

        $permohonan->update($data);

        return back()->with('status', 'Status pengajuan berhasil diperbarui.');
    }

    public function replyToIncoming(Request $request, SuratMasuk $suratMasuk): RedirectResponse
    {
        abort_unless($request->user()->hasRole(User::ROLE_ADMIN), 403);

        $stamp = now()->format('YmdHis');

        SuratKeluar::create([
            'surat_masuk_id' => $suratMasuk->id,
            'nomor_agenda' => "BL-{$suratMasuk->id}-{$stamp}",
            'nomor_surat' => "DRAFT-{$suratMasuk->id}-{$stamp}",
            'tujuan_surat' => $suratMasuk->asal_surat,
            'perihal' => 'Balasan: '.$suratMasuk->perihal,
            'tanggal_surat' => now()->toDateString(),
            'isi_ringkas' => trim("Balasan atas surat {$suratMasuk->nomor_surat}. {$suratMasuk->isi_ringkas}"),
            'status' => 'draft',
            'created_by' => $request->user()->id,
        ]);

        $suratMasuk->update(['status' => 'diproses']);

        return redirect()->route('dashboard.section', 'surat-keluar')->with('status', 'Draft balasan berhasil dibuat.');
    }

    public function updateSuratKeluar(Request $request, SuratKeluar $suratKeluar): RedirectResponse
    {
        abort_unless($request->user()->hasRole(User::ROLE_ADMIN), 403);

        $data = $request->validate([
            'nomor_agenda' => ['required', 'string', 'max:255', Rule::unique('surat_keluar', 'nomor_agenda')->ignore($suratKeluar->id)],
            'nomor_surat' => ['required', 'string', 'max:255'],
            'tujuan_surat' => ['required', 'string', 'max:255'],
            'perihal' => ['required', 'string', 'max:255'],
            'tanggal_surat' => ['required', 'date'],
            'isi_ringkas' => ['nullable', 'string'],
            'status' => ['required', Rule::in(['draft', 'diterbitkan', 'dikirim', 'diarsipkan'])],
            'keterangan' => ['nullable', 'string'],
        ]);

        $suratKeluar->update($data);

        return back()->with('status', 'Surat keluar berhasil diperbarui.');
    }

    public function updateSuratMasuk(Request $request, SuratMasuk $suratMasuk): RedirectResponse
    {
        abort_unless($request->user()->hasRole(User::ROLE_ADMIN), 403);

        $suratMasuk->update($request->validate([
            'nomor_agenda' => ['required', 'string', 'max:255', Rule::unique('surat_masuk', 'nomor_agenda')->ignore($suratMasuk->id)],
            'nomor_surat' => ['required', 'string', 'max:255'],
            'asal_surat' => ['required', 'string', 'max:255'],
            'perihal' => ['required', 'string', 'max:255'],
            'tanggal_surat' => ['required', 'date'],
            'tanggal_diterima' => ['required', 'date'],
            'isi_ringkas' => ['nullable', 'string'],
            'status' => ['required', Rule::in(['baru', 'dibaca', 'diproses', 'selesai', 'didisposisikan', 'diarsipkan'])],
            'keterangan' => ['nullable', 'string'],
        ]));

        return back()->with('status', 'Surat masuk berhasil diperbarui.');
    }

    public function updateMasyarakat(Request $request, Masyarakat $masyarakat): RedirectResponse
    {
        abort_unless(
            $request->user()->hasRole(User::ROLE_ADMIN)
            || ($request->user()->hasRole(User::ROLE_MASYARAKAT) && $masyarakat->user_id === $request->user()->id),
            403
        );

        $masyarakat->update($request->validate([
            'nik' => ['required', 'string', 'max:32', Rule::unique('masyarakat', 'nik')->ignore($masyarakat->id)],
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
        ]));

        return back()->with('status', 'Data masyarakat berhasil diperbarui.');
    }

    public function updatePermohonan(Request $request, PermohonanSktm $permohonan): RedirectResponse
    {
        abort_unless(
            $request->user()->hasRole(User::ROLE_ADMIN)
            || ($request->user()->hasRole(User::ROLE_MASYARAKAT) && $permohonan->masyarakat->user_id === $request->user()->id),
            403
        );

        $data = $request->validate([
            'keperluan' => ['required', 'string'],
            'catatan' => ['nullable', 'string'],
            'status' => ['nullable', Rule::in([PermohonanSktm::STATUS_MENUNGGU, PermohonanSktm::STATUS_DIVERIFIKASI])],
        ]);

        if ($request->user()->hasRole(User::ROLE_MASYARAKAT)) {
            unset($data['status']);
        }

        $permohonan->update($data);

        return back()->with('status', 'Permohonan Surat Keterangan Tidak Mampu berhasil diperbarui.');
    }

    public function printSktm(Request $request, PenerbitanSktm $penerbitan)
    {
        $penerbitan->load('permohonanSktm.masyarakat', 'penerbit');

        abort_unless(
            $request->user()->hasRole(User::ROLE_ADMIN)
            || ($request->user()->hasRole(User::ROLE_MASYARAKAT)
                && $penerbitan->permohonanSktm->masyarakat->user_id === $request->user()->id),
            403
        );

        return view('dashboard.print-sktm', ['penerbitan' => $penerbitan]);
    }

    public function downloadSktm(Request $request, PenerbitanSktm $penerbitan)
    {
        $penerbitan->load('permohonanSktm.masyarakat');

        abort_unless(
            $request->user()->hasRole(User::ROLE_ADMIN)
            || ($request->user()->hasRole(User::ROLE_MASYARAKAT)
                && $penerbitan->permohonanSktm->masyarakat->user_id === $request->user()->id),
            403
        );

        $pdf = $this->simplePdf('SURAT KETERANGAN TIDAK MAMPU', [
            'PEMERINTAH KABUPATEN KUTAI KARTANEGARA',
            'KECAMATAN MARANGKAYU',
            'SURAT KETERANGAN TIDAK MAMPU',
            'Nomor: '.str_replace('SKTM', 'Surat Keterangan Tidak Mampu', $penerbitan->nomor_surat),
            'Nama: '.$penerbitan->permohonanSktm->nama_pemohon,
            'NIK: '.$penerbitan->permohonanSktm->nik,
            'Alamat: '.$penerbitan->permohonanSktm->alamat,
            'Keperluan: '.$penerbitan->permohonanSktm->keperluan,
            'Tanggal Terbit: '.$penerbitan->tanggal_terbit?->format('d/m/Y'),
            'Masa Berlaku: '.($penerbitan->masa_berlaku?->format('d/m/Y') ?: '-'),
            'Pejabat Penandatangan: '.$penerbitan->pejabat_penandatangan,
        ]);

        return response($pdf, 200, [
            'Content-Type' => 'application/pdf',
            'Content-Disposition' => 'attachment; filename="sktm-'.$penerbitan->id.'.pdf"',
        ]);
    }

    public function exportLaporan(Request $request)
    {
        abort_unless($request->user()->hasRole([User::ROLE_ADMIN, User::ROLE_KEPALA_KECAMATAN]), 403);

        $rows = [
            ['Jenis Data', 'Total'],
            ['Surat Masuk', SuratMasuk::count()],
            ['Surat Keluar', SuratKeluar::count()],
            ['Disposisi Surat', DisposisiSurat::count()],
            ['Permohonan Surat Keterangan Tidak Mampu', PermohonanSktm::count()],
            ['Surat Keterangan Tidak Mampu Terbit', PenerbitanSktm::count()],
            ['Arsip Dokumen', ArsipDokumen::count()],
        ];

        if ($request->query('format') === 'pdf') {
            $pdf = $this->simplePdf('LAPORAN ADMINISTRASI', collect($rows)->map(fn ($row) => implode(' : ', $row))->all());

            return response($pdf, 200, [
                'Content-Type' => 'application/pdf',
                'Content-Disposition' => 'attachment; filename="laporan-administrasi.pdf"',
            ]);
        }

        $body = collect($rows)->map(fn ($row) => implode("\t", $row))->implode("\n");

        return response($body, 200, [
            'Content-Type' => 'application/vnd.ms-excel; charset=UTF-8',
            'Content-Disposition' => 'attachment; filename="laporan-administrasi.xls"',
        ]);
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
        $existingId = $user->hasRole(User::ROLE_MASYARAKAT) ? $user->masyarakat?->id : null;
        $data = $request->validate([
            'nik' => ['required', 'string', 'max:32', Rule::unique('masyarakat', 'nik')->ignore($existingId)],
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

        $user->hasRole(User::ROLE_MASYARAKAT)
            ? Masyarakat::updateOrCreate(['user_id' => $user->id], $data)
            : Masyarakat::create($data);
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
            'status' => ['required', Rule::in(['baru', 'dibaca', 'diproses', 'selesai', 'didisposisikan', 'diarsipkan'])],
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
            'masyarakat_id' => [$user->hasRole(User::ROLE_MASYARAKAT) ? 'nullable' : 'required', 'exists:masyarakat,id'],
            'keperluan' => ['required', 'string'],
            'catatan' => ['nullable', 'string'],
        ]);

        $masyarakat = $user->hasRole(User::ROLE_MASYARAKAT)
            ? $user->masyarakat()->firstOrFail()
            : Masyarakat::findOrFail($data['masyarakat_id']);
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
            'nama_file' => ['nullable', 'string', 'max:255'],
            'path_file' => ['nullable', 'string', 'max:255'],
            'dokumen_file' => ['nullable', 'file', 'max:2048'],
        ]);

        $permohonan = PermohonanSktm::with('masyarakat')->findOrFail($data['permohonan_sktm_id']);
        abort_if($request->user()->hasRole(User::ROLE_MASYARAKAT) && $permohonan->masyarakat->user_id !== $request->user()->id, 403);

        if ($request->hasFile('dokumen_file')) {
            $file = $request->file('dokumen_file');
            $data['nama_file'] = ($data['nama_file'] ?? null) ?: $file->getClientOriginalName();
            $data['path_file'] = $file->store('dokumen-sktm', 'public');
        }

        abort_unless(($data['nama_file'] ?? null) && ($data['path_file'] ?? null), 422);
        unset($data['dokumen_file']);

        DokumenSktm::create($data);
    }

    private function storePenerbitan(Request $request): void
    {
        abort_unless($request->user()->hasRole(User::ROLE_ADMIN), 403);

        $data = $request->validate([
            'permohonan_sktm_id' => ['required', 'exists:permohonan_sktm,id'],
            'nomor_surat' => ['required', 'string', 'max:255', 'unique:penerbitan_sktm,nomor_surat'],
            'tanggal_terbit' => ['required', 'date'],
            'masa_berlaku' => ['nullable', 'date'],
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
            'arsip_file' => ['nullable', 'file', 'max:2048'],
            'keterangan' => ['nullable', 'string'],
        ]);

        if ($request->hasFile('arsip_file')) {
            $data['file_dokumen'] = $request->file('arsip_file')->store('arsip-dokumen', 'public');
        }

        unset($data['arsip_file']);
        $data['created_by'] = $request->user()->id;

        ArsipDokumen::create($data);
    }

    private function destroyMasyarakat(Request $request, int $id): void
    {
        $query = Masyarakat::whereKey($id);

        if ($request->user()->hasRole(User::ROLE_MASYARAKAT)) {
            $query->where('user_id', $request->user()->id);
        }

        abort_unless($query->delete(), 403);
    }

    private function destroyPermohonan(Request $request, int $id): void
    {
        $query = PermohonanSktm::whereKey($id);

        if ($request->user()->hasRole(User::ROLE_MASYARAKAT)) {
            $query->whereHas('masyarakat', fn ($inner) => $inner->where('user_id', $request->user()->id));
        }

        abort_unless($query->delete(), 403);
    }

    private function destroyDokumen(Request $request, int $id): void
    {
        $query = DokumenSktm::whereKey($id);

        if ($request->user()->hasRole(User::ROLE_MASYARAKAT)) {
            $query->whereHas('permohonanSktm.masyarakat', fn ($inner) => $inner->where('user_id', $request->user()->id));
        }

        $dokumen = $query->first();
        abort_unless($dokumen, 403);

        if ($dokumen->path_file) {
            Storage::disk('public')->delete($dokumen->path_file);
        }

        $dokumen->delete();
    }

    private function simplePdf(string $title, array $lines): string
    {
        $text = 'BT /F1 12 Tf 50 800 Td 16 TL';

        foreach ([$title, ...$lines] as $line) {
            $safe = preg_replace('/[^\x20-\x7E]/', ' ', $line);
            $safe = str_replace(['\\', '(', ')'], ['\\\\', '\\(', '\\)'], $safe);
            $text .= " ({$safe}) Tj T*";
        }

        $text .= ' ET';
        $objects = [
            '1 0 obj << /Type /Catalog /Pages 2 0 R >> endobj',
            '2 0 obj << /Type /Pages /Kids [3 0 R] /Count 1 >> endobj',
            '3 0 obj << /Type /Page /Parent 2 0 R /MediaBox [0 0 595 842] /Resources << /Font << /F1 4 0 R >> >> /Contents 5 0 R >> endobj',
            '4 0 obj << /Type /Font /Subtype /Type1 /BaseFont /Helvetica >> endobj',
            '5 0 obj << /Length '.strlen($text)." >> stream\n{$text}\nendstream endobj",
        ];

        $pdf = "%PDF-1.4\n";
        $offsets = [0];

        foreach ($objects as $object) {
            $offsets[] = strlen($pdf);
            $pdf .= $object."\n";
        }

        $xref = strlen($pdf);
        $pdf .= "xref\n0 ".(count($objects) + 1)."\n0000000000 65535 f \n";

        foreach (array_slice($offsets, 1) as $offset) {
            $pdf .= sprintf("%010d 00000 n \n", $offset);
        }

        return $pdf.'trailer << /Size '.(count($objects) + 1)." /Root 1 0 R >>\nstartxref\n{$xref}\n%%EOF";
    }
}
