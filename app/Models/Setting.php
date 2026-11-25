<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Guarded;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;

#[Guarded([])]
class Setting extends Model
{
    use HasUuids;

    protected static ?array $brandCache = null;

    public static function getValue(string $key, $default = null)
    {
        $setting = self::where('key', $key)->first();
        return $setting ? $setting->value : $default;
    }

    public static function setValue(string $key, $value): void
    {
        self::updateOrCreate(['key' => $key], ['value' => (string) $value]);
        self::clearBrandCache();
    }

    public static function getBrandSettings(): array
    {
        if (self::$brandCache !== null) {
            return self::$brandCache;
        }

        try {
            $rows = self::whereIn('key', ['app_name', 'app_slogan', 'app_logo_path', 'office_name'])
                ->pluck('value', 'key')
                ->toArray();
        } catch (\Throwable $e) {
            $rows = [];
        }

        $logoPath = !empty($rows['app_logo_path']) ? $rows['app_logo_path'] : null;

        return self::$brandCache = [
            'name' => !empty($rows['app_name']) ? $rows['app_name'] : 'ChronoHR',
            'slogan' => !empty($rows['app_slogan']) ? $rows['app_slogan'] : 'Presisi Waktu & Penggajian',
            'logo_path' => $logoPath,
            'logo_url' => $logoPath ? asset($logoPath) : null,
            'office_name' => !empty($rows['office_name']) ? $rows['office_name'] : 'Kantor Pusat ChronoHR',
        ];
    }

    public static function clearBrandCache(): void
    {
        self::$brandCache = null;
    }
}
