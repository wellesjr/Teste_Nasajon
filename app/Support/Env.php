<?php

declare(strict_types=1);

namespace App\Support;

final class Env
{
    /**
     * Carrega as variáveis de ambiente de um arquivo .env
     * 
     * Este método lê um arquivo de configuração no formato .env e carrega
     * as variáveis de ambiente definidas nele para o array global $_ENV.
     * 
     * Características:
     * - Ignora linhas vazias e comentários (linhas iniciadas com #)
     * - Aceita o formato CHAVE=VALOR
     * - Não sobrescreve variáveis já definidas
     * 
     * @param string $path Caminho completo para o arquivo .env a ser carregado
     * 
     * @return void
     * 
     * @example
     * Env::load(__DIR__ . '/.env');
     */
    public static function load(string $path): void
    {
        if (!file_exists($path)) return;

        $lines = file($path, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
        if (!$lines) return;

        foreach ($lines as $line) {
            if (str_starts_with(trim($line), '#')) continue;

            [$key, $value] = array_pad(explode('=', $line, 2), 2, null);
            if ($key && $value !== null) {
                $_ENV[$key] = $value;
            }
        }
    }

    /**
     * Obtém o valor de uma variável de ambiente.
     *
     * @param string $key A chave da variável de ambiente a ser recuperada
     * @return string|null O valor da variável de ambiente ou null se não existir
     */
    public static function get(string $key): ?string
    {
        return $_ENV[$key] ?? null;
    }
}
