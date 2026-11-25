<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Guarded;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

#[Guarded([])]
class SanctionRule extends Model
{
    use HasUuids;

    protected function casts(): array
    {
        return [
            'threshold_count' => 'integer',
            'default_deduction_amount' => 'decimal:2',
            'is_active' => 'boolean',
        ];
    }

    public function employeeSanctions(): HasMany
    {
        return $this->hasMany(EmployeeSanction::class);
    }

    public static function levelLabel(string $level): string
    {
        return match ($level) {
            'TEGURAN_LISAN' => 'Teguran Lisan',
            'SP1' => 'Surat Peringatan 1 (SP1)',
            'SP2' => 'Surat Peringatan 2 (SP2)',
            'SP3' => 'Surat Peringatan 3 (SP3)',
            default => $level,
        };
    }

    public static function categoryLabel(string $category): string
    {
        return match ($category) {
            'DISIPLIN_WAKTU' => 'Kedisiplinan Jam Kerja',
            'KEHADIRAN' => 'Kehadiran & Presensi',
            'KINERJA_TUGAS' => 'Kinerja & Laporan Tugas',
            'ETIKA_SOP' => 'Etika & Kepatuhan SOP',
            default => $category,
        };
    }
}
