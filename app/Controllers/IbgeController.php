<?php
declare(strict_types=1);

namespace App\Controllers;

use App\Models\ResultadoLinha;
use App\Services\CsvService;
use App\Services\IbgeService;
use App\Services\MatcherService;
use App\Services\StatsService;
use App\Services\SubmitService;
use App\Support\Config;

final class IbgeController
{
    public function run(array $argv): void
    {
        $cliToken = null;
        $noSubmit = in_array('--no-submit', $argv, true);

        foreach ($argv as $arg) {
            if (str_starts_with($arg, '--token=')) {
                $cliToken = trim(substr($arg, 8), "\"'");
            }
        }

        $token = Config::accessToken($cliToken);

        $csv = new CsvService();
        $ibge = new IbgeService();
        $matcher = new MatcherService();
        $statsSvc = new StatsService();
        $submitSvc = new SubmitService();

        $input = $csv->readInput(Config::inputCsv());

        $ibgeLoad = $ibge->loadMunicipios();
        $okApi = $ibgeLoad['ok'];
        $municipios = $ibgeLoad['data'];

        if ($okApi) {
            $matcher->buildIndex($municipios);
        }

        $resultadoRows = [];

        foreach ($input as $row) {
            $munIn = $row['municipio'];
            $popIn = (int)$row['populacao'];

            if (!$okApi) {
                $linha = new ResultadoLinha($munIn, $popIn, '', '', '', '', 'ERRO_API');
                $resultadoRows[] = $linha->toCsvRow();
                continue;
            }

            $match = $matcher->match($munIn, $municipios);
            $status = $match['status'];
            $m = $match['municipio'];

            if ($status === 'OK' && $m) {
                $linha = new ResultadoLinha(
                    $munIn, $popIn, $m->nome, $m->uf, $m->regiao, (string)$m->idIbge, 'OK'
                );
            } elseif ($status === 'AMBIGUO' && $m) {
                $linha = new ResultadoLinha(
                    $munIn, $popIn, $m->nome, $m->uf, $m->regiao, (string)$m->idIbge, 'AMBIGUO'
                );
            } else {
                $linha = new ResultadoLinha($munIn, $popIn, '', '', '', '', 'NAO_ENCONTRADO');
            }

            $resultadoRows[] = $linha->toCsvRow();
        }

        $csv->writeOutput(Config::outputCsv(), $resultadoRows);

        $stats = $statsSvc->compute($resultadoRows);

        echo "resultado.csv gerado em: " . Config::outputCsv() . PHP_EOL;
        echo json_encode(['stats' => $stats], JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE) . PHP_EOL;

        if ($noSubmit) {
            echo "Modo --no-submit (não enviou para correção)." . PHP_EOL;
            return;
        }

        if (!$token) {
            fwrite(STDERR, "ERRO: informe ACCESS_TOKEN (env ACCESS_TOKEN=... ou --token=...)\n");
            exit(1);
        }

        $resp = $submitSvc->submit($stats, $token);
        echo "Resposta da correção:\n";
        echo json_encode($resp, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE) . PHP_EOL;

        if (isset($resp['score'])) {
            echo "\nSCORE: {$resp['score']}\n";
        }
    }
}
