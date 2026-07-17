@php
    $type = $module['type'] ?? 'dashboard';
    $records = $module['records'] ?? collect();
    $input = 'h-11 rounded-md border-slate-200 bg-white text-sm font-semibold text-slate-700 shadow-sm focus:border-blue-500 focus:ring-blue-500';
    $canCreate = ! in_array($type, ['verifikasi-sktm', 'laporan'], true)
        && ! ($type === 'penerbitan-sktm' && $role === App\Models\User::ROLE_MASYARAKAT)
        && $role !== App\Models\User::ROLE_KEPALA_KECAMATAN;
    $canDelete = $role !== App\Models\User::ROLE_KEPALA_KECAMATAN
        && in_array($activeSection, ['data-pengguna', 'data-masyarakat', 'profil-saya', 'surat-masuk', 'surat-keluar', 'disposisi-surat', 'permohonan-sktm', 'dokumen-saya', 'penerbitan-sktm', 'arsip-surat'], true);

    $parentTitle = match ($type) {
        'surat-masuk', 'surat-keluar', 'disposisi-surat' => 'Inventarisasi Surat',
        'permohonan-sktm', 'verifikasi-sktm', 'penerbitan-sktm', 'dokumen-saya' => 'Pelayanan Surat Keterangan Tidak Mampu',
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
        'penerbitan-sktm' => 'Terbitkan Surat Keterangan Tidak Mampu',
        'arsip-surat' => 'Tambah Arsip',
        'users' => 'Tambah Pengguna',
        'masyarakat' => $role === App\Models\User::ROLE_MASYARAKAT ? 'Lengkapi Profil' : 'Tambah Masyarakat',
        default => 'Tambah Data',
    };

    $statusOptions = match ($type) {
        'surat-masuk' => ['baru' => 'Baru', 'dibaca' => 'Dibaca', 'diproses' => 'Diproses', 'selesai' => 'Selesai', 'didisposisikan' => 'Didisposisikan', 'diarsipkan' => 'Diarsipkan'],
        'surat-keluar' => ['draft' => 'Draft', 'diterbitkan' => 'Diterbitkan', 'dikirim' => 'Dikirim', 'diarsipkan' => 'Diarsipkan'],
        'disposisi-surat' => ['menunggu' => 'Menunggu', 'diproses' => 'Diproses', 'selesai' => 'Selesai'],
        'permohonan-sktm' => ['menunggu' => 'Menunggu', 'diverifikasi' => 'Diverifikasi'],
        'verifikasi-sktm' => ['menunggu' => 'Menunggu', 'diverifikasi' => 'Diverifikasi', 'disetujui' => 'Disetujui', 'ditolak' => 'Ditolak'],
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

    @if ($type === 'laporan')
        <div class="mx-8 flex gap-3">
            <a href="{{ route('dashboard.laporan.export', ['format' => 'pdf']) }}" class="rounded-md bg-[#2379d7] px-4 py-2 text-sm font-extrabold text-white">Export PDF</a>
            <a href="{{ route('dashboard.laporan.export', ['format' => 'excel']) }}" class="rounded-md bg-emerald-600 px-4 py-2 text-sm font-extrabold text-white">Export Excel</a>
        </div>

        <div class="mx-8 grid gap-4 md:grid-cols-2 xl:grid-cols-3">
            @foreach (($module['summary'] ?? []) as $label => $value)
                <article class="rounded-lg border border-slate-200 bg-white p-5 shadow-[0_1px_4px_rgba(15,23,42,0.05)]">
                    <p class="text-sm font-extrabold text-slate-500">{{ $label }}</p>
                    <p class="mt-2 text-3xl font-extrabold text-[#1b2b3f]">{{ $value }}</p>
                </article>
            @endforeach
        </div>

        <div class="mx-8 grid gap-5 xl:grid-cols-3">
            @foreach ([
                'Status Surat Keterangan Tidak Mampu' => ($module['sktmStatus'] ?? collect()),
                'Status Surat Masuk' => ($module['suratMasukStatus'] ?? collect()),
                'Status Surat Keluar' => ($module['suratKeluarStatus'] ?? collect()),
            ] as $title => $items)
                <article class="rounded-lg border border-slate-200 bg-white shadow-[0_1px_4px_rgba(15,23,42,0.05)]">
                    <h4 class="border-b border-slate-200 px-5 py-4 text-base font-extrabold text-slate-800">{{ $title }}</h4>
                    <div class="divide-y divide-slate-100">
                        @forelse ($items as $status => $total)
                            <div class="flex items-center justify-between px-5 py-3 text-sm font-bold">
                                <span class="text-slate-600">{{ Str::title(str_replace('_', ' ', $status)) }}</span>
                                <span class="text-[#2379d7]">{{ $total }}</span>
                            </div>
                        @empty
                            <p class="px-5 py-6 text-sm font-semibold text-slate-500">Belum ada data.</p>
                        @endforelse
                    </div>
                </article>
            @endforeach
        </div>
    @else

    @if ($canCreate && $type !== 'laporan')
        <form x-show="showForm" method="POST" enctype="multipart/form-data" action="{{ route('dashboard.section.store', $activeSection) }}" class="mx-8 rounded-lg border border-slate-200 bg-white p-5 shadow-[0_1px_4px_rgba(15,23,42,0.05)]">
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
                        <option value="kepala_kecamatan">Kepala Kecamatan</option>
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
                    @if ($role !== App\Models\User::ROLE_MASYARAKAT)
                        <select name="masyarakat_id" class="{{ $input }}" required>
                            <option value="">Pilih masyarakat</option>
                            @foreach (($module['masyarakat'] ?? []) as $masyarakat)
                                <option value="{{ $masyarakat->id }}">{{ $masyarakat->nik }} - {{ $masyarakat->nama_lengkap }}</option>
                            @endforeach
                        </select>
                    @endif
                    <textarea name="keperluan" placeholder="Keperluan pengajuan" class="{{ $input }} min-h-20 md:col-span-2 xl:col-span-2" required></textarea>
                    <textarea name="catatan" placeholder="Catatan opsional" class="{{ $input }} min-h-20 md:col-span-2 xl:col-span-2"></textarea>
                @elseif ($type === 'dokumen-saya')
                    <select name="permohonan_sktm_id" class="{{ $input }}" required>
                        <option value="">Pilih permohonan</option>
                        @foreach (($module['permohonan'] ?? []) as $permohonan)
                            <option value="{{ $permohonan->id }}">{{ str_replace('SKTM', 'Surat Keterangan Tidak Mampu', $permohonan->nomor_pengajuan) }}</option>
                        @endforeach
                    </select>
                    <select name="jenis_dokumen" class="{{ $input }}" required>
                        <option value="KTP">KTP</option>
                        <option value="Kartu Keluarga">Kartu Keluarga</option>
                        <option value="Surat Pengantar RT">Surat Pengantar RT</option>
                        <option value="Dokumen Pendukung Lain">Dokumen Pendukung Lain</option>
                    </select>
                    <input name="nama_file" placeholder="Nama file" class="{{ $input }}">
                    <input name="dokumen_file" type="file" class="{{ $input }} p-2" required>
                @elseif ($type === 'penerbitan-sktm')
                    <select name="permohonan_sktm_id" class="{{ $input }}" required>
                        <option value="">Pilih permohonan disetujui</option>
                        @foreach (($module['permohonan'] ?? []) as $permohonan)
                            <option value="{{ $permohonan->id }}">{{ str_replace('SKTM', 'Surat Keterangan Tidak Mampu', $permohonan->nomor_pengajuan) }} - {{ $permohonan->nama_pemohon }}</option>
                        @endforeach
                    </select>
                    <input name="nomor_surat" placeholder="Nomor Surat Keterangan Tidak Mampu" class="{{ $input }}" required>
                    <input name="tanggal_terbit" type="date" class="{{ $input }}" required>
                    <input name="masa_berlaku" type="date" class="{{ $input }}">
                    <input name="pejabat_penandatangan" placeholder="Pejabat penandatangan" class="{{ $input }}" required>
                @elseif ($type === 'arsip-surat')
                    <input name="jenis_arsip" placeholder="Jenis arsip" class="{{ $input }}" required>
                    <input name="judul_dokumen" placeholder="Judul dokumen" class="{{ $input }}" required>
                    <input name="nomor_dokumen" placeholder="Nomor dokumen" class="{{ $input }}">
                    <input name="tanggal_dokumen" type="date" class="{{ $input }}">
                    <input name="arsip_file" type="file" class="{{ $input }} p-2">
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
                <input name="q" value="{{ request('q') }}" placeholder="{{ $type === 'verifikasi-sktm' ? 'Cari pengajuan...' : 'Cari '.strtolower($currentTitle).'...' }}" class="{{ $input }} w-full pl-12">
            </div>

            @if ($statusOptions)
                <select name="status" class="{{ $input }} w-full lg:w-[220px]">
                    <option value="">Semua Status</option>
                    @foreach ($statusOptions as $value => $label)
                        <option value="{{ $value }}" @selected(request('status') === $value)>{{ $label }}</option>
                    @endforeach
                </select>
            @endif

            @if ($type === 'penerbitan-sktm')
                <input name="tahun" value="{{ request('tahun') }}" placeholder="Tahun" class="{{ $input }} w-full lg:w-[130px]">
            @endif

            <button class="h-11 rounded-md border border-slate-300 px-5 text-sm font-extrabold text-slate-700 hover:bg-slate-50">Filter</button>
        </form>

        @if ($type === 'verifikasi-sktm')
            <div class="divide-y divide-slate-100 md:hidden">
                @forelse ($records as $record)
                    <article class="space-y-4 p-5">
                        <div>
                            <p class="font-extrabold text-slate-700">{{ str_replace('SKTM', 'Surat Keterangan Tidak Mampu', $record->nomor_pengajuan) }}</p>
                            <p class="mt-2 text-sm font-semibold leading-6 text-slate-600">{{ $record->nama_pemohon }} - {{ $record->keperluan }}</p>
                        </div>
                        <span class="{{ $statusBadge($record->status) }} inline-block rounded-md px-3 py-1.5 text-xs font-extrabold">{{ Str::title($record->status) }}</span>
                        <form method="POST" action="{{ route('dashboard.permohonan.verify', $record) }}" class="grid gap-3">
                            @csrf
                            @method('PATCH')
                            <input name="catatan" placeholder="Catatan" class="h-10 rounded border-slate-300 text-sm">
                            <div class="flex flex-wrap gap-2">
                                @foreach ($record->dokumen as $dokumen)
                                    <a href="{{ asset('storage/'.$dokumen->path_file) }}" target="_blank" class="h-9 rounded bg-blue-50 px-3 py-2 text-xs font-bold text-[#2379d7]">{{ $dokumen->jenis_dokumen }}</a>
                                @endforeach
                                @if ($role === App\Models\User::ROLE_KEPALA_KECAMATAN)
                                    <button name="status" value="disetujui" class="h-9 rounded bg-emerald-600 px-3 text-xs font-bold text-white hover:bg-emerald-700 focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-emerald-700">Setujui</button>
                                    <button name="status" value="ditolak" class="h-9 rounded bg-red-600 px-3 text-xs font-bold text-white hover:bg-red-700 focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-red-700">Tolak</button>
                                @else
                                    <button name="status" value="diverifikasi" class="h-9 rounded bg-[#2379d7] px-3 text-xs font-bold text-white">Verifikasi Berkas</button>
                                @endif
                            </div>
                        </form>
                    </article>
                @empty
                    <p class="px-5 py-10 text-center font-semibold text-slate-500">Belum ada data.</p>
                @endforelse
            </div>
        @endif

        <div class="{{ $type === 'verifikasi-sktm' ? 'hidden md:block' : 'overflow-x-auto' }}">
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
                                        <details class="text-left">
                                            <summary class="cursor-pointer rounded bg-blue-50 px-3 py-2 text-xs font-extrabold text-[#2379d7]">Detail</summary>
                                            <div class="mt-3 min-w-[360px] rounded-lg border border-slate-200 bg-white p-4 text-sm font-semibold text-slate-600 shadow-lg">
                                                <p>Nomor Surat: {{ $record->nomor_surat }}</p>
                                                <p>Tanggal Surat: {{ $record->tanggal_surat?->format('d/m/Y') }}</p>
                                                <p>Keterangan: {{ $record->keterangan ?: '-' }}</p>
                                            </div>
                                        </details>
                                        <details class="text-left">
                                            <summary class="cursor-pointer rounded bg-blue-50 px-3 py-2 text-xs font-extrabold text-[#2379d7]">Edit</summary>
                                            <form method="POST" action="{{ route('dashboard.surat-masuk.update', $record) }}" class="mt-3 grid min-w-[520px] gap-2 rounded-lg border border-slate-200 bg-white p-4 shadow-lg">
                                                @csrf
                                                @method('PATCH')
                                                <input name="nomor_agenda" value="{{ $record->nomor_agenda }}" class="{{ $input }}" required>
                                                <input name="nomor_surat" value="{{ $record->nomor_surat }}" class="{{ $input }}" required>
                                                <input name="asal_surat" value="{{ $record->asal_surat }}" class="{{ $input }}" required>
                                                <input name="perihal" value="{{ $record->perihal }}" class="{{ $input }}" required>
                                                <input name="tanggal_surat" type="date" value="{{ $record->tanggal_surat?->format('Y-m-d') }}" class="{{ $input }}" required>
                                                <input name="tanggal_diterima" type="date" value="{{ $record->tanggal_diterima?->format('Y-m-d') }}" class="{{ $input }}" required>
                                                <select name="status" class="{{ $input }}" required>
                                                    @foreach ($statusOptions as $value => $label)
                                                        <option value="{{ $value }}" @selected($record->status === $value)>{{ $label }}</option>
                                                    @endforeach
                                                </select>
                                                <textarea name="isi_ringkas" class="{{ $input }} min-h-20">{{ $record->isi_ringkas }}</textarea>
                                                <button class="h-10 rounded bg-[#2379d7] px-4 text-sm font-extrabold text-white">Simpan Edit</button>
                                            </form>
                                        </details>
                                        <form method="POST" action="{{ route('dashboard.surat-masuk.reply', $record) }}">
                                            @csrf
                                            <button class="hover:text-[#2379d7]" title="Buat Balasan">
                                                <svg viewBox="0 0 24 24" class="h-5 w-5 fill-current"><path d="M4 4h11l5 5v11H4V4Zm10 1.5V10h4.5L14 5.5ZM7 13h8v-2H7v2Zm0 4h10v-2H7v2Z" /></svg>
                                            </button>
                                        </form>
                                        @if ($canDelete)
                                            <form method="POST" action="{{ route('dashboard.section.destroy', [$activeSection, $record->id]) }}" onsubmit="return confirm('Hapus data ini?')">
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
                                    'surat-keluar' => str_replace('SKTM', 'Surat Keterangan Tidak Mampu', $record->nomor_surat),
                                    'disposisi-surat' => $record->nomor_disposisi,
                                    'permohonan-sktm', 'verifikasi-sktm' => str_replace('SKTM', 'Surat Keterangan Tidak Mampu', $record->nomor_pengajuan),
                                    'dokumen-saya' => $record->jenis_dokumen,
                                    'penerbitan-sktm' => str_replace('SKTM', 'Surat Keterangan Tidak Mampu', $record->nomor_surat),
                                    'arsip-surat' => $record->judul_dokumen,
                                    default => '-',
                                };
                                $detail = match ($type) {
                                    'users' => ($record->username ?: '-').' - '.$record->email,
                                    'masyarakat' => $record->nik.' - '.$record->desa,
                                    'surat-keluar' => str_replace('SKTM', 'Surat Keterangan Tidak Mampu', $record->tujuan_surat.' - '.$record->perihal.(optional($record->suratMasuk)->nomor_surat ? ' - Ref: '.$record->suratMasuk->nomor_surat : '')),
                                    'disposisi-surat' => optional($record->suratMasuk)->perihal.' - '.$record->tujuan_disposisi,
                                    'permohonan-sktm', 'verifikasi-sktm' => $record->nama_pemohon.' - '.$record->keperluan,
                                    'dokumen-saya' => $record->nama_file,
                                    'penerbitan-sktm' => optional($record->permohonanSktm)->nama_pemohon.' - '.$record->tanggal_terbit?->format('d/m/Y').' - Berlaku: '.($record->masa_berlaku?->format('d/m/Y') ?: '-'),
                                    'arsip-surat' => str_replace('SKTM', 'Surat Keterangan Tidak Mampu', $record->jenis_arsip.' - '.$record->nomor_dokumen),
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
                                            <form method="POST" action="{{ route('dashboard.permohonan.verify', $record) }}" class="flex flex-wrap gap-2">
                                                @csrf
                                                @method('PATCH')
                                                <input name="catatan" placeholder="Catatan" class="h-9 w-28 rounded border-slate-300 text-xs">
                                                @foreach ($record->dokumen as $dokumen)
                                                    <a href="{{ asset('storage/'.$dokumen->path_file) }}" target="_blank" class="h-9 rounded bg-blue-50 px-3 py-2 text-xs font-bold text-[#2379d7]">{{ $dokumen->jenis_dokumen }}</a>
                                                @endforeach
                                                @if ($role === App\Models\User::ROLE_KEPALA_KECAMATAN)
                                                    <button name="status" value="disetujui" class="h-9 rounded bg-emerald-600 px-3 text-xs font-bold text-white hover:bg-emerald-700 focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-emerald-700">Setujui</button>
                                                    <button name="status" value="ditolak" class="h-9 rounded bg-red-600 px-3 text-xs font-bold text-white hover:bg-red-700 focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-red-700">Tolak</button>
                                                @else
                                                    <button name="status" value="diverifikasi" class="h-9 rounded bg-[#2379d7] px-3 text-xs font-bold text-white">Verifikasi Berkas</button>
                                                @endif
                                            </form>
                                        @else
                                            <details class="text-left">
                                                <summary class="cursor-pointer rounded bg-blue-50 px-3 py-2 text-xs font-extrabold text-[#2379d7]">Detail</summary>
                                                <div class="mt-3 min-w-[360px] rounded-lg border border-slate-200 bg-white p-4 text-sm font-semibold text-slate-600 shadow-lg">
                                                    <p>{{ $title }}</p>
                                                    <p>{{ $detail }}</p>
                                                    <p>Status: {{ Str::title(str_replace('_', ' ', $status)) }}</p>
                                                </div>
                                            </details>

                                            @if ($type === 'masyarakat')
                                                <details class="text-left">
                                                    <summary class="cursor-pointer rounded bg-blue-50 px-3 py-2 text-xs font-extrabold text-[#2379d7]">Edit</summary>
                                                    <form method="POST" action="{{ route('dashboard.masyarakat.update', $record) }}" class="mt-3 grid min-w-[520px] gap-2 rounded-lg border border-slate-200 bg-white p-4 shadow-lg">
                                                        @csrf
                                                        @method('PATCH')
                                                        <input name="nik" value="{{ $record->nik }}" class="{{ $input }}" required>
                                                        <input name="nama_lengkap" value="{{ $record->nama_lengkap }}" class="{{ $input }}" required>
                                                        <input name="tempat_lahir" value="{{ $record->tempat_lahir }}" class="{{ $input }}">
                                                        <input name="tanggal_lahir" type="date" value="{{ $record->tanggal_lahir?->format('Y-m-d') }}" class="{{ $input }}">
                                                        <input name="desa" value="{{ $record->desa }}" class="{{ $input }}">
                                                        <input name="kecamatan" value="{{ $record->kecamatan }}" class="{{ $input }}">
                                                        <input name="no_hp" value="{{ $record->no_hp }}" class="{{ $input }}">
                                                        <textarea name="alamat" class="{{ $input }} min-h-20">{{ $record->alamat }}</textarea>
                                                        <button class="h-10 rounded bg-[#2379d7] px-4 text-sm font-extrabold text-white">Simpan Edit</button>
                                                    </form>
                                                </details>
                                            @endif

                                            @if ($type === 'permohonan-sktm')
                                                <details class="text-left">
                                                    <summary class="cursor-pointer rounded bg-blue-50 px-3 py-2 text-xs font-extrabold text-[#2379d7]">Edit</summary>
                                                    <form method="POST" action="{{ route('dashboard.permohonan.update', $record) }}" class="mt-3 grid min-w-[520px] gap-2 rounded-lg border border-slate-200 bg-white p-4 shadow-lg">
                                                        @csrf
                                                        @method('PATCH')
                                                        <textarea name="keperluan" class="{{ $input }} min-h-20" required>{{ $record->keperluan }}</textarea>
                                                        <textarea name="catatan" class="{{ $input }} min-h-20">{{ $record->catatan }}</textarea>
                                                        @if ($role !== App\Models\User::ROLE_MASYARAKAT)
                                                            <select name="status" class="{{ $input }}">
                                                                @foreach ($statusOptions as $value => $label)
                                                                    <option value="{{ $value }}" @selected($record->status === $value)>{{ $label }}</option>
                                                                @endforeach
                                                            </select>
                                                        @endif
                                                        <button class="h-10 rounded bg-[#2379d7] px-4 text-sm font-extrabold text-white">Simpan Edit</button>
                                                    </form>
                                                </details>
                                            @endif

                                            @if ($type === 'surat-keluar')
                                                <details class="text-left">
                                                    <summary class="cursor-pointer rounded bg-blue-50 px-3 py-2 text-xs font-extrabold text-[#2379d7]">Edit</summary>
                                                    <form method="POST" action="{{ route('dashboard.surat-keluar.update', $record) }}" class="mt-3 grid min-w-[520px] gap-2 rounded-lg border border-slate-200 bg-white p-4 shadow-lg">
                                                        @csrf
                                                        @method('PATCH')
                                                        <input name="nomor_agenda" value="{{ $record->nomor_agenda }}" class="{{ $input }}" required>
                                                        <input name="nomor_surat" value="{{ $record->nomor_surat }}" class="{{ $input }}" required>
                                                        <input name="tujuan_surat" value="{{ $record->tujuan_surat }}" class="{{ $input }}" required>
                                                        <input name="perihal" value="{{ $record->perihal }}" class="{{ $input }}" required>
                                                        <input name="tanggal_surat" type="date" value="{{ $record->tanggal_surat?->format('Y-m-d') }}" class="{{ $input }}" required>
                                                        <select name="status" class="{{ $input }}" required>
                                                            @foreach (['draft' => 'Draft', 'diterbitkan' => 'Diterbitkan', 'dikirim' => 'Dikirim', 'diarsipkan' => 'Diarsipkan'] as $value => $label)
                                                                <option value="{{ $value }}" @selected($record->status === $value)>{{ $label }}</option>
                                                            @endforeach
                                                        </select>
                                                        <textarea name="isi_ringkas" class="{{ $input }} min-h-20">{{ $record->isi_ringkas }}</textarea>
                                                        <button class="h-10 rounded bg-[#2379d7] px-4 text-sm font-extrabold text-white">Simpan Edit</button>
                                                    </form>
                                                </details>
                                            @endif

                                            @if ($type === 'dokumen-saya' && $record->path_file)
                                                <a href="{{ Str::startsWith($record->path_file, ['http://', 'https://']) ? $record->path_file : asset('storage/'.$record->path_file) }}" target="_blank" class="rounded bg-blue-50 px-3 py-2 text-xs font-extrabold text-[#2379d7]">File</a>
                                            @endif

                                            @if ($type === 'arsip-surat' && $record->file_dokumen)
                                                <a href="{{ Str::startsWith($record->file_dokumen, ['http://', 'https://']) ? $record->file_dokumen : asset('storage/'.$record->file_dokumen) }}" target="_blank" class="rounded bg-blue-50 px-3 py-2 text-xs font-extrabold text-[#2379d7]">File</a>
                                            @endif

                                            @if ($type === 'penerbitan-sktm')
                                                <a href="{{ route('dashboard.penerbitan-sktm.print', $record) }}" target="_blank" class="rounded bg-blue-50 px-3 py-2 text-xs font-extrabold text-[#2379d7]">Cetak</a>
                                                <a href="{{ route('dashboard.penerbitan-sktm.download', $record) }}" class="rounded bg-blue-50 px-3 py-2 text-xs font-extrabold text-[#2379d7]">PDF</a>
                                            @endif

                                            @if ($canDelete)
                                            <form method="POST" action="{{ route('dashboard.section.destroy', [$activeSection, $record->id]) }}" onsubmit="return confirm('Hapus data ini?')">
                                                @csrf
                                                @method('DELETE')
                                                <button class="hover:text-red-600" title="Hapus">
                                                    <svg viewBox="0 0 24 24" class="h-5 w-5 fill-current"><path d="M7 21a2 2 0 0 1-2-2V7h14v12a2 2 0 0 1-2 2H7ZM9 4h6l1 2H8l1-2Z" /></svg>
                                                </button>
                                            </form>
                                            @endif
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
    @endif
</section>
