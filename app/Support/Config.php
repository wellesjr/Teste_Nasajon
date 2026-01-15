<?php
declare(strict_types=1);

namespace App\Support;

final class Config
{
    public const IBGE_URL = 'https://servicodados.ibge.gov.br/api/v1/localidades/municipios';
    public const SUBMIT_URL = 'https://mynxlubykylncinttggu.functions.supabase.co/ibge-submit';

    public static function inputCsv(): string
    {
        return __DIR__ . '/../../storage/input.csv';
    }

    public static function outputCsv(): string
    {
        return __DIR__ . '/../../storage/resultado.csv';
    }

    public static function cacheFile(): string
    {
        return __DIR__ . '/../../storage/cache_municipios.json';
    }

    public static function accessToken(?string $cliToken = null): ?string
    {
        if ($cliToken) return $cliToken;
        $env = getenv('ACCESS_TOKEN');
        return $env !== false ? $env : null;
    }
}
