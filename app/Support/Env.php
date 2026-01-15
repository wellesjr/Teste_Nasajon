<?php
declare(strict_types=1);

namespace App\Support;

final class Env
{
    public static function load(string $path): void
    {
        if (!file_exists($path)) return;

        $lines = file($path, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
        if (!$lines) return;

        foreach ($lines as $line) {
            if (str_starts_with(trim($line), '#')) continue;

            [$key, $value] = array_pad(explode('=', $line, 2), 2, null);
            if ($key && $value !== null) {
                $_ENV[$key] = $value;
            }
        }
    }

    public static function get(string $key): ?string
    {
        return $_ENV[$key] ?? null;
    }
}