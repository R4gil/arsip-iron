<?php

namespace App\Services;

use App\Models\AktivitasLog;
use App\Models\NotifikasiDibaca;
use App\Models\Peminjaman;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Schema;

class NotifikasiService
{
    public static function getForUser(?User $user): Collection
    {
        if (!$user) {
            return collect();
        }

        $dismissed = self::getDismissedKeys($user->id);
        $notifications = collect();

        self::tambahNotifikasiRetensi($notifications, $dismissed);
        self::tambahNotifikasiPeminjaman($notifications, $dismissed);
        self::tambahNotifikasiAktivitas($notifications, $dismissed);

        return $notifications->sortByDesc('waktu')->values();
    }

    public static function countForUser(?User $user): int
    {
        return self::getForUser($user)->count();
    }

    public static function getAllCurrentKeys(?User $user): array
    {
        return self::getForUser($user)->pluck('key')->all();
    }

    public static function dismiss(int $userId, string $key): void
    {
        if (!Schema::hasTable('notifikasi_dibaca')) {
            return;
        }

        NotifikasiDibaca::updateOrCreate(
            ['user_id' => $userId, 'notification_key' => $key],
            ['dismissed_at' => now()]
        );
    }

    public static function dismissMany(int $userId, array $keys): void
    {
        foreach ($keys as $key) {
            self::dismiss($userId, (string) $key);
        }
    }

    private static function getDismissedKeys(int $userId): Collection
    {
        if (!Schema::hasTable('notifikasi_dibaca')) {
            return collect();
        }

        return NotifikasiDibaca::where('user_id', $userId)->pluck('notification_key');
    }

    private static function tambahNotifikasiRetensi(Collection $notifications, Collection $dismissed): void
    {
        $key = 'retensi-masuk';
        if ($dismissed->contains($key)) {
            return;
        }

        $jumlah = RetensiService::countSudahRetensi();
        if ($jumlah <= 0) {
            return;
        }

        $notifications->push([
            'key' => $key,
            'tipe' => 'retensi',
            'ikon' => 'fa-hourglass-half',
            'warna' => '#dc2626',
            'judul' => 'Arsip Masuk Masa Retensi',
            'pesan' => "{$jumlah} arsip sudah memasuki masa retensi dan perlu ditinjau.",
            'url' => route('retensi.index'),
            'waktu' => now()->timestamp,
            'waktu_label' => 'Perlu tindakan',
        ]);
    }

    private static function tambahNotifikasiPeminjaman(Collection $notifications, Collection $dismissed): void
    {
        $aktif = Peminjaman::with('archive')
            ->where('status_pinjam', 'Dipinjam')
            ->latest('tanggal_keluar')
            ->limit(10)
            ->get();

        foreach ($aktif as $pinjam) {
            $key = 'peminjaman-' . $pinjam->id;
            if ($dismissed->contains($key)) {
                continue;
            }

            $namaArsip = $pinjam->archive->nama_arsip ?? 'Arsip';
            $nomor = $pinjam->archive->nomor_surat ?? '-';

            $notifications->push([
                'key' => $key,
                'tipe' => 'peminjaman',
                'ikon' => 'fa-book',
                'warna' => '#ea580c',
                'judul' => 'Arsip Sedang Dipinjam',
                'pesan' => "{$nomor} — {$namaArsip} dipinjam oleh {$pinjam->nama_peminjam}.",
                'url' => route('peminjaman.create'),
                'waktu' => $pinjam->tanggal_keluar ? Carbon::parse($pinjam->tanggal_keluar)->timestamp : now()->timestamp,
                'waktu_label' => $pinjam->tanggal_keluar
                    ? Carbon::parse($pinjam->tanggal_keluar)->diffForHumans()
                    : 'Baru saja',
            ]);
        }
    }

    private static function tambahNotifikasiAktivitas(Collection $notifications, Collection $dismissed): void
    {
        if (!Schema::hasTable('aktivitas_log')) {
            return;
        }

        $jenisAktivitas = [
            'Pinjam Arsip',
            'Kembalikan Arsip',
            'Tambah Arsip',
            'Edit Arsip',
            'Hapus Arsip',
            'Peminjaman Arsip',
            'Pengembalian Arsip',
            'Login',
            'Logout',
            'Clear Log',
        ];

        $logs = AktivitasLog::with('user')
            ->whereIn('aktivitas', $jenisAktivitas)
            ->where('created_at', '>=', now()->subDays(3))
            ->latest()
            ->limit(12)
            ->get();

        foreach ($logs as $log) {
            $key = 'aktivitas-' . $log->id;
            if ($dismissed->contains($key)) {
                continue;
            }

            $meta = self::metaAktivitas($log->aktivitas);

            $notifications->push([
                'key' => $key,
                'tipe' => 'aktivitas',
                'ikon' => $meta['ikon'],
                'warna' => $meta['warna'],
                'judul' => $meta['judul'],
                'pesan' => self::ringkasPesan($log->detail ?: $log->aktivitas),
                'url' => route('activity-log.index'),
                'waktu' => $log->created_at->timestamp,
                'waktu_label' => $log->created_at->diffForHumans(),
            ]);
        }
    }

    private static function metaAktivitas(string $aktivitas): array
    {
        return match ($aktivitas) {
            'Pinjam Arsip', 'Peminjaman Arsip' => [
                'judul' => 'Peminjaman Arsip',
                'ikon' => 'fa-arrow-circle-right',
                'warna' => '#2563eb',
            ],
            'Kembalikan Arsip', 'Pengembalian Arsip' => [
                'judul' => 'Pengembalian Arsip',
                'ikon' => 'fa-undo',
                'warna' => '#059669',
            ],
            'Tambah Arsip' => [
                'judul' => 'Arsip Baru Ditambahkan',
                'ikon' => 'fa-plus-circle',
                'warna' => '#7c3aed',
            ],
            'Edit Arsip' => [
                'judul' => 'Arsip Diperbarui',
                'ikon' => 'fa-edit',
                'warna' => '#d97706',
            ],
            'Hapus Arsip' => [
                'judul' => 'Arsip Dihapus',
                'ikon' => 'fa-trash',
                'warna' => '#dc2626',
            ],
            'Clear Log' => [
                'judul' => 'Log Aktivitas Dibersihkan',
                'ikon' => 'fa-broom',
                'warna' => '#64748b',
            ],
            default => [
                'judul' => $aktivitas,
                'ikon' => 'fa-bell',
                'warna' => '#475569',
            ],
        };
    }

    private static function ringkasPesan(?string $teks): string
    {
        if (!$teks) {
            return 'Aktivitas sistem tercatat.';
        }

        if (str_starts_with($teks, '{')) {
            $data = json_decode($teks, true);
            if (is_array($data) && isset($data['input']['nama_arsip'])) {
                return 'Arsip: ' . $data['input']['nama_arsip'];
            }
        }

        return mb_strlen($teks) > 90 ? mb_substr($teks, 0, 87) . '...' : $teks;
    }
}
