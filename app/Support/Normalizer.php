<?php
declare(strict_types=1);

namespace App\Support;

final class Normalizer
{
    public static function name(string $s): string
    {
        $s = trim(mb_strtolower($s, 'UTF-8'));

        $conv = @iconv('UTF-8', 'ASCII//TRANSLIT//IGNORE', $s);
        if ($conv !== false) $s = $conv;

        $s = preg_replace('/[^a-z0-9\s]/', '', $s) ?? $s;
        $s = preg_replace('/\s+/', '', $s) ?? $s;

        return trim($s);
    }
}
