<?php

namespace App\Http\Controllers;

use App\Models\Archive;
use App\Models\Location;
use App\Services\RetensiService;
use App\Services\LayananEkstraksiDokumen;
use App\Services\LayananEmbedding;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Storage;

class ArsipController extends Controller
{
    public function create()
    {
        $locations = Location::orderBy('ruangan')->get();

        return view('arsip.tambah', [
            'jenis_dokumen_list' => self::JENIS_DOKUMEN,
            'locations' => $locations,
            'retensiTersedia' => RetensiService::kolomRetensiTersedia(),
        ]);
    }

    public const JENIS_DOKUMEN = [
        'Dokumen Tata Usaha',
        'Dokumen Keimigrasian',
        'Dokumen Pengawasan dan Penindakan',
    ];

    public function index(Request $request)
    {
        $query = DB::table('arsip')
            ->leftJoin('klasifikasi', 'arsip.jenis_arsip_id', '=', 'klasifikasi.id')
            ->leftJoin('lokasi_simpan', 'arsip.lokasi_id', '=', 'lokasi_simpan.id')
            ->leftJoin('lemari', 'arsip.cabinet_id', '=', 'lemari.lemari_id')
            ->leftJoin('rak', 'arsip.rack_id', '=', 'rak.rak_id')
            ->select(
                'arsip.*',
                'klasifikasi.nama as nama_jenis',
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

        // Sorting
        $sortableColumns = [
            'nomor_surat' => 'arsip.nomor_surat',
            'tanggal_arsip' => 'arsip.tanggal_arsip',
            'nama_arsip' => 'arsip.nama_arsip',
            'nama_jenis' => 'klasifikasi.nama',
            'status' => 'arsip.status',
            'status_ketersediaan' => 'arsip.status_ketersediaan',
            'masa_retensi' => 'arsip.masa_retensi',
            'tanggal_retensi' => 'arsip.tanggal_retensi',
            'status_retensi' => 'arsip.status_retensi',
        ];

        $sortBy = $request->get('sort_by', 'arsip.id');
        $sortOrder = $request->get('sort_order', 'desc');

        if (isset($sortableColumns[$sortBy])) {
            $sortBy = $sortableColumns[$sortBy];
        } elseif ($sortBy !== 'arsip.id') {
            $sortBy = 'arsip.id';
        }

        $sortOrder = strtolower($sortOrder) === 'asc' ? 'asc' : 'desc';

        $query->orderBy($sortBy, $sortOrder);

        $archives = $query->paginate($request->get('per_page', 10))->withQueryString();

        $locations = Location::orderBy('ruangan')->get();

        return view('arsip.daftar', [
            'archives' => $archives,
            'locations' => $locations,
            'retensiTersedia' => RetensiService::kolomRetensiTersedia(),
            'sortBy' => $request->get('sort_by', 'arsip.id'),
            'sortOrder' => $sortOrder,
        ]);
    }

    public function store(Request $request)
    {
        \Log::info('=== Mulai proses simpan arsip ===', [
            'input' => $request->all(),
            'has_file' => $request->hasFile('file_arsip'),
        ]);

        $rules = [
            'nomor_surat' => 'required|string|max:191|unique:arsip,nomor_surat',
            'nama_arsip' => 'required|string',
            'jenis_dokumen' => 'required|in:Dokumen Tata Usaha,Dokumen Keimigrasian,Dokumen Pengawasan dan Penindakan',
            'jenis_arsip_id' => 'nullable|exists:klasifikasi,id',
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
            $rules['masa_retensi'] = 'required|in:3 Tahun,5 Tahun,10 Tahun,Permanen';
        }

        try {
            $data = $request->validate($rules);
            \Log::info('Validation passed', ['data' => $data]);
        } catch (\Illuminate\Validation\ValidationException $e) {
            \Log::error('Validation failed', ['errors' => $e->errors()]);
            throw $e;
        }

        // Set tahun_arsip from tanggal_arsip for backward compatibility
        if (isset($data['tanggal_arsip'])) {
            $data['tahun_arsip'] = date('Y', strtotime($data['tanggal_arsip']));
        }

        if ($request->hasFile('file_arsip')) {
            $file = $request->file('file_arsip');
            $fileName = time() . '_' . $file->getClientOriginalName();
            \Log::info('Uploading file', ['filename' => $fileName]);
            
            try {
                $file->storeAs('arsip_dokumen', $fileName, 'nas_storage');
                $data['file_arsip'] = $fileName;
                \Log::info('File uploaded successfully');
                
                // Ekstrak isi dokumen secara otomatis
                $layananEkstraksi = new LayananEkstraksiDokumen();
                $isiDokumen = $layananEkstraksi->ekstrakIsiDokumen($fileName, $file->getClientOriginalName());
                if ($isiDokumen) {
                    $data['isi_dokumen'] = $isiDokumen;
                    \Log::info('[Ingest] Ekstraksi PDF berhasil', [
                        'file' => $fileName,
                        'jumlah_karakter' => strlen($isiDokumen),
                        'preview' => substr($isiDokumen, 0, 200),
                    ]);
                } else {
                    \Log::warning('[Ingest] Ekstraksi PDF gagal atau kosong', [
                        'file' => $fileName,
                        'original_name' => $file->getClientOriginalName(),
                        'ekstensi' => $file->getClientOriginalExtension(),
                    ]);
                }
            } catch (\Exception $e) {
                \Log::error('File upload failed', ['error' => $e->getMessage()]);
                throw $e;
            }
        }

        if (RetensiService::kolomRetensiTersedia()) {
            $data['tanggal_retensi'] = RetensiService::hitungTanggalRetensi(
                $data['tanggal_arsip'],
                $data['masa_retensi']
            );
            
            // Set status_retensi to Permanen when masa_retensi is Permanen
            if ($data['masa_retensi'] === 'Permanen') {
                $data['status_retensi'] = 'Permanen';
            } else {
                // Calculate initial status_retensi for non-permanent
                $data['status_retensi'] = RetensiService::statusRetensi(
                    $data['masa_retensi'],
                    $data['tanggal_retensi']
                );
            }
        } else {
            unset($data['masa_retensi']);
        }

        try {
            \Log::info('Creating archive record', ['data' => $data]);
            $archive = Archive::create($data);
            \Log::info('[Ingest] Arsip berhasil disimpan ke database', [
                'archive_id' => $archive->id,
                'nomor_surat' => $archive->nomor_surat,
                'text_content_tersimpan' => !empty($archive->isi_dokumen),
                'panjang_text_content' => strlen($archive->isi_dokumen ?? ''),
            ]);
        } catch (\Exception $e) {
            \Log::error('Failed to create archive', ['error' => $e->getMessage()]);
            return redirect()->back()->with('error', 'Gagal menyimpan arsip: ' . $e->getMessage())->withInput();
        }

        // Generate embedding untuk arsip yang baru dibuat
        if (!empty($archive->isi_dokumen) || !empty($archive->nama_arsip)) {
            try {
                $layananEmbedding = new LayananEmbedding();
                if ($layananEmbedding->cekKetersediaan()) {
                    $embedding = $layananEmbedding->generateEmbeddingArsip($archive);
                    
                    if ($embedding) {
                        DB::table('arsip_embeddings')->insert([
                            'arsip_id' => $archive->id,
                            'embedding' => json_encode($embedding),
                            'model' => $layananEmbedding->dapatkanModel(),
                            'dimension' => $layananEmbedding->dapatkanDimensi(),
                            'created_at' => now(),
                            'updated_at' => now(),
                        ]);
                        \Log::info('Embedding created successfully');
                    }
                }
            } catch (\Exception $e) {
                \Log::error('Gagal generate embedding untuk arsip ' . $archive->id . ': ' . $e->getMessage());
            }
        }

        \Log::info('Creating activity log');
        \App\Models\AktivitasLog::create([
            'user_id' => auth()->id(),
            'aktivitas' => 'Tambah Arsip',
            'detail' => "Menambahkan arsip baru: {$archive->nomor_surat} - {$archive->nama_arsip}",
            'ip_address' => request()->ip(),
        ]);
        \Log::info('Activity log created');

        \Log::info('Redirecting to arsip.index with success message');
        return redirect()->route('arsip.index')->with('success', 'Arsip berhasil disimpan!');
    }

    public function edit($id)
    {
        $arsip = Archive::findOrFail($id);
        $locations = Location::orderBy('ruangan')->get();

        return view('arsip.ubah', [
            'arsip' => $arsip,
            'jenis_dokumen_list' => self::JENIS_DOKUMEN,
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
            'jenis_dokumen' => 'required|in:Dokumen Tata Usaha,Dokumen Keimigrasian,Dokumen Pengawasan dan Penindakan',
            'jenis_arsip_id' => 'nullable|exists:klasifikasi,id',
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
            $rules['masa_retensi'] = 'required|in:3 Tahun,5 Tahun,10 Tahun,Permanen';
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
            
            // Ekstrak isi dokumen secara otomatis
            $layananEkstraksi = new LayananEkstraksiDokumen();
            $isiDokumen = $layananEkstraksi->ekstrakIsiDokumen($fileName, $file->getClientOriginalName());
            if ($isiDokumen) {
                $data['isi_dokumen'] = $isiDokumen;
                \Log::info('[Ingest] Ekstraksi PDF berhasil (update)', [
                    'file' => $fileName,
                    'jumlah_karakter' => strlen($isiDokumen),
                    'preview' => substr($isiDokumen, 0, 200),
                ]);
            } else {
                \Log::warning('[Ingest] Ekstraksi PDF gagal atau kosong (update)', [
                    'file' => $fileName,
                    'original_name' => $file->getClientOriginalName(),
                ]);
            }
        }

        if (RetensiService::kolomRetensiTersedia()) {
            $data['tanggal_retensi'] = RetensiService::hitungTanggalRetensi(
                $data['tanggal_arsip'],
                $data['masa_retensi']
            );
            
            // Set status_retensi to Permanen when masa_retensi is Permanen
            if ($data['masa_retensi'] === 'Permanen') {
                $data['status_retensi'] = 'Permanen';
            } else {
                // Recalculate status_retensi for non-permanent
                $data['status_retensi'] = RetensiService::statusRetensi(
                    $data['masa_retensi'],
                    $data['tanggal_retensi']
                );
            }
        } else {
            unset($data['masa_retensi']);
        }

        try {
            $archive->update($data);
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Gagal memperbarui arsip: ' . $e->getMessage())->withInput();
        }

        // Regenerate embedding jika isi dokumen atau nama arsip berubah
        if (!empty($archive->isi_dokumen) || !empty($archive->nama_arsip)) {
            try {
                $layananEmbedding = new LayananEmbedding();
                if ($layananEmbedding->cekKetersediaan()) {
                    $embedding = $layananEmbedding->generateEmbeddingArsip($archive);
                    
                    if ($embedding) {
                        // Update atau insert embedding
                        DB::table('arsip_embeddings')
                            ->updateOrInsert(
                                ['arsip_id' => $archive->id],
                                [
                                    'embedding' => json_encode($embedding),
                                    'model' => $layananEmbedding->dapatkanModel(),
                                    'dimension' => $layananEmbedding->dapatkanDimensi(),
                                    'updated_at' => now(),
                                ]
                            );
                    }
                }
            } catch (\Exception $e) {
                \Log::error('Gagal regenerate embedding untuk arsip ' . $archive->id . ': ' . $e->getMessage());
            }
        }

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
            ->leftJoin('klasifikasi', 'arsip.jenis_arsip_id', '=', 'klasifikasi.id')
            ->leftJoin('lokasi_simpan', 'arsip.lokasi_id', '=', 'lokasi_simpan.id')
            ->leftJoin('lemari', 'arsip.cabinet_id', '=', 'lemari.lemari_id')
            ->leftJoin('rak', 'arsip.rack_id', '=', 'rak.rak_id')
            ->select(
                'arsip.*',
                'klasifikasi.nama as nama_jenis',
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
            ->leftJoin('klasifikasi', 'arsip.jenis_arsip_id', '=', 'klasifikasi.id')
            ->leftJoin('lokasi_simpan', 'arsip.lokasi_id', '=', 'lokasi_simpan.id')
            ->leftJoin('lemari', 'arsip.cabinet_id', '=', 'lemari.lemari_id')
            ->leftJoin('rak', 'arsip.rack_id', '=', 'rak.rak_id')
            ->select(
                'arsip.nomor_surat',
                'arsip.nama_arsip',
                'arsip.perihal_surat',
                'klasifikasi.nama as nama_jenis',
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
                    $q->whereRaw('arsip.nomor_surat like ?', ["%{$search}%"])
                        ->orWhereRaw('arsip.nama_arsip like ?', ["%{$search}%"])
                        ->orWhereRaw('arsip.perihal_surat like ?', ["%{$search}%"]);
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
        $query = DB::table('arsip')
            ->leftJoin('klasifikasi', 'arsip.jenis_arsip_id', '=', 'klasifikasi.id')
            ->leftJoin('lokasi_simpan', 'arsip.lokasi_id', '=', 'lokasi_simpan.id')
            ->leftJoin('lemari', 'arsip.cabinet_id', '=', 'lemari.lemari_id')
            ->leftJoin('rak', 'arsip.rack_id', '=', 'rak.rak_id')
            ->select(
                'arsip.*',
                'klasifikasi.nama as nama_jenis',
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
                    $q->whereRaw('arsip.nomor_surat like ?', ["%{$search}%"])
                        ->orWhereRaw('arsip.nama_arsip like ?', ["%{$search}%"])
                        ->orWhereRaw('arsip.perihal_surat like ?', ["%{$search}%"]);
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

        $pdf = app('dompdf.wrapper');
        $pdf->loadView('arsip.pdf', compact('archives'))
            ->setPaper('a4', 'landscape');

        return $pdf->download('arsip_' . date('Y-m-d_His') . '.pdf');
    }

    public function getFileInfo($id)
    {
        $archive = DB::table('arsip')
            ->leftJoin('klasifikasi', 'arsip.jenis_arsip_id', '=', 'klasifikasi.id')
            ->select(
                'arsip.id',
                'arsip.nomor_surat',
                'arsip.nama_arsip',
                'arsip.file_arsip',
                'arsip.masa_retensi',
                'arsip.tanggal_retensi',
                'arsip.status_retensi',
                'klasifikasi.nama as nama_jenis'
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
            ->leftJoin('klasifikasi', 'arsip.jenis_arsip_id', '=', 'klasifikasi.id')
            ->leftJoin('lokasi_simpan', 'arsip.lokasi_id', '=', 'lokasi_simpan.id')
            ->leftJoin('lemari', 'arsip.cabinet_id', '=', 'lemari.lemari_id')
            ->leftJoin('rak', 'arsip.rack_id', '=', 'rak.rak_id')
            ->select(
                'arsip.*',
                'klasifikasi.nama as nama_jenis',
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
                    $q->whereRaw('arsip.nomor_surat like ?', ["%{$search}%"])
                        ->orWhereRaw('arsip.nama_arsip like ?', ["%{$search}%"])
                        ->orWhereRaw('arsip.perihal_surat like ?', ["%{$search}%"]);
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