<?php

namespace App\Http\Controllers;

use App\Models\Classification;
use Illuminate\Http\Request;

class ClassificationController extends Controller
{
    public function index()
    {
        $classifications = Classification::latest()->paginate(15);

        return view('classifications.index', compact('classifications'));
    }

    public function create()
    {
        return view('classifications.create');
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'kode' => 'required|string|max:50|unique:classifications,kode',
            'nama' => 'required|string|max:191',
        ]);

        Classification::create($data);

        return redirect()->route('classifications.index')->with('success', 'Klasifikasi arsip berhasil ditambahkan.');
    }

    public function edit(Classification $classification)
    {
        return view('classifications.edit', compact('classification'));
    }

    public function update(Request $request, Classification $classification)
    {
        $data = $request->validate([
            'kode' => 'required|string|max:50|unique:classifications,kode,' . $classification->id,
            'nama' => 'required|string|max:191',
        ]);

        $classification->update($data);

        return redirect()->route('classifications.index')->with('success', 'Klasifikasi arsip berhasil diperbarui.');
    }

    public function destroy(Classification $classification)
    {
        $classification->delete();

        return redirect()->route('classifications.index')->with('success', 'Klasifikasi arsip berhasil dihapus.');
    }
}
