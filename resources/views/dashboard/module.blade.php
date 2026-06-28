@php
    $type = $module['type'] ?? 'dashboard';
    $records = $module['records'] ?? collect();
    $input = 'h-11 rounded-md border-slate-200 bg-white text-sm font-semibold text-slate-700 shadow-sm focus:border-blue-500 focus:ring-blue-500';
    $canCreate = ! (in_array($type, ['verifikasi-sktm', 'laporan', 'penerbitan-sktm'], true) && $role === App\Models\User::ROLE_MASYARAKAT);

    $parentTitle = match ($type) {
        'surat-masuk', 'surat-keluar', 'disposisi-surat' => 'Inventori Surat',
        'permohonan-sktm', 'verifikasi-sktm', 'penerbitan-sktm', 'dokumen-saya' => 'Pelayanan SKTM',
        'users' => 'Manajemen Sistem',
        'masyarakat' => $role === App\Models\User::ROLE_MASYARAKAT ? 'Masyarakat' : 'Data Master',
        'arsip-surat' => 'Arsip & Dokumen',
        'laporan' => 'Laporan',
        default => 'Dashboard',
    };

    $buttonLabel = match ($type) {
        'surat-masuk' => 'Tambah Surat Masuk',
        'surat-keluar' => 'Tambah Surat Keluar',
        'disposisi-surat' => 'Tambah Disposisi',
        'permohonan-sktm' => 'Tambah Permohonan',
        'dokumen-saya' => 'Tambah Dokumen',
        'penerbitan-sktm' => 'Terbitkan SKTM',
        'arsip-surat' => 'Tambah Arsip',
        'users' => 'Tambah Pengguna',
        'masyarakat' => $role === App\Models\User::ROLE_MASYARAKAT ? 'Lengkapi Profil' : 'Tambah Masyarakat',
        default => 'Tambah Data',
    };

    $statusOptions = match ($type) {
        'surat-masuk' => ['baru' => 'Baru', 'diproses' => 'Diproses', 'didisposisikan' => 'Didisposisikan', 'diarsipkan' => 'Diarsipkan'],
        'surat-keluar' => ['draft' => 'Draft', 'diterbitkan' => 'Diterbitkan', 'dikirim' => 'Dikirim', 'diarsipkan' => 'Diarsipkan'],
        'disposisi-surat' => ['menunggu' => 'Menunggu', 'diproses' => 'Diproses', 'selesai' => 'Selesai'],
        'permohonan-sktm', 'verifikasi-sktm' => ['menunggu' => 'Menunggu', 'diverifikasi' => 'Diverifikasi', 'disetujui' => 'Disetujui', 'ditolak' => 'Ditolak', 'diterbitkan' => 'Diterbitkan'],
        'users' => ['aktif' => 'Aktif', 'nonaktif' => 'Nonaktif'],
        default => [],
    };

    $statusBadge = function (?string $status): string {
        return match ($status) {
            'menunggu', 'baru', 'draft' => 'bg-yellow-50 text-yellow-700',
            'diverifikasi', 'diproses', 'didisposisikan', 'dikirim' => 'bg-cyan-50 text-cyan-700',
            'disetujui', 'diterbitkan', 'selesai', 'aktif' => 'bg-emerald-50 text-emerald-700',
            'ditolak', 'nonaktif' => 'bg-red-50 text-red-700',
            default => 'bg-slate-100 text-slate-700',
        };
    };
@endphp

