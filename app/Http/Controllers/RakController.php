<?php

namespace App\Http\Controllers;

use App\Models\Cabinet;
use App\Models\Location;
use App\Models\Rack;
use Illuminate\Http\Request;

class RakController extends Controller
{
    public function index(Request $request)
    {
        $query = Rack::with(['cabinet.location']);

        if ($request->filled('lokasi_id')) {
            $query->whereHas('cabinet', fn ($q) => $q->where('ruangarsip_id', $request->lokasi_id));
        }

        if ($request->filled('lemari_id')) {
            $query->where('lemari_id', $request->lemari_id);
        }

        $racks = $query->orderBy('rak_nama')->simplePaginate($request->get('per_page', 10))->withQueryString();
        $locations = Location::orderBy('ruangan')->get();
        $cabinets = Cabinet::with('location')->orderBy('lemari_nama')->get();

        return view('rak.index', compact('racks', 'locations', 'cabinets'));
    }

    public function create(Request $request)
    {
        $locations = Location::orderBy('ruangan')->get();
        $cabinets = Cabinet::with('location')->orderBy('lemari_nama')->get();
        $selectedLocation = $request->get('lokasi_id');
        $selectedCabinet = $request->get('lemari_id');

        return view('rak.create', compact('locations', 'cabinets', 'selectedLocation', 'selectedCabinet'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'ruangarsip_id' => 'required|exists:lokasi_simpan,id',
            'lemari_id' => 'required|exists:lemari,lemari_id',
            'rak_nama' => 'required|string|max:191',
            'rak_keterangan' => 'nullable|string',
        ]);

        $cabinet = Cabinet::where('lemari_id', $data['lemari_id'])
            ->where('ruangarsip_id', $data['ruangarsip_id'])
            ->first();

        if (!$cabinet) {
            return back()->withErrors(['lemari_id' => 'Pilih lemari yang sesuai dengan lokasi.'])->withInput();
        }

        $rack = Rack::create([
            'lemari_id' => $data['lemari_id'],
            'rak_nama' => $data['rak_nama'],
            'rak_keterangan' => $data['rak_keterangan'] ?? null,
        ]);

        \App\Models\AktivitasLog::create([
            'user_id' => auth()->id(),
            'aktivitas' => 'Tambah Rak',
            'detail' => "Menambahkan rak baru: {$rack->rak_nama}",
            'ip_address' => request()->ip(),
        ]);

        return redirect()->route('rak.index', [
            'lokasi_id' => $data['ruangarsip_id'],
            'lemari_id' => $data['lemari_id'],
        ])->with('success', 'Rak berhasil ditambahkan.');
    }

    public function edit(Rack $rack)
    {
        $locations = Location::orderBy('ruangan')->get();
        $cabinets = Cabinet::with('location')->orderBy('lemari_nama')->get();

        return view('rak.edit', compact('rack', 'locations', 'cabinets'));
    }

    public function update(Request $request, Rack $rack)
    {
        $data = $request->validate([
            'ruangarsip_id' => 'required|exists:lokasi_simpan,id',
            'lemari_id' => 'required|exists:lemari,lemari_id',
            'rak_nama' => 'required|string|max:191',
            'rak_keterangan' => 'nullable|string',
        ]);

        $cabinet = Cabinet::where('lemari_id', $data['lemari_id'])
            ->where('ruangarsip_id', $data['ruangarsip_id'])
            ->first();

        if (!$cabinet) {
            return back()->withErrors(['lemari_id' => 'Pilih lemari yang sesuai dengan lokasi.'])->withInput();
        }

        $rack->update([
            'lemari_id' => $data['lemari_id'],
            'rak_nama' => $data['rak_nama'],
            'rak_keterangan' => $data['rak_keterangan'] ?? null,
        ]);

        \App\Models\AktivitasLog::create([
            'user_id' => auth()->id(),
            'aktivitas' => 'Ubah Rak',
            'detail' => "Mengubah rak: {$rack->rak_nama}",
            'ip_address' => request()->ip(),
        ]);

        return redirect()->route('rak.index', [
            'lokasi_id' => $data['ruangarsip_id'],
            'lemari_id' => $data['lemari_id'],
        ])->with('success', 'Rak berhasil diperbarui.');
    }

    public function destroy(Rack $rack)
    {
        $cabinet = $rack->cabinet;
        $lokasiId = $cabinet?->ruangarsip_id;
        $lemariId = $rack->lemari_id;

        $rack->delete();

        \App\Models\AktivitasLog::create([
            'user_id' => auth()->id(),
            'aktivitas' => 'Hapus Rak',
            'detail' => "Menghapus rak: {$rack->rak_nama}",
            'ip_address' => request()->ip(),
        ]);

        return redirect()->route('rak.index', [
            'lokasi_id' => $lokasiId,
            'lemari_id' => $lemariId,
        ])->with('success', 'Rak berhasil dihapus.');
    }

    public function exportExcel(Request $request)
    {
        $query = Rack::with(['cabinet.location']);

        if ($request->filled('lokasi_id')) {
            $query->whereHas('cabinet', fn ($q) => $q->where('ruangarsip_id', $request->lokasi_id));
        }

        if ($request->filled('lemari_id')) {
            $query->where('lemari_id', $request->lemari_id);
        }

        $racks = $query->orderBy('rak_nama')->get();

        $filename = 'rak_arsip_' . date('Y-m-d_His') . '.csv';
        $handle = fopen('php://temp', 'r+');

        // Header
        fputcsv($handle, [
            'Lokasi',
            'Lemari',
            'Nama Rak',
            'Keterangan'
        ]);

        // Data
        foreach ($racks as $rack) {
            fputcsv($handle, [
                $rack->cabinet?->location?->ruangan ?? '—',
                $rack->cabinet?->lemari_nama ?? '—',
                $rack->rak_nama,
                $rack->rak_keterangan ?? '—'
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
        return redirect()->route('rak.index', $request->all());
    }
}
