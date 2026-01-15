<?php
declare(strict_types=1);

namespace App\Services;

use App\Models\MunicipioIbge;
use App\Support\Normalizer;

final class MatcherService
{
    /** @var array<string, MunicipioIbge[]> */
    private array $index = [];

    /** @param MunicipioIbge[] $municipios */
    public function buildIndex(array $municipios): void
    {
        foreach ($municipios as $m) {
            $this->index[$m->key][] = $m;
        }
    }

    /** @param MunicipioIbge[] $municipios */
    public function match(string $input, array $municipios): array
    {
        $key = Normalizer::name($input);

        // exato
        if (isset($this->index[$key])) {
            $list = $this->index[$key];
            if (count($list) === 1) return ['status' => 'OK', 'municipio' => $list[0]];
            return ['status' => 'AMBIGUO', 'municipio' => $list[0]];
        }

        // fuzzy (Levenshtein)
        $best = null;
        $bestDist = PHP_INT_MAX;

        foreach ($municipios as $m) {
            $d = levenshtein($key, $m->key);
            if ($d < $bestDist) {
                $bestDist = $d;
                $best = $m;
            }
        }

        $len = max(strlen($key), 1);
        $limit = ($len <= 8) ? 2 : (($len <= 14) ? 3 : 4);

        if ($best && $bestDist <= $limit) {
            return ['status' => 'OK', 'municipio' => $best];
        }

        return ['status' => 'NAO_ENCONTRADO', 'municipio' => null];
    }
}
