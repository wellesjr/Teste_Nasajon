<?php
declare(strict_types=1);

namespace App\Services;

final class StatsService
{
    /**
     * Calcula estatísticas sobre os resultados do processamento de municípios.
     *
     * Este método processa um array de resultados de municípios e gera estatísticas
     * agregadas, incluindo totais por status, população total e médias populacionais
     * por região.
     *
     * @param array $resultadoRows Array de resultados contendo informações dos municípios.
     *                            Cada elemento deve conter as chaves:
     *                            - 'status': Status do processamento ('OK', 'NAO_ENCONTRADO', 'AMBIGUO', 'ERRO_API')
     *                            - 'populacao_input': População do município (quando status é 'OK')
     *                            - 'regiao': Região do município (quando status é 'OK')
     *
     * @return array Array associativo contendo as seguintes estatísticas:
     *               - 'total_municipios': Número total de municípios processados
     *               - 'total_ok': Quantidade de municípios processados com sucesso
     *               - 'total_nao_encontrado': Quantidade de municípios não encontrados ou ambíguos
     *               - 'total_erro_api': Quantidade de erros na API
     *               - 'pop_total_ok': População total dos municípios processados com sucesso
     *               - 'medias_por_regiao': Array associativo com a média populacional por região
     */
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
