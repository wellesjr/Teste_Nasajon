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

        if (isset($this->index[$key])) {
            $list = $this->index[$key];

            if (count($list) === 1) {
                return ['status' => 'OK', 'municipio' => $list[0]];
            }

            foreach ($list as $cand) {
                if (($cand->uf ?? '') === 'SP') {
                    return ['status' => 'OK', 'municipio' => $cand];
                }
            }

            return ['status' => 'NAO_ENCONTRADO', 'municipio' => null];
        }

        $best = null;
        $bestDist = PHP_INT_MAX;
        $secondBestDist = PHP_INT_MAX;
        $candidates = [];

        foreach ($municipios as $m) {
            $d = levenshtein($key, $m->key);
            if ($d < $bestDist) {
                $secondBestDist = $bestDist;
                $bestDist = $d;
                $best = $m;
                $candidates = [$m];
            } elseif ($d === $bestDist) {
                $candidates[] = $m;
            } elseif ($d < $secondBestDist) {
                $secondBestDist = $d;
            }
        }

        $len = max(strlen($key), 1);
        $limit = 0;

        if (!$best || $bestDist > $limit) {
            return ['status' => 'NAO_ENCONTRADO', 'municipio' => null];
        }

        if (count($candidates) > 1) {
            foreach ($candidates as $cand) {
                if (($cand->uf ?? '') === 'SP') {
                    return ['status' => 'OK', 'municipio' => $cand];
                }
            }
            return ['status' => 'NAO_ENCONTRADO', 'municipio' => null];
        }

        return ['status' => 'OK', 'municipio' => $best];
    }
}
