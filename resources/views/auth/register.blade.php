<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>Register - Sistem Informasi Inventorisasi Surat dan Pelayanan SKTM</title>

    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600,700,800&display=swap" rel="stylesheet" />
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="min-h-screen bg-white font-sans text-slate-900 antialiased">
    <main class="flex min-h-screen items-center justify-center px-4 py-6">
        <section class="relative flex min-h-[760px] w-full max-w-[1440px] overflow-hidden rounded-xl border border-slate-200 bg-[#f4f8ff] shadow-[0_2px_16px_rgba(15,23,42,0.14)]">
            <div class="pointer-events-none absolute inset-0 bg-[radial-gradient(circle_at_16%_8%,rgba(255,255,255,0.95),transparent_28%),linear-gradient(90deg,rgba(237,244,255,0.92),rgba(250,252,255,0.94))]"></div>

            <div class="relative hidden min-h-full w-[48%] flex-col items-center px-12 pb-0 pt-20 text-center lg:flex">
                <div class="relative z-10 flex max-w-[560px] flex-1 flex-col items-center">
                    <div class="mb-7 h-[150px] w-[128px]">
                        <svg viewBox="0 0 132 154" class="h-full w-full drop-shadow-[0_2px_2px_rgba(15,23,42,0.35)]" aria-label="Lambang Kabupaten Kutai Kartanegara">
                            <path d="M66 3 118 18v68c0 31-21 50-52 65-31-15-52-34-52-65V18L66 3Z" fill="#057334" stroke="#173821" stroke-width="4" />
                            <path d="M66 13 108 25v60c0 24-16 39-42 53-26-14-42-29-42-53V25L66 13Z" fill="#0b8c3a" stroke="#f3d032" stroke-width="2" />
                            <path d="M33 36c20-10 46-10 66 0" fill="none" stroke="#f7d347" stroke-width="9" stroke-linecap="round" />
                            <text x="66" y="38" text-anchor="middle" font-size="8" font-weight="800" fill="#133d20">KUTAI KARTANEGARA</text>
                            <circle cx="66" cy="74" r="25" fill="#fff8d7" stroke="#e2bd25" stroke-width="4" />
                            <path d="M43 84c9-11 22-26 47-18" fill="none" stroke="#ffffff" stroke-width="3" />
                            <path d="M24 70c20 4 63 4 84 0M30 80c18 3 53 3 72 0" stroke="#dbeafe" stroke-width="3" />
                            <path d="M66 45v-18" stroke="#f8fafc" stroke-width="5" stroke-linecap="round" />
                            <path d="M50 93h32M56 99h20" stroke="#f7d347" stroke-width="4" stroke-linecap="round" />
                            <path d="M29 108c18 8 55 8 74 0" fill="none" stroke="#f7d347" stroke-width="8" stroke-linecap="round" />
                            <text x="66" y="112" text-anchor="middle" font-size="7" font-weight="800" fill="#12351d">BENA BENUA ETAM</text>
                            <path d="m66 7 3 8 8 .2-6.4 4.7 2.3 7.8L66 23l-6.9 4.7 2.3-7.8-6.4-4.7 8-.2 3-8Z" fill="#f8d632" />
                        </svg>
                    </div>

                    <h1 class="max-w-[620px] text-[30px] font-extrabold uppercase leading-[1.35] tracking-[0] text-[#16283d]">
                        Sistem Informasi<br>
                        Inventorisasi Surat dan<br>
                        Pelayanan SKTM<br>
                        Administrasi Berbasis Web
                    </h1>
                    <p class="mt-4 text-[23px] font-medium text-slate-800">Kecamatan Marangkayu</p>
                    <div class="mt-4 h-[2px] w-[108px] bg-[#3979bd]"></div>
                    <p class="mt-6 max-w-[520px] text-[16px] font-medium leading-8 text-slate-700">
                        Sistem informasi untuk pengelolaan inventorisasi surat<br>
                        dan pelayanan Surat Keterangan Tidak Mampu (SKTM)<br>
                        secara terintegrasi, transparan dan efisien.
                    </p>
                </div>

                <div class="relative h-[300px] w-full max-w-[650px] opacity-70">
                    <div class="absolute bottom-0 left-0 right-0 h-16 rounded-[50%] bg-[#b8d9b0] blur-sm"></div>
                    <svg viewBox="0 0 680 300" class="absolute inset-x-0 bottom-0 h-full w-full" aria-hidden="true">
                        <path d="M64 234c18-29 48-32 69-8 24-43 83-32 97 12 46-39 108-19 127 26H55c-12 0-14-12 9-30Z" fill="#ffffff" opacity=".72" />
                        <path d="M450 229c17-34 55-38 78-9 17-17 48-11 60 14 21-13 50 1 56 30H438c-10 0-14-13 12-35Z" fill="#ffffff" opacity=".7" />
                        <path d="M180 128 340 44l160 84H180Z" fill="#ccd9ef" stroke="#a5b8d6" stroke-width="3" />
                        <path d="M126 138h428v119H126z" fill="#e8eef9" stroke="#b6c5de" stroke-width="3" />
                        <path d="M210 128h260v129H210z" fill="#f6f8fd" stroke="#b6c5de" stroke-width="3" />
                        <path d="M246 105h188l38 28H208l38-28Z" fill="#b4c4e3" stroke="#8fa6ca" stroke-width="3" />
                        <path d="M259 148h162v50H259z" fill="#ffffff" stroke="#c4cee0" stroke-width="3" />
                        <text x="340" y="169" text-anchor="middle" font-size="16" font-weight="800" fill="#53647b">KANTOR CAMAT</text>
                        <text x="340" y="188" text-anchor="middle" font-size="15" font-weight="800" fill="#53647b">MARANGKAYU</text>
                        <path d="M294 205h92v52h-92z" fill="#90a7ce" />
                        <path d="M304 216h32v41h-32zM344 216h32v41h-32z" fill="#7f98c1" />
                        <path d="M150 158h44v39h-44zM150 212h44v34h-44zM486 158h44v39h-44zM486 212h44v34h-44zM224 212h44v34h-44zM412 212h44v34h-44z" fill="#fff" stroke="#c0cbe0" stroke-width="3" />
                        <path d="M58 258h565" stroke="#9dc78e" stroke-width="12" stroke-linecap="round" />
                        <path d="M246 257 340 214l94 43H246Z" fill="#d7e2f4" opacity=".75" />
                        <g fill="#a9cf91">
                            <path d="M54 251c-17-45 6-56 6-56s24 28 3 57Z" />
                            <path d="M24 254c-8-42 18-52 18-52s17 32-9 53Z" />
                            <path d="M615 252c-17-45 6-56 6-56s24 28 3 57Z" />
                            <path d="M584 254c-8-42 18-52 18-52s17 32-9 53Z" />
                        </g>
                        <g fill="#92bf78">
                            <circle cx="98" cy="244" r="22" /><circle cx="124" cy="248" r="18" /><circle cx="546" cy="246" r="21" /><circle cx="512" cy="250" r="16" />
                        </g>
                    </svg>
                </div>
            </div>

            <div class="relative flex min-h-full flex-1 flex-col items-center justify-center px-5 py-8 lg:px-14">
                <div class="w-full max-w-[580px] rounded-xl bg-white px-10 py-10 shadow-[0_8px_22px_rgba(15,23,42,0.12)] sm:px-14 lg:px-[52px]">
                    <div class="flex flex-col items-center text-center">
                        <div class="flex h-[102px] w-[102px] items-center justify-center rounded-full bg-[#e7f0ff]">
                            <svg viewBox="0 0 80 80" class="h-[58px] w-[58px]" aria-hidden="true">
                                <circle cx="36" cy="25" r="12" fill="#1f78d1" />
                                <path d="M14 63c3-15 13-23 22-23s19 8 22 23H14Z" fill="#1f78d1" />
                                <path d="M58 28v20M48 38h20" stroke="#1f78d1" stroke-width="8" stroke-linecap="round" />
                            </svg>
                        </div>
                        <h2 class="mt-5 text-[30px] font-extrabold leading-tight text-[#16283d]">Register</h2>
                        <p class="mt-3 text-[17px] font-medium text-slate-600">Silakan daftar untuk mengakses sistem</p>
                    </div>

                    <form method="POST" action="{{ route('register') }}" class="mt-8 space-y-5">
                        @csrf

                        <div>
                            <label for="name" class="block text-[16px] font-bold text-slate-800">Nama</label>
                            <div class="mt-2 flex h-[56px] items-center rounded-md border border-slate-300 bg-white px-5 shadow-[inset_0_1px_2px_rgba(15,23,42,0.04)] transition focus-within:border-blue-500 focus-within:ring-2 focus-within:ring-blue-100">
                                <svg viewBox="0 0 24 24" class="mr-5 h-6 w-6 flex-none fill-slate-500" aria-hidden="true">
                                    <path d="M12 12a5 5 0 1 0-5-5 5 5 0 0 0 5 5Zm0 2c-4.42 0-8 2.02-8 4.5V20h16v-1.5c0-2.48-3.58-4.5-8-4.5Z" />
                                </svg>
                                <input id="name" name="name" type="text" value="{{ old('name') }}" required autofocus autocomplete="name" placeholder="Masukkan nama Anda" class="h-full w-full border-0 bg-transparent p-0 text-[16px] font-medium text-slate-800 placeholder:text-slate-500 focus:ring-0">
                            </div>
                            <x-input-error :messages="$errors->get('name')" class="mt-2" />
                        </div>

                        <div>
                            <label for="email" class="block text-[16px] font-bold text-slate-800">Email</label>
                            <div class="mt-2 flex h-[56px] items-center rounded-md border border-slate-300 bg-white px-5 shadow-[inset_0_1px_2px_rgba(15,23,42,0.04)] transition focus-within:border-blue-500 focus-within:ring-2 focus-within:ring-blue-100">
                                <svg viewBox="0 0 24 24" class="mr-5 h-6 w-6 flex-none fill-slate-500" aria-hidden="true">
                                    <path d="M20 4H4a2 2 0 0 0-2 2v12a2 2 0 0 0 2 2h16a2 2 0 0 0 2-2V6a2 2 0 0 0-2-2Zm-.4 4.25-7.07 5.3a.9.9 0 0 1-1.06 0L4.4 8.25V6.5l7.6 5.7 7.6-5.7v1.75Z" />
                                </svg>
                                <input id="email" name="email" type="email" value="{{ old('email') }}" required autocomplete="username" placeholder="Masukkan email Anda" class="h-full w-full border-0 bg-transparent p-0 text-[16px] font-medium text-slate-800 placeholder:text-slate-500 focus:ring-0">
                            </div>
                            <x-input-error :messages="$errors->get('email')" class="mt-2" />
                        </div>

                        <div>
                            <label for="username" class="block text-[16px] font-bold text-slate-800">Username</label>
                            <div class="mt-2 flex h-[56px] items-center rounded-md border border-slate-300 bg-white px-5 shadow-[inset_0_1px_2px_rgba(15,23,42,0.04)] transition focus-within:border-blue-500 focus-within:ring-2 focus-within:ring-blue-100">
                                <svg viewBox="0 0 24 24" class="mr-5 h-6 w-6 flex-none fill-slate-500" aria-hidden="true">
                                    <path d="M12 12a5 5 0 1 0-5-5 5 5 0 0 0 5 5Zm0 2c-4.42 0-8 2.02-8 4.5V20h16v-1.5c0-2.48-3.58-4.5-8-4.5Z" />
                                </svg>
                                <input id="username" name="username" type="text" value="{{ old('username') }}" autocomplete="username" placeholder="Masukkan username Anda" class="h-full w-full border-0 bg-transparent p-0 text-[16px] font-medium text-slate-800 placeholder:text-slate-500 focus:ring-0">
                            </div>
                            <x-input-error :messages="$errors->get('username')" class="mt-2" />
                        </div>

                        <div x-data="{ showPassword: false }">
                            <label for="password" class="block text-[16px] font-bold text-slate-800">Password</label>
                            <div class="mt-2 flex h-[56px] items-center rounded-md border border-slate-300 bg-white px-5 shadow-[inset_0_1px_2px_rgba(15,23,42,0.04)] transition focus-within:border-blue-500 focus-within:ring-2 focus-within:ring-blue-100">
                                <svg viewBox="0 0 24 24" class="mr-5 h-6 w-6 flex-none fill-slate-500" aria-hidden="true">
                                    <path d="M17 9V7A5 5 0 0 0 7 7v2H6a2 2 0 0 0-2 2v9a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2v-9a2 2 0 0 0-2-2h-1ZM9 7a3 3 0 0 1 6 0v2H9V7Zm4 10.73V19h-2v-1.27a2 2 0 1 1 2 0Z" />
                                </svg>
                                <input id="password" name="password" :type="showPassword ? 'text' : 'password'" required autocomplete="new-password" placeholder="Masukkan password Anda" class="h-full w-full border-0 bg-transparent p-0 text-[16px] font-medium text-slate-800 placeholder:text-slate-500 focus:ring-0">
                                <button type="button" class="ml-4 flex h-8 w-8 flex-none items-center justify-center rounded text-slate-500 hover:text-slate-700 focus:outline-none focus:ring-2 focus:ring-blue-500" @click="showPassword = ! showPassword" aria-label="Tampilkan password">
                                    <svg viewBox="0 0 24 24" class="h-6 w-6 fill-none stroke-current stroke-[2.2]" aria-hidden="true">
                                        <path d="M2.5 12s3.5-6 9.5-6 9.5 6 9.5 6-3.5 6-9.5 6-9.5-6-9.5-6Z" />
                                        <circle cx="12" cy="12" r="3" />
                                    </svg>
                                </button>
                            </div>
                            <x-input-error :messages="$errors->get('password')" class="mt-2" />
                        </div>

                        <div x-data="{ showPassword: false }">
                            <label for="password_confirmation" class="block text-[16px] font-bold text-slate-800">Konfirmasi Password</label>
                            <div class="mt-2 flex h-[56px] items-center rounded-md border border-slate-300 bg-white px-5 shadow-[inset_0_1px_2px_rgba(15,23,42,0.04)] transition focus-within:border-blue-500 focus-within:ring-2 focus-within:ring-blue-100">
                                <svg viewBox="0 0 24 24" class="mr-5 h-6 w-6 flex-none fill-slate-500" aria-hidden="true">
                                    <path d="M17 9V7A5 5 0 0 0 7 7v2H6a2 2 0 0 0-2 2v9a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2v-9a2 2 0 0 0-2-2h-1ZM9 7a3 3 0 0 1 6 0v2H9V7Zm4 10.73V19h-2v-1.27a2 2 0 1 1 2 0Z" />
                                </svg>
                                <input id="password_confirmation" name="password_confirmation" :type="showPassword ? 'text' : 'password'" required autocomplete="new-password" placeholder="Ulangi password Anda" class="h-full w-full border-0 bg-transparent p-0 text-[16px] font-medium text-slate-800 placeholder:text-slate-500 focus:ring-0">
                                <button type="button" class="ml-4 flex h-8 w-8 flex-none items-center justify-center rounded text-slate-500 hover:text-slate-700 focus:outline-none focus:ring-2 focus:ring-blue-500" @click="showPassword = ! showPassword" aria-label="Tampilkan konfirmasi password">
                                    <svg viewBox="0 0 24 24" class="h-6 w-6 fill-none stroke-current stroke-[2.2]" aria-hidden="true">
                                        <path d="M2.5 12s3.5-6 9.5-6 9.5 6 9.5 6-3.5 6-9.5 6-9.5-6-9.5-6Z" />
                                        <circle cx="12" cy="12" r="3" />
                                    </svg>
                                </button>
                            </div>
                            <x-input-error :messages="$errors->get('password_confirmation')" class="mt-2" />
                        </div>

                        <button type="submit" class="flex h-[58px] w-full items-center justify-center rounded-md bg-[#1f78d1] text-[19px] font-bold text-white shadow-[0_2px_4px_rgba(29,78,216,0.25)] transition hover:bg-[#1668bb] focus:outline-none focus:ring-4 focus:ring-blue-200">
                            <svg viewBox="0 0 24 24" class="mr-4 h-7 w-7 fill-none stroke-current stroke-[2.4]" aria-hidden="true">
                                <path d="M15 3h4a2 2 0 0 1 2 2v14a2 2 0 0 1-2 2h-4" />
                                <path d="m10 17 5-5-5-5" />
                                <path d="M15 12H3" />
                            </svg>
                            Daftar
                        </button>

                        <div class="flex items-center gap-6 pt-3">
                            <div class="h-px flex-1 bg-slate-200"></div>
                            <span class="text-[16px] font-medium text-slate-700">atau</span>
                            <div class="h-px flex-1 bg-slate-200"></div>
                        </div>

                        <p class="text-center text-[15px] font-medium text-slate-600">
                            Sudah punya akun?
                            <a href="{{ route('login') }}" class="font-bold text-[#2d6bb4] hover:text-blue-800 focus:outline-none focus:ring-2 focus:ring-blue-500">
                                Masuk
                            </a>
                        </p>
                    </form>
                </div>

                <p class="mt-8 text-center text-[13px] font-medium text-slate-500">
                    &copy; 2024 Kecamatan Marangkayu. All rights reserved.
                </p>
            </div>
        </section>
    </main>
</body>
</html>
