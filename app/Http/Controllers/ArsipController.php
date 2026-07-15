<?php

namespace App\Http\Controllers;

use App\Models\Archive;
use App\Models\JenisArsip;
use App\Models\Location;
use App\Services\RetensiService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Storage;

class ArsipController extends Controller
{
    public function create()
    {
        $jenis_arsips = $this->getJenisDokumenOptions();
        $locations = Location::orderBy('ruangan')->get();

        return view('arsip.tambah', [
            'jenis_arsips' => $jenis_arsips,
            'locations' => $locations,
            'retensiTersedia' => RetensiService::kolomRetensiTersedia(),
        ]);
    }

    private function getJenisDokumenOptions($currentJenisId = null)
    {
        $defaultJenis = [
            'Dokumen Tata Usaha',
            'Dokumen Keimigrasian',
            'Dokumen Pengawasan dan Penindakan',
        ];

        foreach ($defaultJenis as $namaJenis) {
            DB::table('jenis_arsip')->updateOrInsert(
                ['nama_jenis' => $namaJenis],
                ['masa_retensi_tahun' => 10, 'keterangan' => '']
            );
        }

        $query = JenisArsip::whereIn('nama_jenis', $defaultJenis);

        if ($currentJenisId) {
            $query->orWhere('id', $currentJenisId);
        }

        return $query->orderBy('nama_jenis')->get();
    }

