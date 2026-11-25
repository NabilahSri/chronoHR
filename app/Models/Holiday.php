<?php

namespace App\Models;

use Carbon\Carbon;
use Carbon\CarbonPeriod;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Holiday extends Model
{
    use HasFactory, HasUuids;

    protected $fillable = [
        'date',
        'name',
        'type',
    ];

    protected function casts(): array
    {
        return [
            'date' => 'date',
        ];
    }

    /**
     * Hitung jumlah hari kerja efektif (Senin s/d Jumat dikurangi hari libur pada hari kerja).
     */
    public static function calculateEffectiveWorkingDays(int $month, int $year): int
    {
        $start = Carbon::createFromDate($year, $month, 1)->startOfMonth();
        $end = $start->copy()->endOfMonth();

        $holidays = self::whereYear('date', $year)
            ->whereMonth('date', $month)
            ->pluck('date')
            ->map(fn($d) => Carbon::parse($d)->toDateString())
            ->toArray();

        $workingDays = 0;
        $period = CarbonPeriod::create($start, $end);

        foreach ($period as $day) {
            if ($day->isWeekday() && !in_array($day->toDateString(), $holidays, true)) {
                $workingDays++;
            }
        }

        return max(1, $workingDays);
    }
}
