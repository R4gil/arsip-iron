<?php

namespace App\Http\Controllers;

use App\Models\Cabinet;
use App\Models\Location;
use Illuminate\Http\Request;

class LemariController extends Controller
{
    public function index(Request $request)
    {
        $query = Cabinet::with('location')->withCount('racks');

        if ($request->filled('lokasi_id')) {
            $query->where('ruangarsip_id', $request->lokasi_id);
        }

        $cabinets = $query->orderBy('lemari_nama')->simplePaginate($request->get('per_page', 10))->withQueryString();
        $locations = Location::orderBy('ruangan')->get();

        return view('lemari.index', compact('cabinets', 'locations'));
    }

    public function create(Request $request)
    {
        $locations = Location::orderBy('ruangan')->get();
        $selectedLocation = $request->get('lokasi_id');

        return view('lemari.create', compact('locations', 'selectedLocation'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'ruangarsip_id' => 'required|exists:lokasi_simpan,id',
            'lemari_nama' => 'required|string|max:191',
            'lemari_keterangan' => 'nullable|string',
        ]);

        $cabinet = Cabinet::create([
            'ruangarsip_id' => $data['ruangarsip_id'],
            'lemari_nama' => $data['lemari_nama'],
            'lemari_keterangan' => $data['lemari_keterangan'] ?? null,
        ]);

        \App\Models\AktivitasLog::create([
            'user_id' => auth()->id(),
            'aktivitas' => 'Tambah Lemari',
            'detail' => "Menambahkan lemari baru: {$cabinet->lemari_nama}",
            'ip_address' => request()->ip(),
        ]);

        return redirect()->route('lemari.index', ['lokasi_id' => $data['ruangarsip_id']])
            ->with('success', 'Lemari berhasil ditambahkan.');
    }

    public function edit(Cabinet $cabinet)
    {
        $locations = Location::orderBy('ruangan')->get();

        return view('lemari.edit', compact('cabinet', 'locations'));
    }

    public function update(Request $request, Cabinet $cabinet)
    {
        $data = $request->validate([
            'ruangarsip_id' => 'required|exists:lokasi_simpan,id',
            'lemari_nama' => 'required|string|max:191',
            'lemari_keterangan' => 'nullable|string',
        ]);

        $cabinet->update([
            'ruangarsip_id' => $data['ruangarsip_id'],
            'lemari_nama' => $data['lemari_nama'],
            'lemari_keterangan' => $data['lemari_keterangan'] ?? null,
        ]);

        \App\Models\AktivitasLog::create([
            'user_id' => auth()->id(),
            'aktivitas' => 'Ubah Lemari',
            'detail' => "Mengubah lemari: {$cabinet->lemari_nama}",
            'ip_address' => request()->ip(),
        ]);

        return redirect()->route('lemari.index', ['lokasi_id' => $data['ruangarsip_id']])
            ->with('success', 'Lemari berhasil diperbarui.');
    }

    public function destroy(Cabinet $cabinet)
    {
        if ($cabinet->racks()->exists()) {
            return redirect()->route('lemari.index')->with('error', 'Lemari masih memiliki rak. Hapus rak terlebih dahulu.');
        }

        $lokasiId = $cabinet->ruangarsip_id;
        $cabinet->delete();

        \App\Models\AktivitasLog::create([
            'user_id' => auth()->id(),
            'aktivitas' => 'Hapus Lemari',
            'detail' => "Menghapus lemari: {$cabinet->lemari_nama}",
            'ip_address' => request()->ip(),
        ]);

        return redirect()->route('lemari.index', ['lokasi_id' => $lokasiId])
            ->with('success', 'Lemari berhasil dihapus.');
    }

    public function exportExcel(Request $request)
    {
        $query = Cabinet::with('location')->withCount('racks');

        if ($request->filled('lokasi_id')) {
            $query->where('ruangarsip_id', $request->lokasi_id);
        }

        $cabinets = $query->orderBy('lemari_nama')->get();

        $filename = 'lemari_arsip_' . date('Y-m-d_His') . '.csv';
        $handle = fopen('php://temp', 'r+');

        // Header
        fputcsv($handle, [
            'Lokasi',
            'Nama Lemari',
            'Jumlah Rak',
            'Keterangan'
        ]);

        // Data
        foreach ($cabinets as $cabinet) {
            fputcsv($handle, [
                $cabinet->location?->ruangan ?? '—',
                $cabinet->lemari_nama,
                $cabinet->racks_count,
                $cabinet->lemari_keterangan ?? '—'
            ]);
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
        return redirect()->route('lemari.index', $request->all());
    }
}