    public function index(Request $request)
    {
        $query = DB::table('arsip')
            ->leftJoin('jenis_arsip', 'arsip.jenis_arsip_id', '=', 'jenis_arsip.id')
            ->leftJoin('lokasi_simpan', 'arsip.lokasi_id', '=', 'lokasi_simpan.id')
            ->leftJoin('lemari', 'arsip.cabinet_id', '=', 'lemari.lemari_id')
            ->leftJoin('rak', 'arsip.rack_id', '=', 'rak.rak_id')
            ->select(
                'arsip.*',
                'jenis_arsip.nama_jenis as nama_jenis',
                'lokasi_simpan.ruangan',
                'lemari.lemari_nama',
                'rak.rak_nama'
            );

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('arsip.nomor_surat', 'like', "%{$search}%")
                    ->orWhere('arsip.nama_arsip', 'like', "%{$search}%")
                    ->orWhere('arsip.perihal_surat', 'like', "%{$search}%");
            });
        }

        if ($request->filled('lokasi_id')) {
            $query->where('arsip.lokasi_id', $request->lokasi_id);
        }

        if ($request->filled('tanggal_mulai')) {
            $query->whereDate('arsip.tanggal_arsip', '>=', $request->tanggal_mulai);
        }

        if ($request->filled('tanggal_selesai')) {
            $query->whereDate('arsip.tanggal_arsip', '<=', $request->tanggal_selesai);
        }

        if ($request->filled('status')) {
            $query->where('arsip.status', $request->status);
        }

        $archives = $query->latest('arsip.id')->simplePaginate($request->get('per_page', 10))->withQueryString();

        $locations = Location::orderBy('ruangan')->get();

        return view('arsip.daftar', [
            'archives' => $archives,
            'locations' => $locations,
            'retensiTersedia' => RetensiService::kolomRetensiTersedia(),
        ]);
    }

    public function store(Request $request)
    {
        $rules = [
            'nomor_surat' => 'required|string|max:191|unique:arsip,nomor_surat',
            'nama_arsip' => 'required|string',
            'jenis_arsip_id' => 'required|exists:jenis_arsip,id',
            'lokasi_id' => 'required|exists:lokasi_simpan,id',
            'cabinet_id' => 'nullable|exists:lemari,lemari_id',
            'rack_id' => 'nullable|exists:rak,rak_id',
            'status' => 'required|in:Aktif,Inaktif',
            'status_ketersediaan' => 'required|in:Tersedia,Dipinjam',
            'tanggal_arsip' => 'required|date',
            'perihal_surat' => 'nullable|string',
            'file_arsip' => 'nullable|file',
        ];

        if (RetensiService::kolomRetensiTersedia()) {
            $rules['masa_retensi'] = 'required|in:3 Tahun,5 Tahun,10 Tahun';
        }

        $data = $request->validate($rules);

        // Set tahun_arsip from tanggal_arsip for backward compatibility
        if (isset($data['tanggal_arsip'])) {
            $data['tahun_arsip'] = date('Y', strtotime($data['tanggal_arsip']));
        }

        if ($request->hasFile('file_arsip')) {
            $file = $request->file('file_arsip');
            $fileName = time() . '_' . $file->getClientOriginalName();
            $file->storeAs('arsip_dokumen', $fileName, 'nas_storage');
            $data['file_arsip'] = $fileName;
        }

        if (RetensiService::kolomRetensiTersedia()) {
            $data['tanggal_retensi'] = RetensiService::hitungTanggalRetensi(
                $data['tanggal_arsip'],
                $data['masa_retensi']
            );
        } else {
            unset($data['masa_retensi']);
        }

        $archive = Archive::create($data);

        \App\Models\AktivitasLog::create([
            'user_id' => auth()->id(),
            'aktivitas' => 'Tambah Arsip',
            'detail' => "Menambahkan arsip baru: {$archive->nomor_surat} - {$archive->nama_arsip}",
            'ip_address' => request()->ip(),
        ]);

        return redirect()->route('arsip.index')->with('success', 'Arsip berhasil disimpan!');
    }

    public function edit($id)
    {
        $arsip = Archive::findOrFail($id);
        $jenis_arsips = $this->getJenisDokumenOptions($arsip->jenis_arsip_id);
        $locations = Location::orderBy('ruangan')->get();

        return view('arsip.ubah', [
            'arsip' => $arsip,
            'jenis_arsips' => $jenis_arsips,
            'locations' => $locations,
            'retensiTersedia' => RetensiService::kolomRetensiTersedia(),
        ]);
    }

    public function update(Request $request, $id)
    {
        $archive = Archive::findOrFail($id);

        $rules = [
            'nomor_surat' => ['required', 'string', 'max:191', Rule::unique('arsip', 'nomor_surat')->ignore($archive->id)],
            'nama_arsip' => 'required|string',
            'jenis_arsip_id' => 'required|exists:jenis_arsip,id',
            'lokasi_id' => 'required|exists:lokasi_simpan,id',
            'cabinet_id' => 'nullable|exists:lemari,lemari_id',
            'rack_id' => 'nullable|exists:rak,rak_id',
            'status' => 'required|in:Aktif,Inaktif',
            'status_ketersediaan' => 'required|in:Tersedia,Dipinjam',
            'tanggal_arsip' => 'required|date',
            'perihal_surat' => 'nullable|string',
            'file_arsip' => 'nullable|file',
        ];

        if (RetensiService::kolomRetensiTersedia()) {
            $rules['masa_retensi'] = 'required|in:3 Tahun,5 Tahun,10 Tahun';
        }

        $data = $request->validate($rules);

        // Set tahun_arsip from tanggal_arsip for backward compatibility
        if (isset($data['tanggal_arsip'])) {
            $data['tahun_arsip'] = date('Y', strtotime($data['tanggal_arsip']));
        }

        if ($request->hasFile('file_arsip')) {
            // Hapus file lama di NAS
            if ($archive->file_arsip) {
                Storage::disk('nas_storage')->delete('arsip_dokumen/' . $archive->file_arsip);
            }
            
            $file = $request->file('file_arsip');
            $fileName = time() . '_' . $file->getClientOriginalName();
            
            // Simpan file baru ke NAS
            $file->storeAs('arsip_dokumen', $fileName, 'nas_storage');
            $data['file_arsip'] = $fileName;
        }

        if (RetensiService::kolomRetensiTersedia()) {
            $data['tanggal_retensi'] = RetensiService::hitungTanggalRetensi(
                $data['tanggal_arsip'],
                $data['masa_retensi']
            );
        } else {
            unset($data['masa_retensi']);
        }

        $archive->update($data);

        \App\Models\AktivitasLog::create([
            'user_id' => auth()->id(),
            'aktivitas' => 'Ubah Arsip',
            'detail' => "Mengubah data arsip: {$archive->nomor_surat} - {$archive->nama_arsip}",
            'ip_address' => request()->ip(),
        ]);

        return redirect()->route('arsip.index')->with('success', 'Arsip diperbarui!');
    }

    public function show($id)
    {
        $archive = DB::table('arsip')
            ->leftJoin('jenis_arsip', 'arsip.jenis_arsip_id', '=', 'jenis_arsip.id')
            ->leftJoin('lokasi_simpan', 'arsip.lokasi_id', '=', 'lokasi_simpan.id')
            ->leftJoin('lemari', 'arsip.cabinet_id', '=', 'lemari.lemari_id')
            ->leftJoin('rak', 'arsip.rack_id', '=', 'rak.rak_id')
            ->select(
                'arsip.*',
                'jenis_arsip.nama_jenis as nama_jenis',
                'lokasi_simpan.ruangan',
                'lemari.lemari_nama',
                'rak.rak_nama'
            )
            ->where('arsip.id', $id)
            ->first();

        if (!$archive) {
            abort(404, 'Arsip tidak ditemukan.');
        }

        $archive->status_retensi = RetensiService::kolomRetensiTersedia()
            ? RetensiService::statusRetensi($archive->masa_retensi ?? null, $archive->tanggal_retensi ?? null)
            : null;

        return view('arsip.detail', compact('archive'));
    }

    public function destroy($id)
        {
            $archive = Archive::find($id);

            if (!$archive) {
                return redirect()->route('arsip.index')->with('error', 'Data tidak ditemukan.');
            }

            // PERBAIKAN: Hapus dari NAS, bukan public_path lokal
            if ($archive->file_arsip) {
                Storage::disk('nas_storage')->delete('arsip_dokumen/' . $archive->file_arsip);
            }

            $archive->delete();

            \App\Models\AktivitasLog::create([
                'user_id' => auth()->id(),
                'aktivitas' => 'Hapus Arsip',
                'detail' => "Menghapus arsip: {$archive->nomor_surat} - {$archive->nama_arsip}",
                'ip_address' => request()->ip(),
            ]);

            return redirect()->route('arsip.index')->with('success', 'Arsip berhasil dihapus!');
        }

    public function viewFile($filename)
    {
        $path = 'arsip_dokumen/' . $filename;

        // Debug: Cek apakah file ada
        if (!Storage::disk('nas_storage')->exists($path)) {
            // Coba cek dengan path absolut
            $fullPath = '\\\\10.10.1.95\\scan arsip\\DATABASE\\arsip_dokumen\\' . $filename;
            if (!file_exists($fullPath)) {
                abort(404, 'File tidak ditemukan: ' . $filename . ' (Path: ' . $fullPath . ')');
            }
        }

        // Ambil konten file dari NAS
        $file = Storage::disk('nas_storage')->get($path);

        // Tentukan tipe file berdasarkan ekstensi
        $extension = strtolower(pathinfo($filename, PATHINFO_EXTENSION));
        $mimeTypes = [
            'pdf' => 'application/pdf',
            'jpg' => 'image/jpeg',
            'jpeg' => 'image/jpeg',
            'png' => 'image/png',
            'gif' => 'image/gif',
            'doc' => 'application/msword',
            'docx' => 'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
            'xls' => 'application/vnd.ms-excel',
            'xlsx' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
        ];
        $type = $mimeTypes[$extension] ?? 'application/octet-stream';

        // Kembalikan respons file dengan header yang sesuai
        return response($file, 200)
            ->header('Content-Type', $type)
            ->header('Content-Disposition', 'inline; filename="' . $filename . '"')
            ->header('Cache-Control', 'private, max-age=3600');
    }

    public function ajukanRetensi(Request $request)
    {
        $ids = $request->input('ids', '');
        if (empty($ids)) {
            return back()->with('error', 'Pilih arsip terlebih dahulu.');
        }

        $idsArray = is_array($ids) ? $ids : explode(',', $ids);
        if (empty($idsArray)) {
            return back()->with('error', 'Pilih arsip terlebih dahulu.');
        }

        try {
            $archives = Archive::whereIn('id', $idsArray)->get();
            $updated = Archive::whereIn('id', $idsArray)->update(['status_retensi' => 'Proses Retensi']);
            
            \App\Models\AktivitasLog::create([
                'user_id' => auth()->id(),
                'aktivitas' => 'Ajukan Retensi',
                'detail' => "Mengajukan {$updated} arsip untuk proses retensi",
                'ip_address' => request()->ip(),
            ]);
            
            return back()->with('success', $updated . ' arsip berhasil diajukan untuk retensi.');
        } catch (\Exception $e) {
            return back()->with('error', 'Gagal mengajukan retensi: ' . $e->getMessage());
        }
    }

    public function batalRetensi(Request $request)
    {
        $ids = $request->input('ids', '');
        if (empty($ids)) {
            return back()->with('error', 'Pilih arsip terlebih dahulu.');
        }

        $idsArray = is_array($ids) ? $ids : explode(',', $ids);
        if (empty($idsArray)) {
            return back()->with('error', 'Pilih arsip terlebih dahulu.');
        }

        try {
            $archives = Archive::whereIn('id', $idsArray)->get();
            $updated = Archive::whereIn('id', $idsArray)
                ->where('status_retensi', 'Proses Retensi')
                ->update(['status_retensi' => 'Masuk Masa Retensi']);
            
            \App\Models\AktivitasLog::create([
                'user_id' => auth()->id(),
                'aktivitas' => 'Batal Retensi',
                'detail' => "Membatalkan retensi untuk {$updated} arsip",
                'ip_address' => request()->ip(),
            ]);
            
            return back()->with('success', $updated . ' arsip berhasil dibatalkan retensinya.');
        } catch (\Exception $e) {
            return back()->with('error', 'Gagal membatalkan retensi: ' . $e->getMessage());
        }
    }

    public function selesaiRetensi(Request $request)
    {
        $ids = $request->input('ids', '');
        if (empty($ids)) {
            return back()->with('error', 'Pilih arsip terlebih dahulu.');
        }

        $idsArray = is_array($ids) ? $ids : explode(',', $ids);
        if (empty($idsArray)) {
            return back()->with('error', 'Pilih arsip terlebih dahulu.');
        }

        try {
            $archives = Archive::whereIn('id', $idsArray)->get();
            $updated = Archive::whereIn('id', $idsArray)->update(['status_retensi' => 'Sudah Retensi']);
            
            \App\Models\AktivitasLog::create([
                'user_id' => auth()->id(),
                'aktivitas' => 'Selesai Retensi',
                'detail' => "Menyelesaikan retensi untuk {$updated} arsip",
                'ip_address' => request()->ip(),
            ]);
            
            return back()->with('success', $updated . ' arsip retensi selesai diproses.');
        } catch (\Exception $e) {
            return back()->with('error', 'Gagal menyelesaikan retensi: ' . $e->getMessage());
        }
    }

    public function bulkDelete(Request $request)
    {
        $ids = $request->input('ids', '');
        if (empty($ids)) {
            return back()->with('error', 'Pilih arsip terlebih dahulu.');
        }

        $idsArray = is_array($ids) ? $ids : explode(',', $ids);
        if (empty($idsArray)) {
            return back()->with('error', 'Pilih arsip terlebih dahulu.');
        }

        try {
            $archives = Archive::whereIn('id', $idsArray)->get();
            $deletedCount = 0;

            foreach ($archives as $archive) {
                // Hapus file dari NAS jika ada
                if ($archive->file_arsip) {
                    Storage::disk('nas_storage')->delete('arsip_dokumen/' . $archive->file_arsip);
                }
                $archive->delete();
                $deletedCount++;
            }

            \App\Models\AktivitasLog::create([
                'user_id' => auth()->id(),
                'aktivitas' => 'Hapus Arsip (Bulk)',
                'detail' => "Menghapus {$deletedCount} arsip sekaligus",
                'ip_address' => request()->ip(),
            ]);

            return back()->with('success', $deletedCount . ' arsip berhasil dihapus.');
        } catch (\Exception $e) {
            return back()->with('error', 'Gagal menghapus arsip: ' . $e->getMessage());
        }
    }

    public function exportExcel(Request $request)
    {
        $query = DB::table('arsip')
            ->leftJoin('jenis_arsip', 'arsip.jenis_arsip_id', '=', 'jenis_arsip.id')
            ->leftJoin('lokasi_simpan', 'arsip.lokasi_id', '=', 'lokasi_simpan.id')
            ->leftJoin('lemari', 'arsip.cabinet_id', '=', 'lemari.lemari_id')
            ->leftJoin('rak', 'arsip.rack_id', '=', 'rak.rak_id')
            ->select(
                'arsip.nomor_surat',
                'arsip.nama_arsip',
                'arsip.perihal_surat',
                'jenis_arsip.nama_jenis',
                'arsip.tanggal_arsip',
                'lokasi_simpan.ruangan',
                'lemari.lemari_nama',
                'rak.rak_nama',
                'arsip.status',
                'arsip.status_ketersediaan',
                'arsip.masa_retensi',
                'arsip.tanggal_retensi',
                'arsip.status_retensi'
            );

        // If specific IDs are provided (from checklist)
        if ($request->filled('ids')) {
            $ids = explode(',', $request->ids);
            $query->whereIn('arsip.id', $ids);
        } else {
            // Apply same filters as index
            if ($request->filled('search')) {
                $search = $request->search;
                $query->where(function ($q) use ($search) {
                    $q->whereraw('arsip.nomor_surat like ?', "%{$search}%")
                        ->orWhereRaw('arsip.nama_arsip like ?', "%{$search}%")
                        ->orWhereRaw('arsip.perihal_surat like ?', "%{$search}%");
                });
            }

            if ($request->filled('lokasi_id')) {
                $query->where('arsip.lokasi_id', $request->lokasi_id);
            }

            if ($request->filled('tanggal_mulai')) {
                $query->whereDate('arsip.tanggal_arsip', '>=', $request->tanggal_mulai);
            }

            if ($request->filled('tanggal_selesai')) {
                $query->whereDate('arsip.tanggal_arsip', '<=', $request->tanggal_selesai);
            }

            if ($request->filled('status')) {
                $query->where('arsip.status', $request->status);
            }
        }

        $archives = $query->get();

        $filename = 'arsip_' . date('Y-m-d_His') . '.csv';
        $handle = fopen('php://temp', 'r+');

        // Header
        fputcsv($handle, [
            'Nomor Surat',
            'Nama Arsip',
            'Perihal',
            'Jenis',
            'Tanggal',
            'Lokasi',
            'Lemari',
            'Rak',
            'Status',
            'Ketersediaan',
            'Masa Retensi',
            'Tanggal Retensi',
            'Status Retensi'
        ]);

        // Data
        foreach ($archives as $archive) {
            fputcsv($handle, [
                $archive->nomor_surat,
                $archive->nama_arsip,
                $archive->perihal_surat,
                $archive->nama_jenis,
                $archive->tanggal_arsip,
                $archive->ruangan,
                $archive->lemari_nama,
                $archive->rak_nama,
                $archive->status,
                $archive->status_ketersediaan,
                $archive->masa_retensi,
                $archive->tanggal_retensi,
                $archive->status_retensi
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
        // For PDF export, we'll redirect to print view for now
        // In production, you'd use a PDF library like dompdf or snappy
        return redirect()->route('arsip.print', $request->all());
    }

    public function getFileInfo($id)
    {
        $archive = DB::table('arsip')
            ->leftJoin('jenis_arsip', 'arsip.jenis_arsip_id', '=', 'jenis_arsip.id')
            ->select(
                'arsip.id',
                'arsip.nomor_surat',
                'arsip.nama_arsip',
                'arsip.file_arsip',
                'arsip.masa_retensi',
                'arsip.tanggal_retensi',
                'arsip.status_retensi',
                'jenis_arsip.nama_jenis as nama_jenis'
            )
            ->where('arsip.id', $id)
            ->first();

        if (!$archive) {
            return response()->json(['error' => 'Arsip tidak ditemukan'], 404);
        }

        $fileUrl = null;
        $extension = null;

        if ($archive->file_arsip) {
            $fileUrl = route('arsip.viewFile', ['filename' => $archive->file_arsip]);
            $extension = strtolower(pathinfo($archive->file_arsip, PATHINFO_EXTENSION));
        }

        return response()->json([
            'id' => $archive->id,
            'nomor_surat' => $archive->nomor_surat,
            'nama_arsip' => $archive->nama_arsip,
            'nama_jenis' => $archive->nama_jenis,
            'file_url' => $fileUrl,
            'extension' => $extension,
            'masa_retensi' => $archive->masa_retensi,
            'tanggal_retensi' => $archive->tanggal_retensi,
            'status_retensi' => $archive->status_retensi,
        ]);
    }

    public function print(Request $request)
    {
        $query = DB::table('arsip')
            ->leftJoin('jenis_arsip', 'arsip.jenis_arsip_id', '=', 'jenis_arsip.id')
            ->leftJoin('lokasi_simpan', 'arsip.lokasi_id', '=', 'lokasi_simpan.id')
            ->leftJoin('lemari', 'arsip.cabinet_id', '=', 'lemari.lemari_id')
            ->leftJoin('rak', 'arsip.rack_id', '=', 'rak.rak_id')
            ->select(
                'arsip.*',
                'jenis_arsip.nama_jenis as nama_jenis',
                'lokasi_simpan.ruangan',
                'lemari.lemari_nama',
                'rak.rak_nama'
            );

        // If specific IDs are provided (from checklist)
        if ($request->filled('ids')) {
            $ids = explode(',', $request->ids);
            $query->whereIn('arsip.id', $ids);
        } else {
            // Apply same filters as index
            if ($request->filled('search')) {
                $search = $request->search;
                $query->where(function ($q) use ($search) {
                    $q->whereraw('arsip.nomor_surat like ?', "%{$search}%")
                        ->orWhereRaw('arsip.nama_arsip like ?', "%{$search}%")
                        ->orWhereRaw('arsip.perihal_surat like ?', "%{$search}%");
                });
            }

            if ($request->filled('lokasi_id')) {
                $query->where('arsip.lokasi_id', $request->lokasi_id);
            }

            if ($request->filled('tanggal_mulai')) {
                $query->whereDate('arsip.tanggal_arsip', '>=', $request->tanggal_mulai);
            }

            if ($request->filled('tanggal_selesai')) {
                $query->whereDate('arsip.tanggal_arsip', '<=', $request->tanggal_selesai);
            }

            if ($request->filled('status')) {
                $query->where('arsip.status', $request->status);
            }
        }

        $archives = $query->latest('arsip.id')->get();

        return view('arsip.print', compact('archives'));
    }
}