<section class="space-y-6" x-data="{ showForm: {{ $errors->any() ? 'true' : 'false' }} }">
    <div class="flex flex-col gap-4 border-b border-slate-200 bg-white px-8 py-5 lg:flex-row lg:items-center lg:justify-between">
        <nav class="flex items-center gap-3 text-[15px] font-extrabold text-slate-500" aria-label="Breadcrumb">
            <span>{{ $parentTitle }}</span>
            <svg viewBox="0 0 20 20" class="h-4 w-4 fill-current text-slate-400" aria-hidden="true">
                <path d="M7.3 4.3a1 1 0 0 1 1.4 0l5 5a1 1 0 0 1 0 1.4l-5 5a1 1 0 1 1-1.4-1.4L11.6 10 7.3 5.7a1 1 0 0 1 0-1.4Z" />
            </svg>
            <span class="text-slate-800">{{ $currentTitle }}</span>
        </nav>

        <div class="flex items-center gap-3">
            @if (session('status'))
                <div class="rounded-md bg-emerald-50 px-4 py-2 text-sm font-bold text-emerald-700">{{ session('status') }}</div>
            @endif

            @if ($canCreate && $type !== 'laporan')
                <button type="button" @click="showForm = ! showForm" class="inline-flex h-12 items-center justify-center gap-3 rounded-md bg-[#2379d7] px-5 text-[15px] font-extrabold text-white shadow-sm hover:bg-[#1768bd]">
                    <svg viewBox="0 0 24 24" class="h-5 w-5 fill-none stroke-current stroke-2.5" aria-hidden="true">
                        <path d="M12 5v14M5 12h14" />
                    </svg>
                    {{ $buttonLabel }}
                </button>
            @endif
        </div>
    </div>

    @if ($errors->any())
        <div class="mx-8 rounded-md border border-red-100 bg-red-50 px-4 py-3 text-sm font-semibold text-red-700">
            {{ $errors->first() }}
        </div>
    @endif

    @if ($canCreate && $type !== 'laporan')
        <form x-show="showForm" method="POST" action="{{ route('dashboard.section.store', $activeSection) }}" class="mx-8 rounded-lg border border-slate-200 bg-white p-5 shadow-[0_1px_4px_rgba(15,23,42,0.05)]">
            @csrf
            <div class="mb-4 flex items-center justify-between">
                <h4 class="text-[16px] font-extrabold text-slate-800">{{ $buttonLabel }}</h4>
                <button type="button" @click="showForm = false" class="text-sm font-bold text-slate-500 hover:text-slate-800">Tutup</button>
            </div>

            <div class="grid gap-4 md:grid-cols-2 xl:grid-cols-4">
                @if ($type === 'users')
                    <input name="name" placeholder="Nama" class="{{ $input }}" required>
                    <input name="username" placeholder="Username" class="{{ $input }}">
                    <input name="email" type="email" placeholder="Email" class="{{ $input }}" required>
                    <select name="role" class="{{ $input }}" required>
                        <option value="admin">Admin</option>
                        <option value="petugas">Petugas</option>
                        <option value="masyarakat">Masyarakat</option>
                    </select>
                    <select name="status" class="{{ $input }}" required>
                        <option value="aktif">Aktif</option>
                        <option value="nonaktif">Nonaktif</option>
                    </select>
                    <input name="password" type="password" placeholder="Password" class="{{ $input }}" required>
                @elseif ($type === 'masyarakat')
                    <input name="nik" placeholder="NIK" class="{{ $input }}" required>
                    <input name="nama_lengkap" placeholder="Nama lengkap" class="{{ $input }}" required>
                    <input name="tempat_lahir" placeholder="Tempat lahir" class="{{ $input }}">
                    <input name="tanggal_lahir" type="date" class="{{ $input }}">
                    <select name="jenis_kelamin" class="{{ $input }}">
                        <option value="">Jenis kelamin</option>
                        <option value="Laki-laki">Laki-laki</option>
                        <option value="Perempuan">Perempuan</option>
                    </select>
                    <input name="rt" placeholder="RT" class="{{ $input }}">
                    <input name="rw" placeholder="RW" class="{{ $input }}">
                    <input name="desa" placeholder="Desa/Kelurahan" class="{{ $input }}">
                    <input name="kecamatan" placeholder="Kecamatan" value="Marangkayu" class="{{ $input }}">
                    <input name="no_hp" placeholder="Nomor HP" class="{{ $input }}">
                    <input name="email" type="email" placeholder="Email" class="{{ $input }}">
                    <input name="pekerjaan" placeholder="Pekerjaan" class="{{ $input }}">
                    <textarea name="alamat" placeholder="Alamat" class="{{ $input }} min-h-20 md:col-span-2 xl:col-span-4"></textarea>
                @elseif ($type === 'surat-masuk')
                    <input name="nomor_agenda" placeholder="Nomor agenda" class="{{ $input }}" required>
                    <input name="nomor_surat" placeholder="Nomor surat" class="{{ $input }}" required>
                    <input name="asal_surat" placeholder="Asal surat" class="{{ $input }}" required>
                    <input name="perihal" placeholder="Perihal" class="{{ $input }}" required>
                    <input name="tanggal_surat" type="date" class="{{ $input }}" required>
                    <input name="tanggal_diterima" type="date" class="{{ $input }}" required>
                    <select name="status" class="{{ $input }}" required>
                        <option value="baru">Baru</option>
                        <option value="diproses">Diproses</option>
                        <option value="didisposisikan">Didisposisikan</option>
                        <option value="diarsipkan">Diarsipkan</option>
                    </select>
                    <input name="keterangan" placeholder="Keterangan" class="{{ $input }}">
                    <textarea name="isi_ringkas" placeholder="Ringkasan isi" class="{{ $input }} min-h-20 md:col-span-2 xl:col-span-4"></textarea>
                @elseif ($type === 'surat-keluar')
                    <input name="nomor_agenda" placeholder="Nomor agenda" class="{{ $input }}" required>
                    <input name="nomor_surat" placeholder="Nomor surat" class="{{ $input }}" required>
                    <input name="tujuan_surat" placeholder="Tujuan surat" class="{{ $input }}" required>
                    <input name="perihal" placeholder="Perihal" class="{{ $input }}" required>
                    <input name="tanggal_surat" type="date" class="{{ $input }}" required>
                    <select name="status" class="{{ $input }}" required>
                        <option value="draft">Draft</option>
                        <option value="diterbitkan">Diterbitkan</option>
                        <option value="dikirim">Dikirim</option>
                        <option value="diarsipkan">Diarsipkan</option>
                    </select>
                    <textarea name="isi_ringkas" placeholder="Isi ringkas" class="{{ $input }} min-h-20 md:col-span-2 xl:col-span-4"></textarea>
                @elseif ($type === 'disposisi-surat')
                    <select name="surat_masuk_id" class="{{ $input }}" required>
                        <option value="">Pilih surat masuk</option>
                        @foreach (($module['suratMasuk'] ?? []) as $surat)
                            <option value="{{ $surat->id }}">{{ $surat->nomor_surat }} - {{ $surat->perihal }}</option>
                        @endforeach
                    </select>
                    <input name="nomor_disposisi" placeholder="Nomor disposisi" class="{{ $input }}" required>
                    <input name="tanggal_disposisi" type="date" class="{{ $input }}" required>
                    <input name="tujuan_disposisi" placeholder="Tujuan disposisi" class="{{ $input }}" required>
                    <input name="batas_waktu" type="date" class="{{ $input }}">
                    <select name="status" class="{{ $input }}" required>
                        <option value="menunggu">Menunggu</option>
                        <option value="diproses">Diproses</option>
                        <option value="selesai">Selesai</option>
                    </select>
                    <textarea name="isi_instruksi" placeholder="Isi instruksi" class="{{ $input }} min-h-20 md:col-span-2 xl:col-span-4" required></textarea>
                @elseif ($type === 'permohonan-sktm')
                    <select name="masyarakat_id" class="{{ $input }}" required>
                        <option value="">Pilih masyarakat</option>
                        @foreach (($module['masyarakat'] ?? []) as $masyarakat)
                            <option value="{{ $masyarakat->id }}">{{ $masyarakat->nik }} - {{ $masyarakat->nama_lengkap }}</option>
                        @endforeach
                    </select>
                    <textarea name="keperluan" placeholder="Keperluan pengajuan" class="{{ $input }} min-h-20 md:col-span-2 xl:col-span-2" required></textarea>
                    <textarea name="catatan" placeholder="Catatan opsional" class="{{ $input }} min-h-20 md:col-span-2 xl:col-span-2"></textarea>
                @elseif ($type === 'dokumen-saya')
                    <select name="permohonan_sktm_id" class="{{ $input }}" required>
                        <option value="">Pilih permohonan</option>
                        @foreach (($module['permohonan'] ?? []) as $permohonan)
                            <option value="{{ $permohonan->id }}">{{ $permohonan->nomor_pengajuan }}</option>
                        @endforeach
                    </select>
                    <select name="jenis_dokumen" class="{{ $input }}" required>
                        <option value="KTP">KTP</option>
                        <option value="Kartu Keluarga">Kartu Keluarga</option>
                        <option value="Surat Pengantar RT">Surat Pengantar RT</option>
                        <option value="Dokumen Pendukung Lain">Dokumen Pendukung Lain</option>
                    </select>
                    <input name="nama_file" placeholder="Nama file" class="{{ $input }}" required>
                    <input name="path_file" placeholder="Path file / nama dokumen" class="{{ $input }}" required>
                @elseif ($type === 'penerbitan-sktm')
                    <select name="permohonan_sktm_id" class="{{ $input }}" required>
                        <option value="">Pilih permohonan disetujui</option>
                        @foreach (($module['permohonan'] ?? []) as $permohonan)
                            <option value="{{ $permohonan->id }}">{{ $permohonan->nomor_pengajuan }} - {{ $permohonan->nama_pemohon }}</option>
                        @endforeach
                    </select>
                    <input name="nomor_surat" placeholder="Nomor surat SKTM" class="{{ $input }}" required>
                    <input name="tanggal_terbit" type="date" class="{{ $input }}" required>
                    <input name="pejabat_penandatangan" placeholder="Pejabat penandatangan" class="{{ $input }}" required>
                @elseif ($type === 'arsip-surat')
                    <input name="jenis_arsip" placeholder="Jenis arsip" class="{{ $input }}" required>
                    <input name="judul_dokumen" placeholder="Judul dokumen" class="{{ $input }}" required>
                    <input name="nomor_dokumen" placeholder="Nomor dokumen" class="{{ $input }}">
                    <input name="tanggal_dokumen" type="date" class="{{ $input }}">
                    <input name="file_dokumen" placeholder="File dokumen" class="{{ $input }}">
                    <textarea name="keterangan" placeholder="Keterangan" class="{{ $input }} min-h-20 md:col-span-2 xl:col-span-4"></textarea>
                @endif
            </div>

            <div class="mt-5 flex justify-end">
                <button class="h-11 rounded-md bg-[#2379d7] px-5 text-sm font-extrabold text-white hover:bg-[#1768bd]">Simpan</button>
            </div>
        </form>
    @endif

    <div class="mx-8 rounded-lg border border-slate-200 bg-white shadow-[0_1px_4px_rgba(15,23,42,0.05)]">
        <form method="GET" class="flex flex-col gap-4 border-b border-slate-200 p-5 lg:flex-row lg:items-center">
            <div class="relative w-full lg:max-w-[460px]">
                <svg viewBox="0 0 24 24" class="pointer-events-none absolute left-4 top-1/2 h-5 w-5 -translate-y-1/2 fill-slate-400" aria-hidden="true">
                    <path d="M10.5 4a6.5 6.5 0 1 0 4 11.6l4 4 1.4-1.4-4-4A6.5 6.5 0 0 0 10.5 4Zm0 2a4.5 4.5 0 1 1 0 9 4.5 4.5 0 0 1 0-9Z" />
                </svg>
                <input name="q" value="{{ request('q') }}" placeholder="Cari {{ strtolower($currentTitle) }}..." class="{{ $input }} w-full pl-12">
            </div>

            @if ($statusOptions)
                <select name="status" class="{{ $input }} w-full lg:w-[220px]">
                    <option value="">Semua Status</option>
                    @foreach ($statusOptions as $value => $label)
                        <option value="{{ $value }}" @selected(request('status') === $value)>{{ $label }}</option>
                    @endforeach
                </select>
            @endif

            <button class="h-11 rounded-md border border-slate-300 px-5 text-sm font-extrabold text-slate-700 hover:bg-slate-50">Filter</button>
        </form>

        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-slate-200 text-left text-[14px]">
                @if ($type === 'surat-masuk')
                    <thead class="bg-slate-50 text-[13px] font-extrabold text-slate-700">
                        <tr>
                            <th class="px-6 py-4">No. Agenda</th>
                            <th class="px-6 py-4">Tanggal Masuk</th>
                            <th class="px-6 py-4">Asal Surat</th>
                            <th class="px-6 py-4">Perihal</th>
                            <th class="px-6 py-4">Ringkasan Isi</th>
                            <th class="px-6 py-4">Status</th>
                            <th class="px-6 py-4 text-center">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100 bg-white">
                        @forelse ($records as $record)
                            <tr class="hover:bg-slate-50">
                                <td class="px-6 py-5 font-extrabold text-slate-700">{{ $record->nomor_agenda }}</td>
                                <td class="px-6 py-5 font-semibold text-slate-600">{{ $record->tanggal_diterima?->format('d M Y') }}</td>
                                <td class="max-w-[250px] px-6 py-5 font-semibold leading-7 text-slate-600">{{ $record->asal_surat }}</td>
                                <td class="max-w-[250px] px-6 py-5 font-semibold leading-7 text-slate-700">{{ $record->perihal }}</td>
                                <td class="max-w-[300px] px-6 py-5 font-semibold leading-7 text-slate-600">{{ $record->isi_ringkas ?: '-' }}</td>
                                <td class="px-6 py-5">
                                    <span class="{{ $statusBadge($record->status) }} rounded-md px-3 py-1.5 text-xs font-extrabold">{{ Str::title($record->status) }}</span>
                                </td>
                                <td class="px-6 py-5">
                                    <div class="flex justify-center gap-4 text-slate-500">
                                        <button type="button" class="hover:text-[#2379d7]" title="Detail">
                                            <svg viewBox="0 0 24 24" class="h-5 w-5 fill-current"><path d="M12 5c-6 0-9.5 7-9.5 7S6 19 12 19s9.5-7 9.5-7S18 5 12 5Zm0 11a4 4 0 1 1 0-8 4 4 0 0 1 0 8Z" /></svg>
                                        </button>
                                        <button type="button" class="hover:text-[#2379d7]" title="Edit">
                                            <svg viewBox="0 0 24 24" class="h-5 w-5 fill-current"><path d="m4 17.2-.8 3.6 3.6-.8L18.9 7.9 16.1 5.1 4 17.2ZM20.7 6.1a1 1 0 0 0 0-1.4l-1.4-1.4a1 1 0 0 0-1.4 0l-.8.8 2.8 2.8.8-.8Z" /></svg>
                                        </button>
                                        <form method="POST" action="{{ route('dashboard.section.destroy', [$activeSection, $record->id]) }}">
                                            @csrf
                                            @method('DELETE')
                                            <button class="hover:text-red-600" title="Hapus">
                                                <svg viewBox="0 0 24 24" class="h-5 w-5 fill-current"><path d="M7 21a2 2 0 0 1-2-2V7h14v12a2 2 0 0 1-2 2H7ZM9 4h6l1 2H8l1-2Z" /></svg>
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr><td colspan="7" class="px-6 py-10 text-center font-semibold text-slate-500">Belum ada data.</td></tr>
                        @endforelse
                    </tbody>
                @else
                    <thead class="bg-slate-50 text-[13px] font-extrabold text-slate-700">
                        <tr>
                            <th class="px-6 py-4">Data</th>
                            <th class="px-6 py-4">Detail</th>
                            <th class="px-6 py-4">Status</th>
                            <th class="px-6 py-4 text-center">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100 bg-white">
                        @forelse ($records as $record)
                            @php
                                $title = match ($type) {
                                    'users' => $record->name,
                                    'masyarakat' => $record->nama_lengkap,
                                    'surat-keluar' => $record->nomor_surat,
                                    'disposisi-surat' => $record->nomor_disposisi,
                                    'permohonan-sktm', 'verifikasi-sktm' => $record->nomor_pengajuan,
                                    'dokumen-saya' => $record->jenis_dokumen,
                                    'penerbitan-sktm' => $record->nomor_surat,
                                    'arsip-surat' => $record->judul_dokumen,
                                    default => '-',
                                };
                                $detail = match ($type) {
                                    'users' => ($record->username ?: '-').' - '.$record->email,
                                    'masyarakat' => $record->nik.' - '.$record->desa,
                                    'surat-keluar' => $record->tujuan_surat.' - '.$record->perihal,
                                    'disposisi-surat' => optional($record->suratMasuk)->perihal.' - '.$record->tujuan_disposisi,
                                    'permohonan-sktm', 'verifikasi-sktm' => $record->nama_pemohon.' - '.$record->keperluan,
                                    'dokumen-saya' => $record->nama_file,
                                    'penerbitan-sktm' => optional($record->permohonanSktm)->nama_pemohon.' - '.$record->tanggal_terbit?->format('d/m/Y'),
                                    'arsip-surat' => $record->jenis_arsip.' - '.$record->nomor_dokumen,
                                    default => '-',
                                };
                                $status = $type === 'users' ? $record->status : ($record->status ?? $record->role ?? $record->status_data ?? '-');
                            @endphp
                            <tr class="hover:bg-slate-50">
                                <td class="px-6 py-5 font-extrabold text-slate-700">{{ $title }}</td>
                                <td class="max-w-[480px] px-6 py-5 font-semibold leading-7 text-slate-600">{{ $detail }}</td>
                                <td class="px-6 py-5">
                                    <span class="{{ $statusBadge($status) }} rounded-md px-3 py-1.5 text-xs font-extrabold">{{ Str::title(str_replace('_', ' ', $status)) }}</span>
                                </td>
                                <td class="px-6 py-5">
                                    <div class="flex justify-center gap-4 text-slate-500">
                                        @if ($type === 'verifikasi-sktm')
                                            <form method="POST" action="{{ route('dashboard.permohonan.verify', $record) }}" class="flex gap-2">
                                                @csrf
                                                @method('PATCH')
                                                <input name="catatan" placeholder="Catatan" class="h-9 w-28 rounded border-slate-300 text-xs">
                                                <button name="status" value="disetujui" class="h-9 rounded bg-emerald-600 px-3 text-xs font-bold text-white">Setujui</button>
                                                <button name="status" value="ditolak" class="h-9 rounded bg-red-600 px-3 text-xs font-bold text-white">Tolak</button>
                                            </form>
                                        @elseif ($type !== 'laporan')
                                            <button type="button" class="hover:text-[#2379d7]" title="Detail">
                                                <svg viewBox="0 0 24 24" class="h-5 w-5 fill-current"><path d="M12 5c-6 0-9.5 7-9.5 7S6 19 12 19s9.5-7 9.5-7S18 5 12 5Zm0 11a4 4 0 1 1 0-8 4 4 0 0 1 0 8Z" /></svg>
                                            </button>
                                            <button type="button" class="hover:text-[#2379d7]" title="Edit">
                                                <svg viewBox="0 0 24 24" class="h-5 w-5 fill-current"><path d="m4 17.2-.8 3.6 3.6-.8L18.9 7.9 16.1 5.1 4 17.2ZM20.7 6.1a1 1 0 0 0 0-1.4l-1.4-1.4a1 1 0 0 0-1.4 0l-.8.8 2.8 2.8.8-.8Z" /></svg>
                                            </button>
                                            <form method="POST" action="{{ route('dashboard.section.destroy', [$activeSection, $record->id]) }}">
                                                @csrf
                                                @method('DELETE')
                                                <button class="hover:text-red-600" title="Hapus">
                                                    <svg viewBox="0 0 24 24" class="h-5 w-5 fill-current"><path d="M7 21a2 2 0 0 1-2-2V7h14v12a2 2 0 0 1-2 2H7ZM9 4h6l1 2H8l1-2Z" /></svg>
                                                </button>
                                            </form>
                                        @endif
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr><td colspan="4" class="px-6 py-10 text-center font-semibold text-slate-500">Belum ada data.</td></tr>
                        @endforelse
                    </tbody>
                @endif
            </table>
        </div>
    </div>

    @if (method_exists($records, 'links'))
        <div class="px-8 pb-6">{{ $records->links() }}</div>
    @endif
</section>
