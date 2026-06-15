<?php

namespace App\Http\Controllers;

use App\Http\Requests\BorrowingStoreRequest;
use App\Models\Archive;
use App\Models\Borrowing;
use App\Models\User;
use Illuminate\Http\Request;

class BorrowingController extends Controller
{
    public function index(Request $request)
    {
        $query = Borrowing::with(['archive', 'user']);

        if ($request->filled('search')) {
            $query->where(function ($q) use ($request) {
                $q->where('nama_peminjam', 'like', "%{$request->search}%")
                    ->orWhereHas('archive', fn ($q) => $q->where('nomor_arsip', 'like', "%{$request->search}%")
                        ->orWhere('nama_arsip', 'like', "%{$request->search}%"));
            });
        }

        $borrowings = $query->latest('tanggal_pinjam')->paginate(15)->withQueryString();

        $stats = [
            'dipinjam' => Borrowing::where('status_pinjam', 'dipinjam')->count(),
            'dikembalikan' => Borrowing::where('status_pinjam', 'dikembalikan')->count(),
            'terlambat' => Borrowing::where('status_pinjam', 'terlambat')->count(),
        ];

        return view('borrowings.index', compact('borrowings', 'stats'));
    }

    public function create()
    {
        
        $archives = \DB::table('arsip')
             ->where('status_ketersediaan', 'tersedia')
             ->orderBy('nama_arsip') // <-- Jika nama kolomnya bukan 'nama_arsip', ganti dengan kolom yang sesuai di tabel kamu (misalnya 'id')
             ->get();

        $users = \App\Models\User::orderBy('username')->get();

        return view('borrowings.create', compact('archives', 'users'));
    }

    public function store(BorrowingStoreRequest $request)
    {
        $borrowing = Borrowing::create(array_merge($request->validated(), [
            'id' => auth()->id(),
            'status_pinjam' => 'dipinjam',
        ]));

        $borrowing->archive->update(['status_pinjam' => 'dipinjam']);

        return redirect()->route('borrowings.index')->with('success', 'Peminjaman arsip berhasil disimpan.');
    }

    public function show(Borrowing $borrowing)
    {
        return view('borrowings.show', compact('borrowing'));
    }

    public function edit(Borrowing $borrowing)
    {
        $archives = Archive::where(function ($query) use ($borrowing) {
            $query->where('status_pinjam', 'tersedia')->orWhere('id', $borrowing->archive_id);
        })->orderBy('nama_arsip')->get();
        $users = User::orderBy('username')->get();

        return view('borrowings.edit', compact('borrowing', 'archives', 'users'));
    }

    public function update(Request $request, Borrowing $borrowing)
    {
        if ($request->filled('action') && $request->action === 'return') {
            $borrowing->update(['status_pinjam' => 'dikembalikan', 'tanggal_kembali' => now()->toDateString()]);
            $borrowing->archive->update(['status_pinjam' => 'tersedia']);

            return redirect()->route('borrowings.index')->with('success', 'Arsip berhasil dikembalikan.');
        }

        return redirect()->route('borrowings.index');
    }

    public function destroy(Borrowing $borrowing)
    {
        if ($borrowing->status_pinjam === 'dipinjam') {
            $borrowing->archive->update(['status_pinjam' => 'tersedia']);
        }

        $borrowing->delete();

        return redirect()->route('borrowings.index')->with('success', 'Transaksi peminjaman berhasil dihapus.');
    }
}
