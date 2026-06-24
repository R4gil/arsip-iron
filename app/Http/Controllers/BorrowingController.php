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
        // Ambil data arsip
        $archives = \DB::table('arsip')->where('status_ketersediaan', 'tersedia')->get();

        // Ambil data pengguna
        $users = User::all();

        // TAMBAHKAN INI: Ambil data unit kerja
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
            'terlambat' => Borrowing::where('status_pinjam', 'Terlambat')->count(),
        ];

        return view('borrowings.index', compact('borrowings', 'stats'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'arsip_id'        => 'required|exists:arsip,id',
            'nama_peminjam'   => 'required|string|max:150',
            'divisi_peminjam' => 'required|string|max:100',
            'tanggal_keluar'  => 'required|date',
        ]);
        
        Borrowing::create(array_merge($validated, [
            'petugas_keluar_id' => auth()->id(),
            'status_pinjam'     => 'Dipinjam',
        ]));

        // Update status di tabel arsip
        \DB::table('arsip')->where('id', $request->arsip_id)
            ->update(['status_ketersediaan' => 'dipinjam']);

        return redirect()->route('borrowings.index')->with('success', 'Peminjaman arsip berhasil disimpan.');
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
}