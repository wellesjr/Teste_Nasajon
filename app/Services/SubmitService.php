<?php
declare(strict_types=1);

namespace App\Services;

use App\Support\Config;
use App\Support\HttpClient;

final class SubmitService
{
    /**
     * Submete estatísticas para a URL configurada.
     *
     * Envia dados estatísticos através de uma requisição HTTP POST em formato JSON
     * para o endpoint de submissão configurado no sistema.
     *
     * @param array $stats Array contendo as estatísticas a serem enviadas
     * @param string $token Token de autenticação para a requisição
     * @return array Resposta da requisição HTTP contendo o resultado da submissão
     * @throws \Exception Caso ocorra erro na comunicação HTTP
     */
    public function submit(array $stats, string $token): array
    {
        return HttpClient::postJson(Config::SUBMIT_URL, ['stats' => $stats], $token, 30);
    }
}
