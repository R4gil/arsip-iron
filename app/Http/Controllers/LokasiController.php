<?php

namespace App\Http\Controllers;

use App\Models\Location;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Schema;

class LokasiController extends Controller
{
    public function index(Request $request)
    {
        $locations = Location::withCount(['cabinets', 'archives'])
            ->with(['cabinets' => function ($q) {
                $q->withCount('racks');
            }])
            ->orderBy('ruangan')
            ->simplePaginate($request->get('per_page', 10));

        return view('lokasi.index', compact('locations'));
    }

    public function create()
    {
        return view('lokasi.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'ruangan' => 'required|string|max:191',
            'keterangan' => 'nullable|string',
        ]);

        $data = [
            'ruangan' => $request->input('ruangan'),
        ];

        if (Schema::hasColumn('lokasi_simpan', 'keterangan')) {
            $data['keterangan'] = $request->input('keterangan');
        }

        Location::create($data);

        \App\Models\AktivitasLog::create([
            'user_id' => auth()->id(),
            'aktivitas' => 'Tambah Lokasi',
            'detail' => "Menambahkan lokasi baru: {$data['ruangan']}",
            'ip_address' => request()->ip(),
        ]);

        return redirect()->route('lokasi.index')->with('success', 'Lokasi arsip berhasil ditambahkan.');
    }

    public function edit(Location $location)
    {
        return view('lokasi.edit', compact('location'));
    }

    public function update(Request $request, Location $location)
    {
        $request->validate([
            'ruangan' => 'required|string|max:191',
            'keterangan' => 'nullable|string',
        ]);

        $data = [
            'ruangan' => $request->input('ruangan'),
        ];

        if (Schema::hasColumn('lokasi_simpan', 'keterangan')) {
            $data['keterangan'] = $request->input('keterangan');
        }

        $location->update($data);

        \App\Models\AktivitasLog::create([
            'user_id' => auth()->id(),
            'aktivitas' => 'Ubah Lokasi',
            'detail' => "Mengubah lokasi: {$location->ruangan}",
            'ip_address' => request()->ip(),
        ]);

        return redirect()->route('lokasi.index')->with('success', 'Lokasi arsip berhasil diperbarui.');
    }

    public function destroy(Location $location)
    {
        if ($location->cabinets()->exists()) {
            return redirect()->route('lokasi.index')->with('error', 'Lokasi masih memiliki lemari. Hapus lemari terlebih dahulu.');
        }

        if ($location->archives()->exists()) {
            return redirect()->route('lokasi.index')->with('error', 'Lokasi masih digunakan oleh arsip.');
        }

        $location->delete();

        \App\Models\AktivitasLog::create([
            'user_id' => auth()->id(),
            'aktivitas' => 'Hapus Lokasi',
            'detail' => "Menghapus lokasi: {$location->ruangan}",
            'ip_address' => request()->ip(),
        ]);

        return redirect()->route('lokasi.index')->with('success', 'Lokasi arsip berhasil dihapus.');
    }

    public function exportExcel(Request $request)
    {
        $locations = Location::withCount(['cabinets', 'archives'])
            ->with(['cabinets' => function ($q) {
                $q->withCount('racks');
            }])
            ->orderBy('ruangan')
            ->get();

        $filename = 'lokasi_arsip_' . date('Y-m-d_His') . '.csv';
        $handle = fopen('php://temp', 'r+');

        // Header
        fputcsv($handle, [
            'Nama Ruangan',
            'Jumlah Lemari',
            'Jumlah Rak',
            'Jumlah Arsip',
            'Keterangan'
        ]);

        // Data
        foreach ($locations as $location) {
            $totalRak = $location->cabinets->sum('racks_count');
            fputcsv($handle, [
                $location->ruangan,
                $location->cabinets_count,
                $totalRak,
                $location->archives_count,
                $location->keterangan ?? '—'
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
        return redirect()->route('lokasi.index', $request->all());
    }
}