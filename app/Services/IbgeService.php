<?php
declare(strict_types=1);

namespace App\Services;

use App\Models\MunicipioIbge;
use App\Support\Config;
use App\Support\HttpClient;
use App\Support\Normalizer;

final class IbgeService
{
    /** @return array{ok:bool, data: MunicipioIbge[]} */
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

    private function loadFromCache(): array
    {
        $file = Config::cacheFile();
        if (!file_exists($file)) return [];
        $json = json_decode(file_get_contents($file) ?: '', true);
        return is_array($json) && isset($json['data']) ? (array)$json['data'] : [];
    }

    private function saveCache(array $raw): void
    {
        file_put_contents(Config::cacheFile(), json_encode([
            'fetched_at' => date('c'),
            'data' => $raw,
        ], JSON_UNESCAPED_UNICODE));
    }

    /** @return MunicipioIbge[] */
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
