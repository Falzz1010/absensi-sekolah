<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>QR Code - {{ $qrCode->nama }}</title>
    <style>
        body {
            font-family: 'Inter', sans-serif;
            display: flex;
            justify-content: center;
            align-items: center;
            min-height: 100vh;
            margin: 0;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        }
        .container {
            background: white;
            padding: 3rem;
            border-radius: 1rem;
            box-shadow: 0 20px 60px rgba(0,0,0,0.3);
            text-align: center;
            max-width: 500px;
        }
        h1 {
            color: #333;
            margin-bottom: 0.5rem;
            font-size: 1.5rem;
        }
        .subtitle {
            color: #666;
            margin-bottom: 2rem;
            font-size: 0.9rem;
        }
        .qr-code {
            margin: 2rem 0;
            padding: 1rem;
            background: #f9fafb;
            border-radius: 0.5rem;
        }
        .info {
            margin-top: 2rem;
            padding: 1rem;
            background: #f3f4f6;
            border-radius: 0.5rem;
            text-align: left;
        }
        .info-row {
            display: flex;
            justify-content: space-between;
            padding: 0.5rem 0;
            border-bottom: 1px solid #e5e7eb;
        }
        .info-row:last-child {
            border-bottom: none;
        }
        .label {
            font-weight: 600;
            color: #374151;
        }
        .value {
            color: #6b7280;
        }
        .badge {
            display: inline-block;
            padding: 0.25rem 0.75rem;
            border-radius: 9999px;
            font-size: 0.75rem;
            font-weight: 600;
        }
        .badge-success {
            background: #d1fae5;
            color: #065f46;
        }
        .badge-info {
            background: #dbeafe;
            color: #1e40af;
        }
        .btn {
            display: inline-block;
            margin-top: 1.5rem;
            padding: 0.75rem 1.5rem;
            background: #667eea;
            color: white;
            text-decoration: none;
            border-radius: 0.5rem;
            font-weight: 600;
            transition: background 0.3s;
        }
        .btn:hover {
            background: #5568d3;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>{{ $qrCode->nama }}</h1>
        <p class="subtitle">Scan QR Code ini untuk absensi</p>
        
        <div class="qr-code">
            {!! $qrCodeImage !!}
        </div>

        <div class="info">
            <div class="info-row">
                <span class="label">Tipe:</span>
                <span class="value">
                    <span class="badge {{ $qrCode->tipe === 'global' ? 'badge-success' : 'badge-info' }}">
                        {{ $qrCode->tipe === 'global' ? 'Global' : 'Per Kelas' }}
                    </span>
                </span>
            </div>
            
            @if($qrCode->kelas)
            <div class="info-row">
                <span class="label">Kelas:</span>
                <span class="value">{{ $qrCode->kelas }}</span>
            </div>
            @endif
            
            <div class="info-row">
                <span class="label">Kode:</span>
                <span class="value" style="font-family: monospace; font-size: 0.75rem;">{{ $qrCode->code }}</span>
            </div>
            
            @if($qrCode->berlaku_dari)
            <div class="info-row">
                <span class="label">Berlaku:</span>
                <span class="value">{{ $qrCode->berlaku_dari->format('d M Y') }} - {{ $qrCode->berlaku_sampai ? $qrCode->berlaku_sampai->format('d M Y') : 'Selamanya' }}</span>
            </div>
            @endif
        </div>

        <a href="{{ route('qr.download', $qrCode) }}" class="btn">Download QR Code</a>
    </div>
</body>
</html>
