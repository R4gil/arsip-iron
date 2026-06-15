<?php

namespace App\Http\Controllers;

use App\Http\Requests\ArchiveStoreRequest;
use App\Http\Requests\ArchiveUpdateRequest;
use App\Models\Archive;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ArchiveController extends Controller
{
public function index(Request $request)
{
    $query = DB::table('arsip')
        ->leftJoin('jenis_arsip', 'arsip.jenis_arsip_id', '=', 'jenis_arsip.id')
        ->leftJoin('lokasi_simpan', 'arsip.lokasi_id', '=', 'lokasi_simpan.id')
        ->select(
            'arsip.*', 
            'jenis_arsip.nama_jenis as nama_klasifikasi', 
            DB::raw("CONCAT('R.', lokasi_simpan.ruangan, ' - L.', lokasi_simpan.lemari, ' - Rak ', lokasi_simpan.rak) as nama_lokasi")
        );

    // Filter Pencarian Global
    if ($request->filled('search')) {
        $search = $request->search;
        $query->where(function ($q) use ($search) {
            $q->where('arsip.nomor_surat', 'like', "%{$search}%")
              ->orWhere('arsip.nama_arsip', 'like', "%{$search}%")
              ->orWhere('arsip.perihal_surat', 'like', "%{$search}%")
              ->orWhere('jenis_arsip.nama_jenis', 'like', "%{$search}%")
              ->orWhere('lokasi_simpan.ruangan', 'like', "%{$search}%")
              ->orWhere('lokasi_simpan.lemari', 'like', "%{$search}%")
              ->orWhere('lokasi_simpan.rak', 'like', "%{$search}%");
        });
    }

        // Filter dropdown lokasi_id
        if ($request->filled('location_id')) {
            $query->where('arsip.lokasi_id', $request->location_id);
        }

        // Filter dropdown tahun
        if ($request->filled('tahun')) {
            $query->where('arsip.tahun', $request->tahun);
        }

        // Filter dropdown status (Aktif/Inaktif)
        if ($request->filled('status')) {
            $query->where('arsip.status', $request->status);
        }

        // Eksekusi pagination
        $archives = $query->latest('arsip.id')->paginate(15)->withQueryString();

        // Data untuk isi select filter di index
        $locations = DB::table('lokasi_simpan')
             ->selectRaw("id, CONCAT('R.', ruangan, ' - L.', lemari, ' - Rak ', rak) as nama_lokasi")
             ->orderBy('ruangan')
             ->get();

        $years = DB::table('arsip')
             ->select('tahun')
             ->distinct()
             ->orderByDesc('tahun')
             ->pluck('tahun');

        return view('archives.index', compact('archives', 'locations', 'years'));
    }

public function create()
{
    $lokasi = \DB::table('lokasi_simpan')
        ->selectRaw("id, ruangan, lemari, rak")
        ->orderBy('ruangan')
        ->get();

    // Mengambil nama_jenis dan diurutkan berdasarkan nama_jenis
    $klasifikasi = \DB::table('jenis_arsip')
        ->selectRaw("id, nama_jenis")
        ->orderBy('nama_jenis')
        ->get();

    return view('archives.create', compact('lokasi', 'klasifikasi'));
}

    public function store(Request $request)
    {
        // Validasi disesuaikan 100% dengan tipe data tabel 'arsip' di SQL Anda
        $validated = $request->validate([
            'nama_arsip'          => 'required|string',
            'nomor_surat'         => 'nullable|string|max:150',
            'perihal_surat'       => 'nullable|string',
            'tahun'               => 'required|digits:4',
            'jenis_arsip_id'      => 'required|integer',
            'lokasi_id'           => 'required|integer',
            'tanggal_arsip'       => 'required|date',
            'status'              => 'required|in:Aktif,Inaktif',
            'status_ketersediaan' => 'required|in:Tersedia,Dipinjam',
        ]);

        // Insert data menggunakan Query Builder agar terhindar dari kendala fillable Model
        DB::table('arsip')->insert(array_merge($validated, [
            'created_at' => now(),
            'updated_at' => now()
        ]));

        return redirect()->route('arsip.index')->with('success', 'Arsip berhasil ditambahkan.');
    }

    public function show($id)
    {
        $archive = DB::table('arsip')
            ->leftJoin('klasifikasi', 'arsip.jenis_arsip_id', '=', 'klasifikasi.id')
            ->leftJoin('lokasi_simpan', 'arsip.lokasi_id', '=', 'lokasi_simpan.id')
            ->select(
                'arsip.*', 
                'klasifikasi.nama as nama_klasifikasi', 
                'klasifikasi.kode as kode_klasifikasi',
                DB::raw("CONCAT('R.', lokasi_simpan.ruangan, ' - L.', lokasi_simpan.lemari, ' - Rak ', lokasi_simpan.rak) as nama_lokasi")
            )
            ->where('arsip.id', $id)
            ->first();

        if (!$archive) {
            abort(404, 'Arsip tidak ditemukan.');
        }

        // Ambil data peminjaman terkait jika ada
        $borrowings = DB::table('peminjaman_arsip')
            ->where('arsip_id', $id)
            ->latest()
            ->get();

        return view('archives.show', compact('archive', 'borrowings'));
    }

    public function edit($id)
    {
        $archive = DB::table('arsip')->where('id', $id)->first();
        
        if (!$archive) {
            abort(404, 'Arsip tidak ditemukan.');
        }

        $lokasi = DB::table('lokasi_simpan')
            ->selectRaw("id, ruangan, lemari, rak")
            ->orderBy('ruangan')
            ->get();

        $klasifikasi = DB::table('klasifikasi')->orderBy('kode')->get();

        return view('archives.edit', compact('archive', 'lokasi', 'klasifikasi'));
    }

    public function update(Request $request, $id)
    {
        $validated = $request->validate([
            'nama_arsip'          => 'required|string',
            'nomor_surat'         => 'nullable|string|max:150',
            'perihal_surat'       => 'nullable|string',
            'tahun'               => 'required|digits:4',
            'jenis_arsip_id'      => 'required|integer',
            'lokasi_id'           => 'required|integer',
            'tanggal_arsip'       => 'required|date',
            'status'              => 'required|in:Aktif,Inaktif',
            'status_ketersediaan' => 'required|in:Tersedia,Dipinjam',
        ]);

        DB::table('arsip')
            ->where('id', $id)
            ->update(array_merge($validated, [
                'updated_at' => now()
            ]));

        return redirect()->route('arsip.index')->with('success', 'Arsip berhasil diperbarui.');
    }

    public function destroy($id)
    {
        DB::table('arsip')->where('id', $id)->delete();

        return redirect()->route('arsip.index')->with('success', 'Arsip berhasil dihapus.');
    }
}