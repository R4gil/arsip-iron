<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Daftar Arsip</title>
    <style>
        @page {
            size: A4 landscape;
            margin: 8mm;
        }
        body {
            font-family: 'Courier New', monospace;
            font-size: 9px;
            margin: 0;
            padding: 0;
        }
        .header {
            text-align: center;
            margin-bottom: 15px;
            border-bottom: 2px solid #000;
            padding-bottom: 8px;
        }
        .header h1 {
            margin: 0;
            font-size: 16px;
            font-weight: bold;
        }
        .header p {
            margin: 3px 0 0 0;
            font-size: 10px;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            page-break-inside: auto;
        }
        thead {
            display: table-header-group;
        }
        tbody {
            display: table-row-group;
        }
        tr {
            page-break-inside: avoid;
        }
        th, td {
            border: 1px solid #333;
            padding: 4px 5px;
            text-align: left;
            font-size: 8px;
            word-wrap: break-word;
        }
        th {
            background-color: #e0e0e0;
            font-weight: bold;
            text-align: center;
            font-size: 8px;
        }
        .text-center {
            text-align: center;
        }
        .fw-bold {
            font-weight: bold;
        }
        .footer {
            margin-top: 15px;
            text-align: right;
            font-size: 9px;
            border-top: 1px solid #333;
            padding-top: 5px;
        }
        .badge-status {
            padding: 1px 4px;
            font-size: 7px;
            font-weight: bold;
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
                <th style="width: 4%;">#</th>
                <th style="width: 13%;">Nomor Surat</th>
                <th style="width: 7%;">Tanggal</th>
                <th style="width: 18%;">Nama Arsip</th>
                <th style="width: 10%;">Jenis</th>
                <th style="width: 14%;">Lokasi</th>
                <th style="width: 7%;">Status</th>
                <th style="width: 9%;">Ketersediaan</th>
                <th style="width: 10%;">Masa Retensi</th>
                <th style="width: 8%;">Status Retensi</th>
            </tr>
        </thead>
        <tbody>
            @forelse($archives as $key => $archive)
            <tr>
                <td class="text-center">{{ $key + 1 }}</td>
                <td>{{ $archive->nomor_surat ?? '—' }}</td>
                <td class="text-center">{{ $archive->tanggal_arsip ? \Carbon\Carbon::parse($archive->tanggal_arsip)->format('d-m-Y') : '—' }}</td>
                <td>
                    <span class="fw-bold">{{ $archive->nama_arsip }}</span>
                    @if($archive->perihal_surat)
                    <br><span style="font-size: 7px; color: #555;">{{ $archive->perihal_surat }}</span>
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
                <td class="text-center">{{ $archive->status ?? '—' }}</td>
                <td class="text-center">{{ $archive->status_ketersediaan ?? 'Tersedia' }}</td>
                <td class="text-center">{{ $archive->masa_retensi ?? '—' }}</td>
                <td class="text-center">{{ $archive->status_retensi ?? 'Belum' }}</td>
            </tr>
            @empty
            <tr>
                <td colspan="10" class="text-center">Data tidak ditemukan</td>
            </tr>
            @endforelse
        </tbody>
    </table>

    <div class="footer">
        <strong>Total:</strong> {{ $archives->count() }} arsip
    </div>
</body>
</html>