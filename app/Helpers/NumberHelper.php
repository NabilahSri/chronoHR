<?php

namespace App\Helpers;

class NumberHelper
{
    /**
     * Konversi angka nominal uang ke kalimat terbilang bahasa Indonesia.
     * Contoh: 7500000 -> Tujuh Juta Lima Ratus Ribu Rupiah
     */
    public static function terbilang(float|int $number): string
    {
        $number = (int) abs($number);

        if ($number === 0) {
            return 'Nol Rupiah';
        }

        $words = ['', 'Satu', 'Dua', 'Tiga', 'Empat', 'Lima', 'Enam', 'Tujuh', 'Delapan', 'Sembilan', 'Sepuluh', 'Sebelas'];
        $temp = '';

        if ($number < 12) {
            $temp = ' ' . $words[$number];
        } elseif ($number < 20) {
            $temp = self::terbilangRaw($number - 10) . ' Belas';
        } elseif ($number < 100) {
            $temp = self::terbilangRaw((int) ($number / 10)) . ' Puluh ' . self::terbilangRaw($number % 10);
        } elseif ($number < 200) {
            $temp = ' Seratus ' . self::terbilangRaw($number - 100);
        } elseif ($number < 1000) {
            $temp = self::terbilangRaw((int) ($number / 100)) . ' Ratus ' . self::terbilangRaw($number % 100);
        } elseif ($number < 2000) {
            $temp = ' Seribu ' . self::terbilangRaw($number - 1000);
        } elseif ($number < 1000000) {
            $temp = self::terbilangRaw((int) ($number / 1000)) . ' Ribu ' . self::terbilangRaw($number % 1000);
        } elseif ($number < 1000000000) {
            $temp = self::terbilangRaw((int) ($number / 1000000)) . ' Juta ' . self::terbilangRaw($number % 1000000);
        } elseif ($number < 1000000000000) {
            $temp = self::terbilangRaw((int) ($number / 1000000000)) . ' Miliar ' . self::terbilangRaw(fmod($number, 1000000000));
        } else {
            $temp = self::terbilangRaw((int) ($number / 1000000000000)) . ' Triliun ' . self::terbilangRaw(fmod($number, 1000000000000));
        }

        $result = preg_replace('/\s+/', ' ', trim($temp));
        return $result . ' Rupiah';
    }

    private static function terbilangRaw(int $number): string
    {
        if ($number === 0) {
            return '';
        }

        $words = ['', 'Satu', 'Dua', 'Tiga', 'Empat', 'Lima', 'Enam', 'Tujuh', 'Delapan', 'Sembilan', 'Sepuluh', 'Sebelas'];

        if ($number < 12) {
            return $words[$number];
        }
        if ($number < 20) {
            return self::terbilangRaw($number - 10) . ' Belas';
        }
        if ($number < 100) {
            return self::terbilangRaw((int) ($number / 10)) . ' Puluh ' . self::terbilangRaw($number % 10);
        }
        if ($number < 200) {
            return 'Seratus ' . self::terbilangRaw($number - 100);
        }
        if ($number < 1000) {
            return self::terbilangRaw((int) ($number / 100)) . ' Ratus ' . self::terbilangRaw($number % 100);
        }
        if ($number < 2000) {
            return 'Seribu ' . self::terbilangRaw($number - 1000);
        }
        if ($number < 1000000) {
            return self::terbilangRaw((int) ($number / 1000)) . ' Ribu ' . self::terbilangRaw($number % 1000);
        }
        if ($number < 1000000000) {
            return self::terbilangRaw((int) ($number / 1000000)) . ' Juta ' . self::terbilangRaw($number % 1000000);
        }
        if ($number < 1000000000000) {
            return self::terbilangRaw((int) ($number / 1000000000)) . ' Miliar ' . self::terbilangRaw(fmod($number, 1000000000));
        }

        return self::terbilangRaw((int) ($number / 1000000000000)) . ' Triliun ' . self::terbilangRaw(fmod($number, 1000000000000));
    }
}
