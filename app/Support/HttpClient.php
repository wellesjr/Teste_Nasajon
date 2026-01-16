<?php

declare(strict_types=1);

namespace App\Support;

use RuntimeException;

final class HttpClient
{
    /**
     * Realiza uma requisição HTTP GET e retorna o corpo da resposta como array JSON.
     *
     * @param string $url URL completa para a requisição GET
     * @param int $timeout Tempo limite em segundos para a requisição (padrão: 25)
     * 
     * @return array Dados decodificados do JSON retornado pela API
     * 
     * @throws RuntimeException Se a requisição falhar (código HTTP fora de 2xx) ou se o JSON for inválido
     */
    public static function getJson(string $url, int $timeout = 25): array
    {
        $ch = curl_init($url);
        curl_setopt_array($ch, [
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_TIMEOUT => $timeout,
            CURLOPT_CONNECTTIMEOUT => 10,
            CURLOPT_HTTPHEADER => ['Accept: application/json'],
            CURLOPT_SSL_VERIFYPEER => false,
            CURLOPT_SSL_VERIFYHOST => 0,
        ]);
        $body = curl_exec($ch);
        $err  = curl_error($ch);
        $code = (int)curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);

        if ($body === false || $code < 200 || $code >= 300) {
            throw new RuntimeException("GET falhou (HTTP $code): " . ($err ?: 'sem detalhes'));
        }

        $json = json_decode($body, true);
        if (!is_array($json)) throw new RuntimeException("JSON inválido (GET)");
        return $json;
    }

    /**
     * Realiza uma requisição POST com payload JSON para a URL especificada.
     *
     * Envia dados codificados em JSON via POST HTTP usando cURL, incluindo token de
     * autenticação Bearer e headers apropriados. A verificação SSL é desabilitada
     * por padrão.
     *
     * @param string $url URL de destino para a requisição POST
     * @param array $payload Dados que serão codificados em JSON e enviados no corpo da requisição
     * @param string $token Token de autenticação Bearer para o header Authorization
     * @param int $timeout Tempo máximo em segundos para aguardar a resposta (padrão: 25)
     *
     * @return array Array decodificado da resposta JSON recebida
     *
     * @throws RuntimeException Se a requisição falhar (código HTTP fora do range 200-299),
     *                         ocorrer erro no cURL ou a resposta não for um JSON válido
     */
    public static function postJson(string $url, array $payload, string $token, int $timeout = 25): array
    {
        $data = json_encode($payload, JSON_UNESCAPED_UNICODE);

        $ch = curl_init($url);
        curl_setopt_array($ch, [
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_TIMEOUT => $timeout,
            CURLOPT_CONNECTTIMEOUT => 10,
            CURLOPT_POST => true,
            CURLOPT_POSTFIELDS => $data,
            CURLOPT_HTTPHEADER => [
                'Accept: application/json',
                'Content-Type: application/json',
                'Authorization: Bearer ' . $token,
            ],
            CURLOPT_SSL_VERIFYPEER => false,
            CURLOPT_SSL_VERIFYHOST => 0,
        ]);

        $body = curl_exec($ch);
        $err  = curl_error($ch);
        $code = (int)curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);

        if ($body === false || $code < 200 || $code >= 300) {
            throw new RuntimeException("POST falhou (HTTP $code): " . ($err ?: 'sem detalhes') . " | body=" . ($body ?: ''));
        }

        $json = json_decode($body, true);
        if (!is_array($json)) throw new RuntimeException("JSON inválido (POST)");
        return $json;
    }
}
