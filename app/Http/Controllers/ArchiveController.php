<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class ArchiveController extends Controller
{
   
    public function create()
    {
    $klasifikasi_utama = DB::table('klasifikasi_arsip')->whereNull('parent_id')->orderBy('kode')->get();
    $jenis_arsip = DB::table('jenis_arsip')->get();
    $lokasi_simpan = DB::table('lokasi_simpan')->get();

    return view('archives.create', compact('klasifikasi_utama', 'jenis_arsip', 'lokasi_simpan'));
    }

    public function getSubKlasifikasi($parent_id)
    {
    $data = DB::table('klasifikasi_arsip')->where('parent_id', $parent_id)->get();
    return response()->json($data);
    }

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
                  ->orWhere('arsip.perihal_surat', 'like', "%{$search}%");
            });
        }

        // Filter Lainnya
        if ($request->filled('location_id')) $query->where('arsip.lokasi_id', $request->location_id);
        if ($request->filled('tahun')) $query->whereRaw('YEAR(arsip.tanggal_arsip) = ?', [$request->tahun]);
        if ($request->filled('status')) $query->where('arsip.status', $request->status);

        $archives = $query->latest('arsip.id')->paginate(15)->withQueryString();

        // TRANSFORMASI: Bersihkan nomor surat agar tahun hilang
        $archives->getCollection()->transform(function ($item) {
            $item->nomor_surat_bersih = str_contains($item->nomor_surat, '/') 
                ? explode('/', $item->nomor_surat)[0] 
                : $item->nomor_surat;
            return $item;
        });

        $locations = DB::table('lokasi_simpan')
             ->selectRaw("id, CONCAT('R.', ruangan, ' - L.', lemari, ' - Rak ', rak) as nama_lokasi")
             ->orderBy('ruangan')
             ->get();

        $years = DB::table('arsip')
             ->selectRaw('YEAR(tanggal_arsip) as tahun')
             ->whereNotNull('tanggal_arsip')
             ->distinct()
             ->orderByDesc('tahun')
             ->pluck('tahun');

        return view('archives.index', compact('archives', 'locations', 'years'));
    }

    public function store(Request $request)
    {

    $data = [
        'nomor_surat'           => $request->nomor_surat,
        'nama_arsip'            => $request->nama_arsip,
        'status'                => $request->status,
        'status_ketersediaan'   => $request->status_ketersediaan,
        'tanggal_arsip'         => $request->tanggal_arsip,
        'tahun_arsip'           => $request->tahun,
        'jenis_arsip_id'        => $request->jenis_arsip_id,
        'lokasi_id'             => $request->lokasi_id,
        'perihal_surat'         => $request->perihal_surat,
        
    ];


    // Proses File
    if ($request->hasFile('file_arsip')) {
        $file = $request->file('file_arsip');
        $fileName = time() . '_' . $file->getClientOriginalName();
        $file->move(public_path('dokumen_arsip'), $fileName);

        $data['file_arsip'] = $fileName;
    }

    // Simpan ke DB
    \DB::table('arsip')->insert($data);

    return redirect()->route('arsip.index')->with('success', 'Arsip berhasil disimpan!');

    }

    public function edit($id)
    {
    $arsip = DB::table('arsip')->where('id', $id)->first();
    if (!$arsip) abort(404);

    // Ambil data untuk dropdown
    $klasifikasi_utama = DB::table('klasifikasi_arsip')->whereNull('parent_id')->orderBy('kode')->get();
    
    // Ambil data pendukung
    $jenis_arsip = DB::table('jenis_arsip')->get();
    $lokasi_simpan = DB::table('lokasi_simpan')->get();

    return view('archives.edit', compact('arsip', 'jenis_arsip', 'lokasi_simpan', 'klasifikasi_utama'));
    }

    public function update(Request $request, $id)
    {
    // Ambil data dari form
    $data = $request->only(['nomor_surat', 'nama_arsip', 'jenis_arsip_id', 'lokasi_id', 'status', 'status_ketersediaan']);
    
    // PENTING: Proses Nomor Surat
    // Jika input nomor surat hanya "WIM.11.I.2-..." tanpa "/2026"
    $tahun = date('Y'); // Atau ambil dari input jika ada
    $data['nomor_surat'] = $request->nomor_surat . '/' . $tahun;

    DB::table('arsip')->where('id', $id)->update($data);

    return redirect()->route('arsip.index')->with('success', 'Arsip diperbarui!');
    }
    
    public function show($id)
{
    $archive = DB::table('arsip')
        ->leftJoin('jenis_arsip', 'arsip.jenis_arsip_id', '=', 'jenis_arsip.id')
        ->leftJoin('lokasi_simpan', 'arsip.lokasi_id', '=', 'lokasi_simpan.id')
        ->select(
            'arsip.*', 
            'jenis_arsip.nama_jenis as nama_klasifikasi', 
            DB::raw("CONCAT('R.', lokasi_simpan.ruangan, ' - L.', lokasi_simpan.lemari, ' - Rak ', lokasi_simpan.rak) as nama_lokasi")
        )
        ->where('arsip.id', $id)
        ->first();

    if (!$archive) {
        abort(404, 'Arsip tidak ditemukan.');
    }

    return view('archives.show', compact('archive'));
}

    public function destroy($id)
    {
         // 1. Cari data arsip
            $arsip = DB::table('arsip')->where('id', $id)->first();

             if (!$arsip) {
            return redirect()->route('arsip.index')->with('error', 'Data tidak ditemukan.');
         }

         // 2. Hapus file fisik jika ada (Opsional, sesuaikan dengan path folder Anda)
             if ($arsip->file_arsip && file_exists(storage_path('app/public/dokumen_arsip/' . $arsip->file_arsip))) {
             unlink(storage_path('app/public/dokumen_arsip/' . $arsip->file_arsip));
         }

        // 3. Hapus data dari database
             DB::table('arsip')->where('id', $id)->delete();

            return redirect()->route('arsip.index')->with('success', 'Arsip berhasil dihapus!');
    }

}