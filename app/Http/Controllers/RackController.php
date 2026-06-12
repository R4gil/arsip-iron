<?php

namespace App\Http\Controllers;

use App\Models\Cabinet;
use App\Models\Rack;
use Illuminate\Http\Request;

class RackController extends Controller
{
    public function index()
    {
        $racks = Rack::with('cabinet.location')->latest()->paginate(15);

        return view('racks.index', compact('racks'));
    }

    public function create()
    {
        $cabinets = Cabinet::with('location')->orderBy('nama_lemari')->get();

        return view('racks.create', compact('cabinets'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'cabinet_id' => 'required|exists:cabinets,id',
            'nama_rak' => 'required|string|max:191',
        ]);

        Rack::create($data);

        return redirect()->route('racks.index')->with('success', 'Rak berhasil ditambahkan.');
    }

    public function edit(Rack $rack)
    {
        $cabinets = Cabinet::with('location')->orderBy('nama_lemari')->get();

        return view('racks.edit', compact('rack', 'cabinets'));
    }

    public function update(Request $request, Rack $rack)
    {
        $data = $request->validate([
            'cabinet_id' => 'required|exists:cabinets,id',
            'nama_rak' => 'required|string|max:191',
        ]);

        $rack->update($data);

        return redirect()->route('racks.index')->with('success', 'Rak berhasil diperbarui.');
    }

    public function destroy(Rack $rack)
    {
        $rack->delete();

        return redirect()->route('racks.index')->with('success', 'Rak berhasil dihapus.');
    }
}
