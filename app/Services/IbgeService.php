<?php

declare(strict_types=1);

namespace App\Services;

use App\Models\MunicipioIbge;
use App\Support\Config;
use App\Support\HttpClient;
use App\Support\Normalizer;

final class IbgeService
{
    /**
     * Carrega a lista de municípios do IBGE
     * 
     * Tenta carregar os dados do cache primeiro. Caso não exista cache,
     * busca os dados da API do IBGE, salva no cache e retorna os municípios mapeados.
     * 
     * @return array Array associativo contendo:
     *               - 'ok' (bool): Indica se a operação foi bem-sucedida
     *               - 'data' (array): Lista de municípios mapeados ou array vazio em caso de erro
     * 
     * @throws \Throwable Em caso de falha na requisição ou processamento dos dados
     */
    public function loadMunicipios(): array
    {
        try {
            $data = $this->loadFromCache();
            if (!$data) {
                $raw = HttpClient::getJson(Config::IBGE_URL, 30);
                $this->saveCache($raw);
                $data = $raw;
            }
            return ['ok' => true, 'data' => $this->mapMunicipios($data)];
        } catch (\Throwable $e) {
            return ['ok' => false, 'data' => []];
        }
    }

    /**
     * Carrega os dados do cache armazenado em arquivo.
     * 
     * Verifica se o arquivo de cache existe e, caso exista, decodifica o JSON
     * e retorna os dados armazenados. Se o arquivo não existir ou os dados
     * estiverem em formato inválido, retorna um array vazio.
     * 
     * @return array Os dados armazenados em cache ou array vazio se não houver cache válido
     */
    private function loadFromCache(): array
    {
        $file = Config::cacheFile();
        if (!file_exists($file)) return [];
        $json = json_decode(file_get_contents($file) ?: '', true);
        return is_array($json) && isset($json['data']) ? (array)$json['data'] : [];
    }

    /**
     * Salva os dados em cache no sistema de arquivos.
     *
     * Este método é responsável por persistir os dados brutos recebidos da API do IBGE
     * em um arquivo de cache no formato JSON. Cria o diretório de cache caso não exista
     * e armazena os dados junto com a data/hora em que foram obtidos.
     *
     * @param array $raw Dados brutos a serem armazenados em cache
     * @return void
     */
    private function saveCache(array $raw): void
    {
        $file = \App\Support\Config::cacheFile();
        $dir = dirname($file);

        if (!is_dir($dir)) {
            mkdir($dir, 0777, true);
        }

        file_put_contents($file, json_encode([
            'fetched_at' => date('c'),
            'data' => $raw,
        ], JSON_UNESCAPED_UNICODE));
    }


    /**
     * Mapeia os dados brutos de municípios do IBGE para objetos MunicipioIbge.
     *
     * Transforma um array de dados brutos da API do IBGE em uma coleção de objetos
     * MunicipioIbge, extraindo informações como ID, nome, UF, região e gerando
     * uma chave normalizada para o município.
     *
     * @param array $raw Array de dados brutos dos municípios obtidos da API do IBGE.
     *                   Cada elemento deve conter 'id', 'nome' e estrutura aninhada
     *                   com 'microrregiao.mesorregiao.UF.sigla' e
     *                   'microrregiao.mesorregiao.UF.regiao.nome'.
     *
     * @return array Array de objetos MunicipioIbge contendo os dados processados.
     *               Municípios sem ID ou nome são ignorados.
     */
    private function mapMunicipios(array $raw): array
    {
        $out = [];
        foreach ($raw as $m) {
            if (!isset($m['id'], $m['nome'])) continue;

            $nome = (string)$m['nome'];
            $uf = (string)($m['microrregiao']['mesorregiao']['UF']['sigla'] ?? '');
            $reg = (string)($m['microrregiao']['mesorregiao']['UF']['regiao']['nome'] ?? '');
            $key = Normalizer::name($nome);

            $out[] = new MunicipioIbge((int)$m['id'], $nome, $uf, $reg, $key);
        }
        return $out;
    }
}
