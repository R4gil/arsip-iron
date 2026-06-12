<?php

namespace App\Http\Controllers;

use App\Http\Requests\ArchiveStoreRequest;
use App\Http\Requests\ArchiveUpdateRequest;
use App\Models\Archive;
use App\Models\Cabinet;
use App\Models\Classification;
use App\Models\Location;
use App\Models\Rack;
use Illuminate\Http\Request;

class ArchiveController extends Controller
{
    public function index(Request $request)
    {
        $query = Archive::with(['classification', 'location', 'cabinet', 'rack']);

        if ($request->filled('search')) {
            $query->where(function ($q) use ($request) {
                $q->where('nomor_arsip', 'like', "%{$request->search}%")
                    ->orWhere('nama_arsip', 'like', "%{$request->search}%")
                    ->orWhereHas('classification', fn ($q) => $q->where('nama', 'like', "%{$request->search}%"))
                    ->orWhereHas('location', fn ($q) => $q->where('nama_lokasi', 'like', "%{$request->search}%"));
            });
        }

        if ($request->filled('location_id')) {
            $query->where('location_id', $request->location_id);
        }

        if ($request->filled('tahun')) {
            $query->where('tahun', $request->tahun);
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        $archives = $query->latest('id')->paginate(15)->withQueryString();
        $locations = Location::orderBy('nama_lokasi')->get();
        $years = Archive::select('tahun')->distinct()->orderByDesc('tahun')->pluck('tahun');

        return view('archives.index', compact('archives', 'locations', 'years'));
    }

    public function create()
    {
        $locations = Location::orderBy('nama_lokasi')->get();
        $classifications = Classification::orderBy('kode')->get();
        $cabinets = Cabinet::orderBy('nama_lemari')->get();
        $racks = Rack::orderBy('nama_rak')->get();

        return view('archives.create', compact('locations', 'classifications', 'cabinets', 'racks'));
    }

    public function store(ArchiveStoreRequest $request)
    {
        Archive::create($request->validated());

        return redirect()->route('arsip.index')->with('success', 'Arsip berhasil ditambahkan.');
    }

    public function show(Archive $archive)
    {
        $archive->load(['classification', 'location', 'cabinet', 'rack', 'borrowings']);

        return view('archives.show', compact('archive'));
    }

    public function edit(Archive $archive)
    {
        $locations = Location::orderBy('nama_lokasi')->get();
        $classifications = Classification::orderBy('kode')->get();
        $cabinets = Cabinet::orderBy('nama_lemari')->get();
        $racks = Rack::orderBy('nama_rak')->get();

        return view('archives.edit', compact('archive', 'locations', 'classifications', 'cabinets', 'racks'));
    }

    public function update(ArchiveUpdateRequest $request, Archive $archive)
    {
        $archive->update($request->validated());

        return redirect()->route('arsip.index')->with('success', 'Arsip berhasil diperbarui.');
    }

    public function destroy(Archive $archive)
    {
        $archive->delete();

        return redirect()->route('arsip.index')->with('success', 'Arsip berhasil dihapus.');
    }
}
