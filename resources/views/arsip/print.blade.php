<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Daftar Arsip</title>
    <style>
        @media print {
            @page {
                size: A4;
                margin: 10mm;
            }
            body {
                font-family: Arial, sans-serif;
                font-size: 12px;
            }
            .no-print {
                display: none !important;
            }
        }
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 20px;
        }
        .header {
            text-align: center;
            margin-bottom: 20px;
            border-bottom: 2px solid #000;
            padding-bottom: 10px;
        }
        .header h1 {
            margin: 0;
            font-size: 18px;
            font-weight: bold;
        }
        .header p {
            margin: 5px 0 0 0;
            font-size: 12px;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 10px;
        }
        th, td {
            border: 1px solid #000;
            padding: 8px;
            text-align: left;
            font-size: 11px;
        }
        th {
            background-color: #f0f0f0;
            font-weight: bold;
            text-align: center;
        }
        .text-center {
            text-align: center;
        }
        .text-right {
            text-align: right;
        }
        .badge {
            padding: 2px 6px;
            border-radius: 3px;
            font-size: 10px;
            font-weight: bold;
        }
        .badge-success {
            background-color: #d4edda;
            color: #155724;
        }
        .badge-secondary {
            background-color: #e2e3e5;
            color: #383d41;
        }
        .badge-warning {
            background-color: #fff3cd;
            color: #856404;
        }
        .badge-danger {
            background-color: #f8d7da;
            color: #721c24;
        }
        .footer {
            margin-top: 20px;
            text-align: right;
            font-size: 11px;
        }
    </style>
</head>
<body>
    <div class="header">
        <h1>DAFTAR ARSIP</h1>
        <p>Dicetak pada: {{ date('d-m-Y H:i') }}</p>
    </div>

    <table>
        <thead>
            <tr>
                <th style="width: 5%;">#</th>
                <th style="width: 12%;">Nomor Surat</th>
                <th style="width: 8%;">Tanggal</th>
                <th style="width: 20%;">Nama Arsip</th>
                <th style="width: 12%;">Jenis</th>
                <th style="width: 15%;">Lokasi</th>
                <th style="width: 8%;">Status</th>
                <th style="width: 10%;">Ketersediaan</th>
                <th style="width: 10%;">Status Retensi</th>
            </tr>
        </thead>
        <tbody>
            @forelse($archives as $key => $archive)
            <tr>
                <td class="text-center">{{ $key + 1 }}</td>
                <td>{{ $archive->nomor_surat ?? '—' }}</td>
                <td>{{ $archive->tanggal_arsip ? \Carbon\Carbon::parse($archive->tanggal_arsip)->format('d-m-Y') : '—' }}</td>
                <td>
                    <strong>{{ $archive->nama_arsip }}</strong>
                    @if($archive->perihal_surat)
                    <br><small>{{ $archive->perihal_surat }}</small>
                    @endif
                </td>
                <td>{{ $archive->nama_jenis ?? '—' }}</td>
                <td>
                    @php
                        $locationParts = [];
                        if ($archive->ruangan) $locationParts[] = $archive->ruangan;
                        if ($archive->lemari_nama) $locationParts[] = $archive->lemari_nama;
                        if ($archive->rak_nama) $locationParts[] = $archive->rak_nama;
                        $locationDisplay = implode(' → ', $locationParts);
                    @endphp
                    {{ $locationDisplay ?: '—' }}
                </td>
                <td>
                    <span class="badge {{ $archive->status == 'Aktif' ? 'badge-success' : 'badge-secondary' }}">
                        {{ $archive->status }}
                    </span>
                </td>
                <td>
                    <span class="badge {{ ($archive->status_ketersediaan ?? 'Tersedia') == 'Tersedia' ? 'badge-success' : 'badge-danger' }}">
                        {{ $archive->status_ketersediaan ?? 'Tersedia' }}
                    </span>
                </td>
                <td>
                    @php
                        $statusRetensi = $archive->status_retensi ?? 'Belum Memasuki Masa Retensi';
                        $badgeClass = 'badge-secondary';
                        if ($statusRetensi == 'Proses Retensi') $badgeClass = 'badge-warning';
                        elseif ($statusRetensi == 'Sudah Retensi') $badgeClass = 'badge-success';
                    @endphp
                    <span class="badge {{ $badgeClass }}">{{ $statusRetensi }}</span>
                </td>
            </tr>
            @empty
            <tr>
                <td colspan="9" class="text-center">Data tidak ditemukan</td>
            </tr>
            @endforelse
        </tbody>
    </table>

    <div class="footer">
        <p>Total: {{ $archives->count() }} arsip</p>
    </div>

    <div class="no-print" style="margin-top: 30px; text-align: center;">
        <button onclick="window.print()" style="padding: 10px 20px; font-size: 14px; cursor: pointer;">
            <i class="fas fa-print"></i> Cetak
        </button>
        <button onclick="window.close()" style="padding: 10px 20px; font-size: 14px; cursor: pointer; margin-left: 10px;">
            Tutup
        </button>
    </div>
</body>
</html>
