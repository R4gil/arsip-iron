<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

#[Fillable(['ruangan', 'lemari', 'rak', 'keterangan'])]
class Location extends Model
{
    use HasFactory;

    protected $table = 'lokasi_simpan';
    public $timestamps = false;

    public function getNamaLokasiAttribute()
    {
        return self::formatLabel(
            $this->ruangan,
            $this->lemari,
            $this->rak,
            $this->keterangan
        );
    }

    public static function formatLabel(?string $ruangan, ?string $lemari = null, ?string $rak = null, ?string $keterangan = null): string
    {
        $room = trim($ruangan ?? '');
        $note = trim($keterangan ?? '');
        $lemari = trim($lemari ?? '');
        $rak = trim($rak ?? '');
        $hasLemariRak = $lemari !== '' && $rak !== '' && $lemari !== '-' && $rak !== '-';

        if ($hasLemariRak) {
            return trim("{$room} / {$lemari} / {$rak}");
        }

        if ($note !== '') {
            return $room !== '' ? "{$room} ({$note})" : $note;
        }

        return $room !== '' ? $room : 'Tanpa nama';
    }

    public static function labelSql(string $alias = 'lokasi_simpan'): string
    {
        return "CASE
            WHEN TRIM({$alias}.lemari) NOT IN ('', '-') AND TRIM({$alias}.rak) NOT IN ('', '-')
                THEN CONCAT({$alias}.ruangan, ' / ', {$alias}.lemari, ' / ', {$alias}.rak)
            WHEN TRIM(COALESCE({$alias}.keterangan, '')) != ''
                THEN CONCAT({$alias}.ruangan, ' (', {$alias}.keterangan, ')')
            ELSE {$alias}.ruangan
        END";
    }

    public function cabinets()
    {
        return $this->hasMany(Cabinet::class, 'ruangarsip_id');
    }

    public function archives()
    {
        return $this->hasMany(Archive::class, 'lokasi_id');
    }
}
