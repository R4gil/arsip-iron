<?php

namespace App\Http\Controllers;

use App\Models\Cabinet;
use App\Models\Location;
use Illuminate\Http\Request;

class CabinetController extends Controller
{
    public function index()
    {
        $cabinets = Cabinet::with('location')->latest()->paginate(15);

        return view('cabinets.index', compact('cabinets'));
    }

    public function create()
    {
        $locations = Location::orderBy('nama_lokasi')->get();

        return view('cabinets.create', compact('locations'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'location_id' => 'required|exists:locations,id',
            'nama_lemari' => 'required|string|max:191',
        ]);

        Cabinet::create($data);

        return redirect()->route('cabinets.index')->with('success', 'Lemari berhasil ditambahkan.');
    }

    public function edit(Cabinet $cabinet)
    {
        $locations = Location::orderBy('nama_lokasi')->get();

        return view('cabinets.edit', compact('cabinet', 'locations'));
    }

    public function update(Request $request, Cabinet $cabinet)
    {
        $data = $request->validate([
            'location_id' => 'required|exists:locations,id',
            'nama_lemari' => 'required|string|max:191',
        ]);

        $cabinet->update($data);

        return redirect()->route('cabinets.index')->with('success', 'Lemari berhasil diperbarui.');
    }

    public function destroy(Cabinet $cabinet)
    {
        $cabinet->delete();

        return redirect()->route('cabinets.index')->with('success', 'Lemari berhasil dihapus.');
    }

    public function racks(Location $location)
    {
        return response()->json($location->cabinets()->select('id', 'nama_lemari')->get());
    }
}
