<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Laporan Absensi Harian - {{ $kelas->nama_kelas ?? 'Kelas' }}</title>
    <style>
        body { font-family: sans-serif; font-size: 11pt; color: #333; }
        .header { text-align: center; margin-bottom: 20px; border-bottom: 2px solid #333; padding-bottom: 10px; }
        .header h2 { margin: 0; font-size: 16pt; text-transform: uppercase; }
        .header p { margin: 3px 0 0; font-size: 10pt; color: #666; }
        .meta-info { margin-bottom: 15px; width: 100%; }
        .meta-info td { padding: 3px 0; font-size: 10pt; }
        table.data { width: 100%; border-collapse: collapse; margin-top: 10px; }
        table.data th, table.data td { border: 1px solid #999; padding: 6px 8px; text-align: center; font-size: 9pt; }
        table.data th { bg-color: #f2f2f2; background-color: #f2f2f2; font-weight: bold; }
        table.data td.text-left { text-align: left; }
        .ttd-container { margin-top: 30px; float: right; width: 220px; text-align: center; font-size: 10pt; }
        .ttd-space { height: 60px; }
    </style>
</head>
<body>

    <div class="header">
        <h2>LAPORAN REKAPITULASI ABSENSI HARIAN SISWA</h2>
        <p>SISTEM INFORMASI AKADEMIK (SIAKAD)</p>
    </div>

    <table class="meta-info">
        <tr>
            <td width="15%"><strong>Kelas</strong></td>
            <td width="35%">: {{ $kelas->nama_kelas ?? '-' }}</td>
            <td width="15%"><strong>Bulan / Tahun</strong></td>
            <td width="35%">: {{ DateTime::createFromFormat('!m', $bulan)->format('F') }} {{ $tahun }}</td>
        </tr>
        <tr>
            <td><strong>Wali Kelas</strong></td>
            <td>: {{ $guru->nama_guru ?? '-' }}</td>
            <td><strong>Dicetak Pada</strong></td>
            <td>: {{ date('d-m-Y H:i') }}</td>
        </tr>
    </table>

    <table class="data">
        <thead>
            <tr>
                <th width="5%">No</th>
                <th width="15%">NISN</th>
                <th width="35%">Nama Siswa</th>
                <th width="10%">Hadir</th>
                <th width="10%">Izin</th>
                <th width="10%">Sakit</th>
                <th width="10%">Alpa</th>
            </tr>
        </thead>
        <tbody>
            @forelse ($siswas as $index => $siswa)
                @php
                    $absensiSiswa = $rekap->get($siswa->id) ?? collect();
                    $hadir = $absensiSiswa->where('status', 'Hadir')->count();
                    $izin  = $absensiSiswa->where('status', 'Izin')->count();
                    $sakit = $absensiSiswa->where('status', 'Sakit')->count();
                    $alpa  = $absensiSiswa->where('status', 'Alpa')->count();
                @endphp
                <tr>
                    <td>{{ $index + 1 }}</td>
                    <td>{{ $siswa->nisn }}</td>
                    <td class="text-left">{{ $siswa->nama_siswa }}</td>
                    <td>{{ $hadir }}</td>
                    <td>{{ $izin }}</td>
                    <td>{{ $sakit }}</td>
                    <td>{{ $alpa }}</td>
                </tr>
            @empty
                <tr>
                    <td colspan="7">Tidak ada data siswa.</td>
                </tr>
            @endforelse
        </tbody>
    </table>

    <div class="ttd-container">
        <p>Mengetahui,<br>Wali Kelas {{ $kelas->nama_kelas ?? '' }}</p>
        <div class="ttd-space"></div>
        <p><strong><u>{{ $guru->nama_guru ?? '........................' }}</u></strong><br>NIP. {{ $guru->nip ?? '-' }}</p>
    </div>

</body>
</html>