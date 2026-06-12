<?php

namespace App\Http\Controllers;

use App\Models\Location;
use Illuminate\Http\Request;

class LocationController extends Controller
{
    public function index()
    {
        $locations = Location::latest()->paginate(15);

        return view('locations.index', compact('locations'));
    }

    public function create()
    {
        return view('locations.create');
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'nama_lokasi' => 'required|string|max:191',
            'keterangan' => 'nullable|string',
        ]);

        Location::create($data);

        return redirect()->route('locations.index')->with('success', 'Lokasi arsip berhasil ditambahkan.');
    }

    public function edit(Location $location)
    {
        return view('locations.edit', compact('location'));
    }

    public function update(Request $request, Location $location)
    {
        $data = $request->validate([
            'nama_lokasi' => 'required|string|max:191',
            'keterangan' => 'nullable|string',
        ]);

        $location->update($data);

        return redirect()->route('locations.index')->with('success', 'Lokasi arsip berhasil diperbarui.');
    }

    public function destroy(Location $location)
    {
        $location->delete();

        return redirect()->route('locations.index')->with('success', 'Lokasi arsip berhasil dihapus.');
    }
}
