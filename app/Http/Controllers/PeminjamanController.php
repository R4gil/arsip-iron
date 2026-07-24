<?php

namespace App\Http\Controllers;
use App\Models\Archive;
use App\Models\Peminjaman;
use Illuminate\Http\Request;

class PeminjamanController extends Controller
{
    public function create(Request $request)
    {
        $arsipTersedia = Archive::where('status_ketersediaan', 'Tersedia')
            ->simplePaginate(10, ['*'], 'arsip_page')
            ->withQueryString();

        $peminjamanAktif = Peminjaman::with(['archive'])
            ->where('status_pinjam', 'Dipinjam')
            ->latest('tanggal_keluar')
            ->simplePaginate(10, ['*'], 'pinjam_page')
            ->withQueryString();

        $totalArsip = Archive::where('status_ketersediaan', 'Tersedia')->count();
        $totalPinjam = Peminjaman::where('status_pinjam', 'Dipinjam')->count();

        return view('peminjaman.tambah', compact('arsipTersedia', 'peminjamanAktif', 'totalArsip', 'totalPinjam'));
    }

    public function index(Request $request)
    {
        $query = Peminjaman::with(['archive.jenisArsip', 'archive.lokasi', 'archive.cabinet', 'archive.rack'])
            ->whereHas('archive'); // Hanya tampilkan peminjaman yang arsipnya masih ada

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('nama_peminjam', 'like', "%{$search}%")
                    ->orWhere('divisi_peminjam', 'like', "%{$search}%")
                    ->orWhereHas('archive', function ($q) use ($search) {
                        $q->where('nama_arsip', 'like', "%{$search}%")
                            ->orWhere('nomor_surat', 'like', "%{$search}%");
                    });
            });
        }

        if ($request->filled('status')) {
            $query->where('status_pinjam', $request->status);
        }

        $borrowings = $query->latest('tanggal_keluar')->simplePaginate($request->get('per_page', 10))->withQueryString();

        $stats = [
            'dipinjam' => Peminjaman::whereHas('archive')->where('status_pinjam', 'Dipinjam')->count(),
            'dikembalikan' => Peminjaman::whereHas('archive')->where('status_pinjam', 'Dikembalikan')->count(),
            'total' => Peminjaman::whereHas('archive')->count(),
        ];

        return view('peminjaman.daftar', compact('borrowings', 'stats'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'arsip_id' => 'required|exists:arsip,id',
            'nama_peminjam' => 'required|string|max:191',
            'divisi_peminjam' => 'nullable|string|max:191',
            'tanggal_keluar' => 'required|date',
            'keterangan_kondisi' => 'nullable|string',
        ]);

        $archive = Archive::findOrFail($data['arsip_id']);

        if ($archive->status_ketersediaan === 'Dipinjam' || Peminjaman::where('arsip_id', $archive->id)->where('status_pinjam', 'Dipinjam')->exists()) {
            return redirect()->back()->with('error', 'Arsip ini sedang dipinjam.');
        }

        Peminjaman::create([
            'arsip_id' => $archive->id,
            'nama_peminjam' => $data['nama_peminjam'],
            'divisi_peminjam' => $data['divisi_peminjam'] ?? null,
            'tanggal_keluar' => $data['tanggal_keluar'],
            'petugas_keluar_id' => auth()->id(),
            'status_pinjam' => 'Dipinjam',
            'keterangan_kondisi' => $data['keterangan_kondisi'] ?? null,
        ]);

        $archive->update(['status_ketersediaan' => 'Dipinjam']);

        \App\Models\AktivitasLog::create([
            'user_id' => auth()->id(),
            'aktivitas' => 'Pinjam Arsip',
            'detail' => "Meminjamkan arsip: {$archive->nomor_surat} - {$archive->nama_arsip} kepada {$data['nama_peminjam']}",
            'ip_address' => request()->ip(),
        ]);

        return redirect()->route('peminjaman.index')->with('success', 'Peminjaman arsip berhasil dicatat.');
    }

    public function kembalikan($id)
    {
        $peminjaman = Peminjaman::where('arsip_id', $id)
            ->where('status_pinjam', 'Dipinjam')
            ->latest()
            ->first();

        if (!$peminjaman) {
            $archive = Archive::find($id);
            if ($archive) {
                $archive->update(['status_ketersediaan' => 'Tersedia']);
            }

            return redirect()->back()->with('error', 'Tidak ada peminjaman aktif untuk arsip ini.');
        }

        $peminjaman->update([
            'status_pinjam' => 'Dikembalikan',
            'tanggal_masuk' => now()->toDateString(),
            'petugas_masuk_id' => auth()->id(),
        ]);

        $peminjaman->archive()->update(['status_ketersediaan' => 'Tersedia']);

        \App\Models\AktivitasLog::create([
            'user_id' => auth()->id(),
            'aktivitas' => 'Kembalikan Arsip',
            'detail' => "Mengembalikan arsip: {$peminjaman->archive->nomor_surat} - {$peminjaman->archive->nama_arsip}",
            'ip_address' => request()->ip(),
        ]);

        return redirect()->route('peminjaman.create')->with('success', 'Arsip berhasil dikembalikan.');
    }

    public function update(Request $request, Peminjaman $peminjaman)
    {
        return redirect()->route('peminjaman.index');
    }

    public function destroy(Peminjaman $peminjaman)
    {
        if ($peminjaman->status_pinjam === 'Dipinjam') {
            $peminjaman->archive()->update(['status_ketersediaan' => 'Tersedia']);
        }

        $peminjaman->delete();

        return redirect()->route('peminjaman.index')->with('success', 'Transaksi peminjaman berhasil dihapus.');
    }

    public function clearHistory()
    {
        $deletedCount = Peminjaman::where('status_pinjam', 'Dikembalikan')->count();
        
        Peminjaman::where('status_pinjam', 'Dikembalikan')->delete();

        \App\Models\AktivitasLog::create([
            'user_id' => auth()->id(),
            'aktivitas' => 'Clear Riwayat Peminjaman',
            'detail' => "Menghapus {$deletedCount} riwayat peminjaman yang sudah dikembalikan",
            'ip_address' => request()->ip(),
        ]);

        return redirect()->route('peminjaman.index')->with('success', "Berhasil menghapus {$deletedCount} riwayat peminjaman yang sudah dikembalikan.");
    }

    public function exportExcel(Request $request)
    {
        $query = Peminjaman::with(['archive.jenisArsip', 'archive.lokasi', 'archive.cabinet', 'archive.rack'])
            ->whereHas('archive');

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('nama_peminjam', 'like', "%{$search}%")
                    ->orWhere('divisi_peminjam', 'like', "%{$search}%")
                    ->orWhereHas('archive', function ($q) use ($search) {
                        $q->where('nama_arsip', 'like', "%{$search}%")
                            ->orWhere('nomor_surat', 'like', "%{$search}%");
                    });
            });
        }

        if ($request->filled('status')) {
            $query->where('status_pinjam', $request->status);
        }

        $borrowings = $query->latest('tanggal_keluar')->get();

        $filename = 'peminjaman_arsip_' . date('Y-m-d_His') . '.csv';
        $handle = fopen('php://temp', 'r+');

        // Header
        fputcsv($handle, [
            'Nomor Arsip',
            'Nama Arsip',
            'Kategori',
            'Lokasi',
            'Peminjam',
            'Divisi',
            'Tgl Pinjam',
            'Tgl Kembali',
            'Status'
        ]);

        // Data
        foreach ($borrowings as $borrowing) {
            if ($borrowing->archive) {
                $parts = [];
                if ($borrowing->archive->lokasi) $parts[] = $borrowing->archive->lokasi->ruangan;
                if ($borrowing->archive->cabinet) $parts[] = $borrowing->archive->cabinet->lemari_nama;
                if ($borrowing->archive->rack) $parts[] = $borrowing->archive->rack->rak_nama;
                
                fputcsv($handle, [
                    $borrowing->archive->nomor_surat ?? '—',
                    $borrowing->archive->nama_arsip ?? '—',
                    $borrowing->archive->jenisArsip->nama ?? '—',
                    $parts ? implode(' → ', $parts) : '—',
                    $borrowing->nama_peminjam,
                    $borrowing->divisi_peminjam ?? '—',
                    $borrowing->tanggal_keluar ? \Carbon\Carbon::parse($borrowing->tanggal_keluar)->format('d-m-Y') : '—',
                    $borrowing->tanggal_masuk ? \Carbon\Carbon::parse($borrowing->tanggal_masuk)->format('d-m-Y') : '—',
                    $borrowing->status_pinjam
                ]);
            }
        }

        rewind($handle);
        $content = stream_get_contents($handle);
        fclose($handle);

        return response($content)
            ->header('Content-Type', 'text/csv')
            ->header('Content-Disposition', 'attachment; filename="' . $filename . '"');
    }

    public function exportPDF(Request $request)
    {
        // For PDF export, redirect to index with print parameter
        return redirect()->route('peminjaman.index', $request->all());
    }
}
