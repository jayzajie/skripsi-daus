<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>SKTM {{ $penerbitan->nomor_surat }}</title>
    <style>
        body { font-family: Arial, sans-serif; color: #111827; margin: 40px; }
        .kop { text-align: center; border-bottom: 3px solid #111827; padding-bottom: 14px; margin-bottom: 28px; }
        .kop h1, .kop h2, .kop p { margin: 4px 0; }
        .judul { text-align: center; text-decoration: underline; font-weight: 700; margin-top: 24px; }
        table { width: 100%; border-collapse: collapse; margin: 18px 0; }
        td { padding: 5px 0; vertical-align: top; }
        td:first-child { width: 190px; }
        .ttd { width: 280px; margin-left: auto; text-align: center; margin-top: 48px; }
        .print { position: fixed; right: 24px; top: 24px; }
        @media print { .print { display: none; } body { margin: 24px; } }
    </style>
</head>
<body>
    <button class="print" onclick="window.print()">Cetak</button>

    <div class="kop">
        <h2>PEMERINTAH KABUPATEN KUTAI KARTANEGARA</h2>
        <h1>KECAMATAN MARANGKAYU</h1>
        <p>Jl. Poros Marangkayu, Kutai Kartanegara</p>
    </div>

    <p class="judul">SURAT KETERANGAN TIDAK MAMPU</p>
    <p style="text-align:center">Nomor: {{ $penerbitan->nomor_surat }}</p>

    <p>Yang bertanda tangan di bawah ini menerangkan bahwa:</p>

    <table>
        <tr><td>Nama</td><td>: {{ $penerbitan->permohonanSktm->nama_pemohon }}</td></tr>
        <tr><td>NIK</td><td>: {{ $penerbitan->permohonanSktm->nik }}</td></tr>
        <tr><td>Alamat</td><td>: {{ $penerbitan->permohonanSktm->alamat }}</td></tr>
        <tr><td>Keperluan</td><td>: {{ $penerbitan->permohonanSktm->keperluan }}</td></tr>
        <tr><td>Masa Berlaku</td><td>: {{ $penerbitan->masa_berlaku?->translatedFormat('d F Y') ?: '-' }}</td></tr>
    </table>

    <p>
        Berdasarkan data yang ada, nama tersebut benar merupakan warga Kecamatan Marangkayu
        dan surat keterangan ini dibuat untuk dipergunakan sebagaimana mestinya.
    </p>

    <div class="ttd">
        <p>Marangkayu, {{ $penerbitan->tanggal_terbit?->translatedFormat('d F Y') }}</p>
        <p>{{ $penerbitan->pejabat_penandatangan }}</p>
        <br><br><br>
        <p><strong>{{ $penerbitan->pejabat_penandatangan }}</strong></p>
    </div>
</body>
</html>
