<?php

declare(strict_types=1);

namespace App\Support;

final class Config
{
    public const IBGE_URL = 'https://servicodados.ibge.gov.br/api/v1/localidades/municipios';
    public const SUBMIT_URL = 'https://mynxlubykylncinttggu.functions.supabase.co/ibge-submit';

    /**
     * Retorna o caminho completo do arquivo CSV de entrada.
     *
     * Este método estático constrói o caminho absoluto para o arquivo input.csv
     * localizado no diretório storage, dois níveis acima do diretório atual.
     *
     * @return string Caminho completo para o arquivo input.csv
     */
    public static function inputCsv(): string
    {
        return __DIR__ . '/../../storage/input.csv';
    }

    /**
     * Retorna o caminho completo do arquivo CSV de saída.
     *
     * Este método retorna o caminho absoluto para o arquivo resultado.csv
     * localizado no diretório storage, que é usado para armazenar os
     * resultados processados.
     *
     * @return string O caminho completo do arquivo CSV de saída
     */
    public static function outputCsv(): string
    {
        return __DIR__ . '/../../storage/resultado.csv';
    }

    /**
     * Retorna o caminho completo do arquivo de cache de municípios.
     *
     * Este método estático fornece o caminho absoluto para o arquivo JSON
     * que armazena os dados em cache dos municípios. O arquivo está localizado
     * no diretório storage na raiz do projeto.
     *
     * @return string Caminho completo para o arquivo cache_municipios.json
     */
    public static function cacheFile(): string
    {
        return __DIR__ . '/../../storage/cache_municipios.json';
    }

    /**
     * Obtém o token de acesso para autenticação.
     *
     * Este método retorna o token de acesso, priorizando o token fornecido
     * via parâmetro. Caso nenhum token seja passado, busca o valor da
     * variável de ambiente 'ACCESS_TOKEN'.
     *
     * @param string|null $cliToken Token de acesso fornecido via CLI (opcional)
     * @return string|null Retorna o token de acesso ou null se não encontrado
     */
    public static function accessToken(?string $cliToken = null): ?string
    {
        if ($cliToken) return $cliToken;
        return Env::get('ACCESS_TOKEN');
    }
}
