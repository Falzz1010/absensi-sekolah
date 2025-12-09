<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Laporan Absensi</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            font-size: 12px;
        }
        .header {
            text-align: center;
            margin-bottom: 30px;
            border-bottom: 2px solid #000;
            padding-bottom: 10px;
        }
        .header h2 {
            margin: 5px 0;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }
        th, td {
            border: 1px solid #ddd;
            padding: 8px;
            text-align: left;
        }
        th {
            background-color: #f2f2f2;
            font-weight: bold;
        }
        .status-hadir { color: #10b981; font-weight: bold; }
        .status-sakit { color: #3b82f6; font-weight: bold; }
        .status-izin { color: #f59e0b; font-weight: bold; }
        .status-alfa { color: #ef4444; font-weight: bold; }
        .footer {
            margin-top: 30px;
            text-align: right;
        }
    </style>
</head>
<body>
    <div class="header">
        <h2>LAPORAN KEHADIRAN SISWA</h2>
        <p>Tanggal Cetak: {{ $tanggal }}</p>
    </div>

    <table>
        <thead>
            <tr>
                <th width="5%">No</th>
                <th width="30%">Nama Murid</th>
                <th width="15%">Kelas</th>
                <th width="20%">Tanggal</th>
                <th width="15%">Status</th>
            </tr>
        </thead>
        <tbody>
            @foreach($absensis as $index => $absensi)
            <tr>
                <td>{{ $index + 1 }}</td>
                <td>{{ $absensi->murid->name }}</td>
                <td>{{ $absensi->kelas }}</td>
                <td>{{ \Carbon\Carbon::parse($absensi->tanggal)->format('d/m/Y') }}</td>
                <td class="status-{{ strtolower($absensi->status) }}">{{ $absensi->status }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>

    <div class="footer">
        <p>Total Data: {{ count($absensis) }}</p>
        <br><br>
        <p>_____________________</p>
        <p>Kepala Sekolah</p>
    </div>
</body>
</html>
