<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Rekapitulasi Absensi Siswa</title>
    <style>
        body {
            font-family: Arial, Helvetica, sans-serif;
            font-size: 12px;
            color: #333;
            margin: 20px;
        }
        .header {
            text-align: center;
            margin-bottom: 20px;
            border-bottom: 2px solid #333;
            padding-bottom: 10px;
        }
        .header h2 {
            margin: 0;
            text-transform: uppercase;
        }
        .header p {
            margin: 5px 0 0 0;
            color: #666;
        }
        .info-table {
            width: 100%;
            margin-bottom: 15px;
        }
        .info-table td {
            padding: 3px 0;
        }
        .data-table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 10px;
        }
        .data-table th, .data-table td {
            border: 1px solid #999;
            padding: 8px;
            text-align: center;
        }
        .data-table th {
            background-color: #f2f2f2;
            font-weight: bold;
        }
        .text-left {
            text-align: left !important;
        }
        .no-print {
            margin-bottom: 20px;
        }
        @media print {
            .no-print {
                display: none;
            }
        }
        .btn-print {
            background-color: #4f46e5;
            color: white;
            padding: 8px 16px;
            border: none;
            border-radius: 6px;
            cursor: pointer;
            font-weight: bold;
        }
    </style>
</head>
<body>

    <!-- Tombol Cetak / Download PDF (Tampil di browser saja) -->
    <div class="no-print" style="text-align: right;">
        <button onclick="window.print()" class="btn-print">🖨️ Cetak / Simpan PDF</button>
    </div>

    <!-- Kop / Header Laporan -->
    <div class="header">
        <h2>REKAPITULASI PRESENSI SISWA</h2>
        <p>PERIODE: {{ \Carbon\Carbon::parse($request->tanggal_mulai)->translatedFormat('d F Y') }} s/d {{ \Carbon\Carbon::parse($request->tanggal_selesai)->translatedFormat('d F Y') }}</p>
    </div>

    <!-- Informasi Detail -->
    <table class="info-table">
        <tr>
            <td width="15%"><strong>Mata Pelajaran</strong></td>
            <td width="2%">:</td>
            <td>{{ $mapel->nama_mapel ?? $mapel->nama ?? '-' }}</td>
            <td width="15%"><strong>Kelas</strong></td>
            <td width="2%">:</td>
            <td>{{ $kelas->nama_kelas ?? $kelas->nama ?? '-' }}</td>
        </tr>
    </table>

    <!-- Tabel Data Rekapitulasi -->
    <table class="data-table">
        <thead>
            <tr>
                <th width="5%">No</th>
                <th width="15%">NISN</th>
                <th>Nama Lengkap Siswa</th>
                <th width="10%">Hadir</th>
                <th width="10%">Izin</th>
                <th width="10%">Sakit</th>
                <th width="10%">Alpa</th>
            </tr>
        </thead>
        <tbody>
            @forelse ($siswas as $index => $siswa)
                @php
                    // Mengambil object rekap berdasarkan ID siswa dari Array
                    $stat = $rekap[$siswa->id] ?? null;
                @endphp
                <tr>
                    <td>{{ $index + 1 }}</td>
                    <td>{{ $siswa->nisn }}</td>
                    <td class="text-left">{{ $siswa->nama_siswa }}</td>
                    <td>{{ $stat->total_hadir ?? 0 }}</td>
                    <td>{{ $stat->total_izin ?? 0 }}</td>
                    <td>{{ $stat->total_sakit ?? 0 }}</td>
                    <td>{{ $stat->total_alpa ?? 0 }}</td>
                </tr>
            @empty
                <tr>
                    <td colspan="7">Tidak ada data siswa ditemukan</td>
                </tr>
            @endforelse
        </tbody>
    </table>

</body>
</html>