@php
    $user = Auth::user();
    $role = $user?->role ?? App\Models\User::ROLE_MASYARAKAT;
    $roleLabel = $user?->roleLabel() ?? 'Masyarakat';
    $activeSection = $section ?? 'dashboard';

    $date = now();
    $monthNames = [
        1 => 'Januari',
        2 => 'Februari',
        3 => 'Maret',
        4 => 'April',
        5 => 'Mei',
        6 => 'Juni',
        7 => 'Juli',
        8 => 'Agustus',
        9 => 'September',
        10 => 'Oktober',
        11 => 'November',
        12 => 'Desember',
    ];
    $displayDate = $date->format('d').' '.$monthNames[(int) $date->format('n')].' '.$date->format('Y');

    $menus = [
        'admin' => [
            ['id' => 'dashboard', 'label' => 'Dashboard', 'icon' => 'home'],
            ['id' => 'data-masyarakat', 'label' => 'Data Masyarakat', 'icon' => 'id'],
            ['label' => 'Inventori Surat', 'icon' => 'mail', 'children' => [
                ['id' => 'surat-masuk', 'label' => 'Surat Masuk'],
                ['id' => 'surat-keluar', 'label' => 'Surat Keluar'],
                ['id' => 'disposisi-surat', 'label' => 'Disposisi Surat'],
                ]],
                ['label' => 'Pelayanan SKTM', 'icon' => 'file', 'children' => [
                    ['id' => 'permohonan-sktm', 'label' => 'Permohonan SKTM'],
                    ['id' => 'verifikasi-sktm', 'label' => 'Verifikasi'],
                    ['id' => 'penerbitan-sktm', 'label' => 'Penerbitan SKTM'],
                    ]],
                    ['id' => 'arsip-surat', 'label' => 'Arsip & Dokumen', 'icon' => 'archive'],
                    ['id' => 'data-pengguna', 'label' => 'Manajemen User', 'icon' => 'users'],
                    ['id' => 'laporan', 'label' => 'Laporan', 'icon' => 'report'],
                    ],
                    'petugas' => [
                        ['id' => 'dashboard', 'label' => 'Dashboard', 'icon' => 'home'],
                        ['id' => 'data-masyarakat', 'label' => 'Data Masyarakat', 'icon' => 'id'],
                        ['label' => 'Inventori Surat', 'icon' => 'mail', 'children' => [
                            ['id' => 'surat-masuk', 'label' => 'Surat Masuk'],
                            ['id' => 'surat-keluar', 'label' => 'Surat Keluar'],
                            ['id' => 'disposisi-surat', 'label' => 'Disposisi Surat'],
                            ]],
                            ['label' => 'Pelayanan SKTM', 'icon' => 'file', 'children' => [
                                ['id' => 'permohonan-sktm', 'label' => 'Permohonan SKTM'],
                                ['id' => 'verifikasi-sktm', 'label' => 'Verifikasi'],
                                ['id' => 'penerbitan-sktm', 'label' => 'Cetak SKTM'],
                                ]],
                                ['id' => 'arsip-surat', 'label' => 'Arsip Surat', 'icon' => 'archive'],
                                ['id' => 'laporan', 'label' => 'Laporan', 'icon' => 'report'],
                                ],
                                'masyarakat' => [
                                    ['id' => 'dashboard', 'label' => 'Dashboard', 'icon' => 'home'],
                                    ['id' => 'profil-saya', 'label' => 'Profil Saya', 'icon' => 'id'],
                                    ['label' => 'Pelayanan SKTM', 'icon' => 'file', 'children' => [
                                        ['id' => 'ajukan-sktm', 'label' => 'Ajukan SKTM'],
                                        ['id' => 'status-pengajuan', 'label' => 'Status Pengajuan'],
                                        ['id' => 'dokumen-saya', 'label' => 'Dokumen Saya'],
                                        ['id' => 'sktm-terbit', 'label' => 'SKTM Terbit'],
                                        ]],
        ],
    ];

    $dbStats = $statsFromDb ?? [];
    $stats = [
        'admin' => [
            ['label' => 'Total Pengguna', 'value' => $dbStats['total_pengguna'] ?? 0, 'unit' => 'Akun', 'color' => 'bg-[#247bd6]', 'icon' => 'people'],
            ['label' => 'Data Masyarakat', 'value' => $dbStats['total_masyarakat'] ?? 0, 'unit' => 'Data', 'color' => 'bg-[#3fbd66]', 'icon' => 'people'],
            ['label' => 'Surat Masuk', 'value' => $dbStats['surat_masuk'] ?? 0, 'unit' => 'Surat', 'color' => 'bg-[#247bd6]', 'icon' => 'inbox'],
            ['label' => 'Permohonan SKTM', 'value' => $dbStats['permohonan_sktm'] ?? 0, 'unit' => 'Permohonan', 'color' => 'bg-[#8d4be8]', 'icon' => 'people'],
            ['label' => 'SKTM Diterbitkan', 'value' => $dbStats['sktm_diterbitkan'] ?? 0, 'unit' => 'Surat', 'color' => 'bg-[#2bb8c4]', 'icon' => 'document'],
        ],
        'petugas' => [
            ['label' => 'Surat Masuk', 'value' => $dbStats['surat_masuk'] ?? 0, 'unit' => 'Surat', 'color' => 'bg-[#247bd6]', 'icon' => 'inbox'],
            ['label' => 'Surat Keluar', 'value' => $dbStats['surat_keluar'] ?? 0, 'unit' => 'Surat', 'color' => 'bg-[#3fbd66]', 'icon' => 'outbox'],
            ['label' => 'Disposisi Surat', 'value' => $dbStats['disposisi_surat'] ?? 0, 'unit' => 'Surat', 'color' => 'bg-[#ffad19]', 'icon' => 'clipboard'],
            ['label' => 'Verifikasi SKTM', 'value' => $dbStats['sktm_menunggu'] ?? 0, 'unit' => 'Permohonan', 'color' => 'bg-[#ff7a21]', 'icon' => 'search'],
        ],
        'masyarakat' => [
            ['label' => 'Permohonan Saya', 'value' => $dbStats['permohonan_sktm'] ?? 0, 'unit' => 'Permohonan', 'color' => 'bg-[#8d4be8]', 'icon' => 'people'],
            ['label' => 'Dokumen Saya', 'value' => $dbStats['dokumen_sktm'] ?? 0, 'unit' => 'Dokumen', 'color' => 'bg-[#247bd6]', 'icon' => 'document'],
            ['label' => 'Status Menunggu', 'value' => $dbStats['sktm_menunggu'] ?? 0, 'unit' => 'Permohonan', 'color' => 'bg-[#ffad19]', 'icon' => 'clipboard'],
            ['label' => 'SKTM Terbit', 'value' => $dbStats['sktm_diterbitkan'] ?? 0, 'unit' => 'Surat', 'color' => 'bg-[#3fbd66]', 'icon' => 'outbox'],
        ],
    ];

    $inventorySummary = [
        ['label' => 'Surat Masuk', 'value' => $dbStats['surat_masuk'] ?? 0, 'change' => '+0', 'color' => 'bg-[#247bd6]', 'icon' => 'inbox'],
        ['label' => 'Surat Keluar', 'value' => $dbStats['surat_keluar'] ?? 0, 'change' => '+0', 'color' => 'bg-[#3fbd66]', 'icon' => 'outbox'],
        ['label' => 'Disposisi Surat', 'value' => $dbStats['disposisi_surat'] ?? 0, 'change' => '+0', 'color' => 'bg-[#ffad19]', 'icon' => 'clipboard'],
    ];

    $sktmSummary = [
        ['label' => 'Permohonan Masuk', 'value' => $dbStats['permohonan_sktm'] ?? 0, 'change' => '+0', 'color' => 'bg-[#8d4be8]', 'icon' => 'people'],
        ['label' => 'Proses Verifikasi', 'value' => $dbStats['sktm_menunggu'] ?? 0, 'change' => '+0', 'color' => 'bg-[#ff7a21]', 'icon' => 'search'],
        ['label' => 'SKTM Diterbitkan', 'value' => $dbStats['sktm_diterbitkan'] ?? 0, 'change' => '+0', 'color' => 'bg-[#3fbd66]', 'icon' => 'document'],
    ];

    $sectionTitles = [
        'dashboard' => 'Dashboard',
        'data-masyarakat' => 'Data Masyarakat',
        'surat-masuk' => 'Surat Masuk',
        'surat-keluar' => 'Surat Keluar',
        'disposisi-surat' => 'Disposisi Surat',
        'arsip-surat' => 'Arsip & Dokumen',
        'permohonan-sktm' => 'Permohonan SKTM',
        'verifikasi-sktm' => 'Verifikasi SKTM',
        'penerbitan-sktm' => 'Penerbitan SKTM',
        'profil-saya' => 'Profil Saya',
        'ajukan-sktm' => 'Ajukan SKTM',
        'status-pengajuan' => 'Status Pengajuan',
        'dokumen-saya' => 'Dokumen Saya',
        'sktm-terbit' => 'SKTM Terbit',
        'data-pengguna' => 'Manajemen User',
        'laporan' => 'Laporan',
    ];

    $currentTitle = $sectionTitles[$activeSection] ?? 'Dashboard';
    $roleMenus = $menus[$role] ?? $menus['masyarakat'];
    $roleStats = $stats[$role] ?? $stats['masyarakat'];
