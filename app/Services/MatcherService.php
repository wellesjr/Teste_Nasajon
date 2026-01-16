<?php

declare(strict_types=1);

namespace App\Services;

use App\Support\Normalizer;

final class MatcherService
{
    private array $index = [];

    /**
     * Constrói um índice de municípios agrupados por chave.
     *
     * Este método itera sobre um array de municípios e os organiza em um índice
     * associativo, onde cada chave agrupa múltiplos municípios que compartilham
     * o mesmo valor de chave.
     *
     * @param array $municipios Array de objetos de municípios contendo a propriedade 'key'
     * @return void
     */
    public function buildIndex(array $municipios): void
    {
        foreach ($municipios as $m) {
            $this->index[$m->key][] = $m;
        }
    }

    /**
     * Realiza a correspondência entre um nome de município fornecido e uma lista de municípios.
     * 
     * Este método normaliza o input e busca correspondências exatas no índice. Caso não encontre,
     * utiliza o algoritmo de distância de Levenshtein para encontrar a correspondência mais próxima,
     * considerando um limite de distância baseado no tamanho da string.
     * 
     * @param string $input O nome do município a ser pesquisado
     * @param array $municipios Array de objetos municipio contendo a propriedade 'key' para comparação
     * 
     * @return array Retorna um array associativo com:
     *               - 'status': string indicando o resultado ('OK', 'AMBIGUO' ou 'NAO_ENCONTRADO')
     *               - 'municipio': objeto do município encontrado ou null se não encontrado
     *               
     *               Status possíveis:
     *               - 'OK': Município encontrado com sucesso (exato ou aproximado)
     *               - 'AMBIGUO': Múltiplos municípios encontrados com o mesmo nome
     *               - 'NAO_ENCONTRADO': Nenhum município encontrado dentro do limite de distância
     */
    public function match(string $input, array $municipios): array
    {
        $key = Normalizer::name($input);

        if (isset($this->index[$key])) {
            $list = $this->index[$key];
            if (count($list) === 1) return ['status' => 'OK', 'municipio' => $list[0]];
            return ['status' => 'AMBIGUO', 'municipio' => $list[0]];
        }

        $best = null;
        $bestDist = -1;

        foreach ($municipios as $m) {
            $d = levenshtein($key, $m->key);
            if ($d <= $bestDist || $bestDist < 0) {
                $bestDist = $d;
                $best = $m;
            }
        }

        $len = max(strlen($key), 1);
        $limit = ($len <= 8) ? 2 : (($len <= 14) ? 2 : 3);

        if ($best && $bestDist <= $limit) {
            return ['status' => 'OK', 'municipio' => $best];
        }

        return ['status' => 'NAO_ENCONTRADO', 'municipio' => null];
    }
}
