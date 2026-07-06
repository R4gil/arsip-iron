<?php

namespace App\Http\Controllers;

use App\Models\Archive;
use App\Models\Borrowing;
use App\Models\User;
use Illuminate\Http\Request;

class BorrowingController extends Controller
{
    
public function create()
{
    // Ubah menjadi all() atau hapus where agar semua data tampil
    $archives = \DB::table('arsip')->get(); 

    // Ambil data pengguna
    $users = User::all();

    // Ambil data unit kerja
    $units = \DB::table('users')->select('unit_kerja')->whereNotNull('unit_kerja')->distinct()->get();

    // Kirim ketiganya ke view
    return view('borrowings.create', compact('archives', 'users', 'units'));
}

    public function index(Request $request)
    {
        $query = Borrowing::with(['archive']);

        if ($request->filled('search')) {
            $query->where(function ($q) use ($request) {
                $q->where('nama_peminjam', 'like', "%{$request->search}%")
                  ->orWhereHas('archive', fn($q) => $q->where('nama_arsip', 'like', "%{$request->search}%"));
            });
        }

        $borrowings = $query->latest('tanggal_keluar')->paginate(15)->withQueryString();

        $stats = [
            'dipinjam' => Borrowing::where('status_pinjam', 'Dipinjam')->count(),
            'dikembalikan' => Borrowing::where('status_pinjam', 'Dikembalikan')->count(),
        ];

        $archives = \App\Models\Archive::all();

        return view('borrowings.index', compact('borrowings', 'stats', 'archives'));
    }

    public function update(Request $request, Borrowing $borrowing)
    {
        if ($request->filled('action') && $request->action === 'return') {
            $borrowing->update([
                'status_pinjam'   => 'Dikembalikan', 
                'tanggal_masuk'   => now(),
                'petugas_masuk_id' => auth()->id()
            ]);

            \DB::table('arsip')->where('id', $borrowing->arsip_id)
                ->update(['status_ketersediaan' => 'tersedia']);

            return redirect()->route('borrowings.index')->with('success', 'Arsip berhasil dikembalikan.');
        }

        return redirect()->route('borrowings.index');
    }

    public function destroy(Borrowing $borrowing)
    {
        if ($borrowing->status_pinjam === 'Dipinjam') {
            \DB::table('arsip')->where('id', $borrowing->arsip_id)
                ->update(['status_ketersediaan' => 'tersedia']);
        }

        $borrowing->delete();

        return redirect()->route('borrowings.index')->with('success', 'Transaksi peminjaman berhasil dihapus.');
    }

    // Di dalam BorrowingController.php
    public function store(Request $request)
    {
        // 1. Simpan data peminjaman
        // 2. Update status arsip menjadi 'Dipinjam'
        $arsip = Archive::find($request->arsip_id);
        $arsip->update(['status_ketersediaan' => 'Dipinjam']);

        return redirect()->back()->with('success', 'Arsip berhasil dipinjam!');
    }

    public function return($id)
    {
        // 1. Update status arsip menjadi 'Tersedia'
        $arsip = Archive::find($id);
        $arsip->update(['status_ketersediaan' => 'Tersedia']);

        return redirect()->back()->with('success', 'Arsip berhasil dikembalikan!');
    }
}