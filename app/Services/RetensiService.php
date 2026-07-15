<?php

namespace App\Services;

use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class RetensiService
{
    public const OPSI_MASA_RETENSI = ['3 Tahun', '5 Tahun', '10 Tahun', 'Permanen'];

    public static function kolomRetensiTersedia(): bool
    {
        return Schema::hasTable('arsip')
            && Schema::hasColumn('arsip', 'masa_retensi')
            && Schema::hasColumn('arsip', 'tanggal_retensi')
            && Schema::hasColumn('arsip', 'status_retensi');
    }

    public static function hitungTanggalRetensi(string $tanggalArsip, string $masaRetensi): ?string
    {
        if ($masaRetensi === 'Permanen') {
            return null;
        }

        $tahun = match ($masaRetensi) {
            '3 Tahun' => 3,
            '5 Tahun' => 5,
            '10 Tahun' => 10,
            default => null,
        };

        if ($tahun === null) {
            return null;
        }

        return Carbon::parse($tanggalArsip)->addYears($tahun)->format('Y-m-d');
    }

    public static function statusRetensi(?string $masaRetensi, ?string $tanggalRetensi): string
    {
        if ($masaRetensi === 'Permanen') {
            return 'Permanen';
        }

        if (!$masaRetensi || !$tanggalRetensi) {
            return 'Belum Memasuki Masa Retensi';
        }

        return Carbon::parse($tanggalRetensi)->lte(Carbon::today())
            ? 'Masuk Masa Retensi'
            : 'Belum Memasuki Masa Retensi';
    }

    public static function countSudahRetensi(): int
    {
        try {
            if (!self::kolomRetensiTersedia()) {
                return 0;
            }

            return DB::table('arsip')
                ->whereNotNull('masa_retensi')
                ->where('masa_retensi', '!=', 'Permanen')
                ->whereNotNull('tanggal_retensi')
                ->whereDate('tanggal_retensi', '<=', Carbon::today())
                ->count();
        } catch (\Exception $e) {
            return 0;
        }
    }

    public static function countNotifikasiBaru(): int
    {
        try {
            if (!self::kolomRetensiTersedia()) {
                return 0;
            }

            $lastRead = auth()->user()->last_read_retensi ?? now()->subYear();

            return DB::table('arsip')
                ->whereNotNull('masa_retensi')
                ->where('masa_retensi', '!=', 'Permanen')
                ->whereNotNull('tanggal_retensi')
                ->whereDate('tanggal_retensi', '<=', Carbon::today())
                ->where('created_at', '>', $lastRead)
                ->count();
        } catch (\Exception $e) {
            return 0;
        }
    }

    public static function getNotifikasiTerbaru(int $limit = 5)
    {
        try {
            if (!self::kolomRetensiTersedia()) {
                return collect();
            }

            $lastRead = auth()->user()->last_read_retensi ?? now()->subYear();

            $query = DB::table('arsip')
                ->whereNotNull('masa_retensi')
                ->where('masa_retensi', '!=', 'Permanen')
                ->whereNotNull('tanggal_retensi')
                ->whereDate('tanggal_retensi', '<=', Carbon::today())
                ->orderByDesc('tanggal_retensi')
                ->limit($limit);

            if ($lastRead) {
                $query->where('created_at', '>', $lastRead);
            }

            return $query->get();
        } catch (\Exception $e) {
            return collect();
        }
    }

    public static function markNotifikasiDibaca(): void
    {
        try {
            $user = auth()->user();
            if ($user) {
                $user->last_read_retensi = now();
                $user->save();
            }
        } catch (\Exception $e) {
            // silent
        }
    }

    public static function countPermanen(): int
    {
        try {
            if (!self::kolomRetensiTersedia()) {
                return 0;
            }

            return DB::table('arsip')
                ->where('masa_retensi', 'Permanen')
                ->count();
        } catch (\Exception $e) {
            return 0;
        }
    }
}