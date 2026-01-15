<?php

declare(strict_types=1);

namespace App\Support;

use RuntimeException;

final class HttpClient
{
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
