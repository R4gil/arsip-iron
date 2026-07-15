<?php

namespace App\Http\Controllers;

use App\Models\Classification;
use Illuminate\Http\Request;

class KlasifikasiController extends Controller
{
    public function index(Request $request)
    {
        $classifications = Classification::latest()->simplePaginate($request->get('per_page', 10));

        return view('klasifikasi.daftar', compact('classifications'));
    }

    public function create()
    {
        return view('klasifikasi.tambah');
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'kode' => 'required|string|max:191|unique:classifications,kode',
            'nama' => 'required|string|max:191',
        ]);

        Classification::create($data);

        \App\Models\AktivitasLog::create([
            'user_id' => auth()->id(),
            'aktivitas' => 'Tambah Klasifikasi',
            'detail' => "Menambahkan klasifikasi baru: {$data['kode']} - {$data['nama']}",
            'ip_address' => request()->ip(),
        ]);

        return redirect()->route('klasifikasi.index')->with('success', 'Klasifikasi berhasil ditambahkan.');
    }

    public function edit(Classification $classification)
    {
        return view('klasifikasi.ubah', compact('classification'));
    }

    public function update(Request $request, Classification $classification)
    {
        $data = $request->validate([
            'kode' => 'required|string|max:191|unique:classifications,kode,' . $classification->id,
            'nama' => 'required|string|max:191',
        ]);

        $classification->update($data);

        \App\Models\AktivitasLog::create([
            'user_id' => auth()->id(),
            'aktivitas' => 'Ubah Klasifikasi',
            'detail' => "Mengubah klasifikasi: {$classification->kode} - {$classification->nama}",
            'ip_address' => request()->ip(),
        ]);

        return redirect()->route('klasifikasi.index')->with('success', 'Klasifikasi berhasil diperbarui.');
    }

    public function destroy(Classification $classification)
    {
        $kode = $classification->kode;
        $nama = $classification->nama;
        
        $classification->delete();

        \App\Models\AktivitasLog::create([
            'user_id' => auth()->id(),
            'aktivitas' => 'Hapus Klasifikasi',
            'detail' => "Menghapus klasifikasi: {$kode} - {$nama}",
            'ip_address' => request()->ip(),
        ]);

        return redirect()->route('klasifikasi.index')->with('success', 'Klasifikasi berhasil dihapus.');
    }

    public function import(Request $request)
    {
        $request->validate([
            'file_csv' => 'required|file|mimes:csv,txt|max:2048',
        ]);

        $file = $request->file('file_csv');
        $path = $file->getRealPath();
        $handle = fopen($path, 'r');

        if ($handle === false) {
            return redirect()->back()->with('error', 'Gagal membaca file CSV.');
        }

        $imported = 0;
        $skipped = 0;

        while (($row = fgetcsv($handle, 1000, ',')) !== false) {
            $kode = trim($row[0] ?? '');
            $nama = trim($row[1] ?? '');

            if (empty($kode) || empty($nama)) {
                $skipped++;
                continue;
            }

            Classification::updateOrCreate(
                ['kode' => $kode],
                ['nama' => $nama]
            );

            $imported++;
        }

        fclose($handle);

        \App\Models\AktivitasLog::create([
            'user_id' => auth()->id(),
            'aktivitas' => 'Import Klasifikasi',
            'detail' => "Mengimport {$imported} klasifikasi dari CSV",
            'ip_address' => request()->ip(),
        ]);

        return redirect()->route('klasifikasi.index')->with('success', "Import selesai. {$imported} data berhasil diimport, {$skipped} dilewati.");
    }
}