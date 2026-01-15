<?php
declare(strict_types=1);

namespace App\Services;

final class StatsService
{
    public function compute(array $resultadoRows): array
    {
        $stats = [
            'total_municipios' => count($resultadoRows),
            'total_ok' => 0,
            'total_nao_encontrado' => 0,
            'total_erro_api' => 0,
            'pop_total_ok' => 0,
            'medias_por_regiao' => [],
        ];

        $sum = [];
        $cnt = [];

        foreach ($resultadoRows as $r) {
            $status = $r['status'] ?? '';
            if ($status === 'OK') {
                $stats['total_ok']++;
                $pop = (int)($r['populacao_input'] ?? 0);
                $stats['pop_total_ok'] += $pop;

                $reg = (string)($r['regiao'] ?? '');
                if ($reg !== '') {
                    $sum[$reg] = ($sum[$reg] ?? 0) + $pop;
                    $cnt[$reg] = ($cnt[$reg] ?? 0) + 1;
                }
            } elseif ($status === 'NAO_ENCONTRADO' || $status === 'AMBIGUO') {
                $stats['total_nao_encontrado']++;
            } elseif ($status === 'ERRO_API') {
                $stats['total_erro_api']++;
            }
        }

        foreach ($sum as $reg => $s) {
            $stats['medias_por_regiao'][$reg] = round($s / $cnt[$reg], 2);
        }

        return $stats;
    }
}