@endphp

<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ $currentTitle }} - Sistem Informasi SKTM</title>

    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600,700,800&display=swap" rel="stylesheet" />
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-white font-sans text-slate-900 antialiased">
    <div class="min-h-screen p-3">
        <div class="mx-auto flex min-h-[calc(100vh-24px)] max-w-[1860px] overflow-hidden border border-slate-200 bg-white shadow-[0_1px_18px_rgba(15,23,42,0.12)]">
            <aside class="hidden w-[330px] shrink-0 border-r border-slate-200 bg-white px-7 py-8 lg:block">
                <div class="flex flex-col items-center text-center">
                    <div class="h-[116px] w-[98px]">
                        <svg viewBox="0 0 132 154" class="h-full w-full drop-shadow-[0_2px_2px_rgba(15,23,42,0.3)]" aria-label="Lambang Kabupaten Kutai Kartanegara">
                            <path d="M66 3 118 18v68c0 31-21 50-52 65-31-15-52-34-52-65V18L66 3Z" fill="#057334" stroke="#173821" stroke-width="4" />
                            <path d="M66 13 108 25v60c0 24-16 39-42 53-26-14-42-29-42-53V25L66 13Z" fill="#0b8c3a" stroke="#f3d032" stroke-width="2" />
                            <path d="M33 36c20-10 46-10 66 0" fill="none" stroke="#f7d347" stroke-width="9" stroke-linecap="round" />
                            <text x="66" y="38" text-anchor="middle" font-size="8" font-weight="800" fill="#133d20">KUTAI KARTANEGARA</text>
                            <circle cx="66" cy="74" r="25" fill="#fff8d7" stroke="#e2bd25" stroke-width="4" />
                            <path d="M24 70c20 4 63 4 84 0M30 80c18 3 53 3 72 0" stroke="#dbeafe" stroke-width="3" />
                            <path d="M29 108c18 8 55 8 74 0" fill="none" stroke="#f7d347" stroke-width="8" stroke-linecap="round" />
                            <text x="66" y="112" text-anchor="middle" font-size="7" font-weight="800" fill="#12351d">BENA BENUA ETAM</text>
                            <path d="m66 7 3 8 8 .2-6.4 4.7 2.3 7.8L66 23l-6.9 4.7 2.3-7.8-6.4-4.7 8-.2 3-8Z" fill="#f8d632" />
                        </svg>
                    </div>
                    <h1 class="mt-5 text-[15px] font-extrabold uppercase leading-[1.55] tracking-[0] text-[#17283d]">
                        Sistem Informasi<br>
                        Inventori Surat dan<br>
                        Pelayanan SKTM<br>
                        Administrasi Berbasis Web
                    </h1>
                    <p class="mt-3 text-[15px] font-semibold text-slate-700">Kecamatan Marangkayu</p>
                </div>

                <nav class="mt-8 space-y-2">
                    @foreach ($roleMenus as $menu)
                        @php
                            $hasChildren = isset($menu['children']);
                            $childIds = $hasChildren ? collect($menu['children'])->pluck('id')->all() : [];
                            $isActive = ($menu['id'] ?? null) === $activeSection || in_array($activeSection, $childIds, true);
                            $href = isset($menu['id']) ? (($menu['id'] === 'dashboard') ? route('dashboard') : route('dashboard.section', $menu['id'])) : '#';
                        @endphp

                        <div @if ($hasChildren) x-data="{ open: {{ $isActive ? 'true' : 'false' }} }" @endif>
                            @if ($hasChildren)
                                <button type="button" @click="open = ! open" class="{{ $isActive ? 'text-[#2379d7] bg-blue-50' : 'text-slate-700 hover:bg-slate-50' }} flex h-[46px] w-full items-center rounded-md px-4 text-left text-[15px] font-bold transition" :aria-expanded="open.toString()">
                            @else
                                <a href="{{ $href }}" class="{{ $isActive ? 'bg-[#2379d7] text-white shadow-[0_4px_9px_rgba(35,121,215,0.28)]' : 'text-slate-700 hover:bg-slate-50' }} flex h-[46px] items-center rounded-md px-4 text-[15px] font-bold transition">
                            @endif
                                <span class="{{ $isActive && ! $hasChildren ? 'text-white' : 'text-slate-500' }} mr-4 flex h-6 w-6 items-center justify-center">
                                    <svg viewBox="0 0 24 24" class="h-5 w-5 fill-current" aria-hidden="true">
                                        @if (($menu['icon'] ?? '') === 'home')
                                            <path d="M3 11.5 12 4l9 7.5V21h-6v-6H9v6H3v-9.5Z" />
                                        @elseif (($menu['icon'] ?? '') === 'users')
                                            <path d="M8 11a4 4 0 1 0 0-8 4 4 0 0 0 0 8Zm8 1a3.5 3.5 0 1 0 0-7 3.5 3.5 0 0 0 0 7ZM8 13c-3.3 0-6 1.7-6 3.8V20h12v-3.2C14 14.7 11.3 13 8 13Zm8 1c-.7 0-1.3.1-1.9.3 1.2.8 1.9 1.9 1.9 3.1V20h6v-2.7c0-1.8-2.7-3.3-6-3.3Z" />
                                        @elseif (($menu['icon'] ?? '') === 'id')
                                            <path d="M4 4h16a2 2 0 0 1 2 2v12a2 2 0 0 1-2 2H4a2 2 0 0 1-2-2V6a2 2 0 0 1 2-2Zm3 10h6v-1c0-1.4-1.8-2.5-3-2.5S7 11.6 7 13v1Zm9-5h3V7h-3v2Zm0 4h3v-2h-3v2Zm0 4h3v-2h-3v2Zm-6-7.5A2.5 2.5 0 1 0 10 4a2.5 2.5 0 0 0 0 5.5Z" />
                                        @elseif (($menu['icon'] ?? '') === 'archive')
                                            <path d="M3 4h18v5H3V4Zm2 7h14v9H5v-9Zm4 2v2h6v-2H9Z" />
                                        @elseif (($menu['icon'] ?? '') === 'report')
                                            <path d="M5 3h10l4 4v14H5V3Zm9 1.5V8h3.5L14 4.5ZM8 11v2h8v-2H8Zm0 4v2h8v-2H8Z" />
                                        @else
                                            <path d="M4 4h16v16H4V4Zm3 4v2h10V8H7Zm0 4v2h10v-2H7Z" />
                                        @endif
                                    </svg>
                                </span>
                                <span class="flex-1">{{ $menu['label'] }}</span>
                                @if ($hasChildren)
                                    <svg viewBox="0 0 20 20" class="h-4 w-4 fill-current text-slate-400 transition-transform" :class="{ 'rotate-180': open }" aria-hidden="true">
                                        <path d="M5.3 7.3a1 1 0 0 1 1.4 0L10 10.6l3.3-3.3a1 1 0 1 1 1.4 1.4l-4 4a1 1 0 0 1-1.4 0l-4-4a1 1 0 0 1 0-1.4Z" />
                                    </svg>
                                @endif
                            @if ($hasChildren)
                                </button>
                            @else
                                </a>
                            @endif

                            @if ($hasChildren)
                                <div x-show="open" class="ml-[30px] mt-1 border-l border-slate-200 py-1 pl-8">
                                    @foreach ($menu['children'] as $child)
                                        <a href="{{ route('dashboard.section', $child['id']) }}" class="{{ $activeSection === $child['id'] ? 'text-[#2379d7]' : 'text-slate-500 hover:text-slate-800' }} block rounded px-2 py-2 text-[14px] font-semibold">
                                            {{ $child['label'] }}
                                        </a>
                                    @endforeach
                                </div>
                            @endif
                        </div>
                    @endforeach
                </nav>
            </aside>

            <div class="min-w-0 flex-1 bg-[#fbfdff]">
                <header class="flex h-[88px] items-center justify-between border-b border-slate-200 bg-white px-5 sm:px-8">
                    <div class="flex items-center gap-6">
                        <button type="button" class="flex h-10 w-10 items-center justify-center rounded-md text-slate-700 hover:bg-slate-100" aria-label="Buka menu">
                            <svg viewBox="0 0 24 24" class="h-7 w-7 fill-current" aria-hidden="true">
                                <path d="M4 6h16v2H4V6Zm0 5h16v2H4v-2Zm0 5h16v2H4v-2Z" />
                            </svg>
                        </button>
                        <h2 class="text-[24px] font-extrabold text-[#1b2b3f]">{{ $currentTitle }}</h2>
                    </div>

                    <div class="flex items-center gap-6">
                        <div class="hidden items-center gap-3 text-[14px] font-semibold text-slate-600 sm:flex">
                            <svg viewBox="0 0 24 24" class="h-6 w-6 fill-slate-500" aria-hidden="true">
                                <path d="M7 2h2v2h6V2h2v2h3a2 2 0 0 1 2 2v14a2 2 0 0 1-2 2H4a2 2 0 0 1-2-2V6a2 2 0 0 1 2-2h3V2Zm13 8H4v10h16V10Z" />
                            </svg>
                            {{ $displayDate }}
                        </div>

                        <div class="flex items-center gap-3">
                            <div class="flex h-11 w-11 items-center justify-center rounded-full bg-[#16283d] text-white">
                                <svg viewBox="0 0 24 24" class="h-7 w-7 fill-current" aria-hidden="true">
                                    <path d="M12 12a5 5 0 1 0-5-5 5 5 0 0 0 5 5Zm0 2c-4.42 0-8 2.02-8 4.5V20h16v-1.5c0-2.48-3.58-4.5-8-4.5Z" />
                                </svg>
                            </div>
                            <div class="hidden sm:block">
                                <p class="text-[15px] font-bold text-slate-800">{{ $roleLabel }}</p>
                                <p class="text-[12px] font-semibold text-slate-500">{{ $user?->name }}</p>
                            </div>
                            <form method="POST" action="{{ route('logout') }}">
                                @csrf
                                <button type="submit" class="flex h-9 w-9 items-center justify-center rounded-md text-slate-500 hover:bg-slate-100 hover:text-slate-800" aria-label="Logout">
                                    <svg viewBox="0 0 24 24" class="h-5 w-5 fill-none stroke-current stroke-2" aria-hidden="true">
                                        <path d="M15 3h4a2 2 0 0 1 2 2v14a2 2 0 0 1-2 2h-4" />
                                        <path d="m10 17 5-5-5-5" />
                                        <path d="M15 12H3" />
                                    </svg>
                                </button>
                            </form>
                        </div>
                    </div>
                </header>

                <main class="px-5 py-6 sm:px-8">
                    @if ($activeSection === 'dashboard')
                        <section class="overflow-hidden rounded-lg border border-[#dbe8f7] bg-[#f4f9ff] px-8 py-8">
                            <div class="grid items-center gap-8 lg:grid-cols-[1fr_430px]">
                                <div>
                                    <h3 class="text-[34px] font-extrabold leading-tight text-[#1b2b3f]">Selamat Datang, {{ $roleLabel }}!</h3>
                                    <p class="mt-5 max-w-[760px] text-[17px] font-semibold leading-8 text-slate-600">
                                        Anda masuk ke dalam Sistem Informasi Inventori Surat<br class="hidden xl:block">
                                        dan Pelayanan SKTM Administrasi Kecamatan Marangkayu.
                                    </p>
                                </div>

                                <div class="hidden justify-end lg:flex">
                                    <svg viewBox="0 0 430 190" class="h-[170px] w-[390px]" aria-hidden="true">
                                        <path d="M42 130c-24-40-4-78 38-64 10-35 54-52 84-21 35-30 91-15 101 31 44-9 73 20 61 54H42Z" fill="#dff4ed" />
                                        <path d="M318 125c22-28 38-56 52-101 25 51 4 89-52 101ZM329 132c39-16 57-34 72-72 11 49-17 76-72 72ZM75 130C51 102 35 74 21 29-4 80 19 119 75 130ZM62 137C23 121 5 103-10 65c-11 49 17 76 72 72Z" fill="#8ad8c8" />
                                        <rect x="92" y="14" width="248" height="133" rx="9" fill="#143451" />
                                        <rect x="105" y="28" width="222" height="104" rx="3" fill="#f8fbff" />
                                        <path d="M80 148h272l-12 18H93l-13-18Z" fill="#7e9eb9" />
                                        <path d="M116 166h202" stroke="#5f7f9c" stroke-width="6" stroke-linecap="round" />
                                        <path d="M158 45h75l34 34v43H158V45Z" fill="#ffffff" stroke="#e2e8f0" stroke-width="2" />
                                        <path d="M232 45v35h35" fill="#3182d8" />
                                        <path d="M181 70h35M181 86h50M181 102h58" stroke="#8aa0b7" stroke-width="4" stroke-linecap="round" />
                                        <path d="M292 72 340 91v16c0 28-18 44-48 55-30-11-48-27-48-55V91l48-19Z" fill="#42bf67" />
                                        <path d="m275 115 13 13 27-31" fill="none" stroke="#fff" stroke-width="10" stroke-linecap="round" stroke-linejoin="round" />
                                    </svg>
                                </div>
                            </div>
                        </section>

                        <section class="mt-7 grid gap-5 sm:grid-cols-2 {{ count($roleStats) >= 5 ? 'xl:grid-cols-5' : 'xl:grid-cols-4' }}">
                            @foreach ($roleStats as $stat)
                                <article class="overflow-hidden rounded-lg border border-slate-200 bg-white shadow-[0_1px_4px_rgba(15,23,42,0.06)]">
                                    <div class="flex gap-6 px-6 py-6">
                                        <div class="{{ $stat['color'] }} flex h-16 w-16 shrink-0 items-center justify-center rounded-xl text-white shadow-sm">
                                            <svg viewBox="0 0 24 24" class="h-9 w-9 fill-current" aria-hidden="true">
                                                @if ($stat['icon'] === 'people')
                                                    <path d="M8 11a4 4 0 1 0 0-8 4 4 0 0 0 0 8Zm8 1a3.5 3.5 0 1 0 0-7 3.5 3.5 0 0 0 0 7ZM8 13c-3.3 0-6 1.7-6 3.8V20h12v-3.2C14 14.7 11.3 13 8 13Zm8 1c-.7 0-1.3.1-1.9.3 1.2.8 1.9 1.9 1.9 3.1V20h6v-2.7c0-1.8-2.7-3.3-6-3.3Z" />
                                                @elseif ($stat['icon'] === 'clipboard')
                                                    <path d="M9 2h6l1 2h3v18H5V4h3l1-2Zm0 7h6V7H9v2Zm0 4h8v-2H9v2Zm0 4h8v-2H9v2Z" />
                                                @elseif ($stat['icon'] === 'document')
                                                    <path d="M5 3h10l4 4v14H5V3Zm9 1.5V8h3.5L14 4.5ZM8 11v2h8v-2H8Zm0 4v2h8v-2H8Z" />
                                                @elseif ($stat['icon'] === 'search')
                                                    <path d="M10.5 4a6.5 6.5 0 1 0 4 11.6l4 4 1.4-1.4-4-4A6.5 6.5 0 0 0 10.5 4Zm0 2a4.5 4.5 0 1 1 0 9 4.5 4.5 0 0 1 0-9Z" />
                                                @else
                                                    <path d="M4 6h4l2 2h10v10H4V6Zm3 6h10v-2H7v2Z" />
                                                @endif
                                            </svg>
                                        </div>
                                        <div class="min-w-0">
                                            <p class="truncate text-[15px] font-extrabold text-slate-700">{{ $stat['label'] }}</p>
                                            <p class="mt-2 text-[31px] font-extrabold leading-none text-[#1b2b3f]">{{ $stat['value'] }}</p>
                                            <p class="mt-2 text-[14px] font-semibold text-slate-500">{{ $stat['unit'] }}</p>
                                        </div>
                                    </div>
                                    @php
                                        $detailSection = match ($stat['label']) {
                                            'Surat Masuk' => 'surat-masuk',
                                            'Surat Keluar' => 'surat-keluar',
                                            'Disposisi Surat', 'Disposisi Diproses' => 'disposisi-surat',
                                            'Permohonan SKTM' => 'permohonan-sktm',
                                            'Permohonan Saya', 'Status Menunggu' => 'status-pengajuan',
                                            'Total Pengguna' => 'data-pengguna',
                                            'Data Masyarakat' => 'data-masyarakat',
                                            'Verifikasi SKTM' => 'verifikasi-sktm',
                                            'Cetak SKTM', 'SKTM Diterbitkan' => 'penerbitan-sktm',
                                            'SKTM Terbit' => 'sktm-terbit',
                                            'Dokumen Saya' => 'dokumen-saya',
                                            default => 'dashboard',
                                        };
                                    @endphp
                                    <a href="{{ $detailSection === 'dashboard' ? route('dashboard') : route('dashboard.section', $detailSection) }}" class="flex h-11 items-center justify-between border-t border-slate-100 px-6 text-[14px] font-extrabold text-[#2c73bb]">
                                        Lihat Detail
                                        <span class="text-xl leading-none">›</span>
                                    </a>
                                </article>
                            @endforeach
                        </section>

                        <section class="mt-7 grid gap-6 xl:grid-cols-2">
                            <article class="rounded-lg border border-slate-200 bg-white shadow-[0_1px_4px_rgba(15,23,42,0.05)]">
                                <h3 class="border-b border-slate-200 px-6 py-5 text-[20px] font-extrabold text-[#1b2b3f]">Ringkasan Inventori Surat</h3>
                                <div class="divide-y divide-slate-100 px-6">
                                    @foreach ($inventorySummary as $item)
                                        <div class="grid grid-cols-[auto_1fr_auto_auto_auto] items-center gap-5 py-4">
                                            <div class="{{ $item['color'] }} flex h-10 w-10 items-center justify-center rounded-lg text-white">
                                                <svg viewBox="0 0 24 24" class="h-6 w-6 fill-current" aria-hidden="true">
                                                    <path d="M4 6h4l2 2h10v10H4V6Zm3 6h10v-2H7v2Z" />
                                                </svg>
                                            </div>
                                            <p class="text-[15px] font-bold text-slate-700">{{ $item['label'] }}</p>
                                            <p class="text-[15px] font-bold text-slate-700">{{ $item['value'] }}</p>
                                            <span class="rounded bg-emerald-50 px-2 py-1 text-[13px] font-extrabold text-emerald-700">{{ $item['change'] }}</span>
                                            <p class="text-[14px] font-semibold text-slate-500">dari bulan lalu</p>
                                        </div>
                                    @endforeach
                                </div>
                            </article>

                            <article class="rounded-lg border border-slate-200 bg-white shadow-[0_1px_4px_rgba(15,23,42,0.05)]">
                                <h3 class="border-b border-slate-200 px-6 py-5 text-[20px] font-extrabold text-[#1b2b3f]">Ringkasan Pelayanan SKTM</h3>
                                <div class="divide-y divide-slate-100 px-6">
                                    @foreach ($sktmSummary as $item)
                                        <div class="grid grid-cols-[auto_1fr_auto_auto_auto] items-center gap-5 py-4">
                                            <div class="{{ $item['color'] }} flex h-10 w-10 items-center justify-center rounded-lg text-white">
                                                <svg viewBox="0 0 24 24" class="h-6 w-6 fill-current" aria-hidden="true">
                                                    <path d="M8 11a4 4 0 1 0 0-8 4 4 0 0 0 0 8Zm8 1a3.5 3.5 0 1 0 0-7 3.5 3.5 0 0 0 0 7ZM8 13c-3.3 0-6 1.7-6 3.8V20h12v-3.2C14 14.7 11.3 13 8 13Zm8 1c-.7 0-1.3.1-1.9.3 1.2.8 1.9 1.9 1.9 3.1V20h6v-2.7c0-1.8-2.7-3.3-6-3.3Z" />
                                                </svg>
                                            </div>
                                            <p class="text-[15px] font-bold text-slate-700">{{ $item['label'] }}</p>
                                            <p class="text-[15px] font-bold text-slate-700">{{ $item['value'] }}</p>
                                            <span class="rounded bg-emerald-50 px-2 py-1 text-[13px] font-extrabold text-emerald-700">{{ $item['change'] }}</span>
                                            <p class="text-[14px] font-semibold text-slate-500">dari bulan lalu</p>
                                        </div>
                                    @endforeach
                                </div>
                            </article>
                        </section>
                    @endif

                    @if ($activeSection !== 'dashboard')
                        @include('dashboard.module', ['module' => $module ?? ['type' => 'dashboard', 'records' => collect()]])
                    @endif
                </main>
            </div>
        </div>
    </div>
</body>
</html>
