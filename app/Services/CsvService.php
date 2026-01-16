<?php

declare(strict_types=1);

namespace App\Services;

use RuntimeException;

final class CsvService
{
    /**
     * Lê e processa um arquivo CSV de entrada contendo dados de municípios e população.
     *
     * Este método abre um arquivo CSV, valida sua existência e estrutura, e converte
     * os dados em um array associativo. Espera-se que o CSV tenha pelo menos duas colunas:
     * município (string) e população (inteiro).
     *
     * @param string $path Caminho completo do arquivo CSV a ser lido
     * 
     * @return array Array de arrays associativos, onde cada elemento contém:
     *               - 'municipio' (string): Nome do município
     *               - 'populacao' (int): Número de habitantes
     * 
     * @throws RuntimeException Se o arquivo não existir
     * @throws RuntimeException Se não for possível abrir o arquivo
     * @throws RuntimeException Se o arquivo CSV estiver vazio (sem cabeçalho)
     */
    public function readInput(string $path): array
    {
        if (!file_exists($path)) throw new RuntimeException("CSV não encontrado: $path");
        $fh = fopen($path, 'r');
        if (!$fh) throw new RuntimeException("Não abriu CSV: $path");

        $header = fgetcsv($fh);
        if ($header === false) throw new RuntimeException("CSV vazio: $path");

        $rows = [];
        while (($line = fgetcsv($fh)) !== false) {
            if (count($line) < 2) continue;
            $rows[] = ['municipio' => (string)$line[0], 'populacao' => (int)$line[1]];
        }
        fclose($fh);
        return $rows;
    }

    /**
     * Escreve os dados processados em um arquivo CSV de saída.
     *
     * Cria um novo arquivo CSV no caminho especificado e escreve um cabeçalho fixo
     * seguido pelas linhas de dados fornecidas. Cada linha é formatada de acordo
     * com as colunas definidas no cabeçalho.
     *
     * @param string $path Caminho completo do arquivo CSV a ser criado
     * @param array $rows Array associativo contendo as linhas a serem escritas.
     *                    Cada elemento deve conter as chaves: 'municipio_input',
     *                    'populacao_input', 'municipio_ibge', 'uf', 'regiao',
     *                    'id_ibge' e 'status'
     *
     * @return void
     *
     * @throws RuntimeException Se não for possível criar ou escrever no arquivo
     */
    public function writeOutput(string $path, array $rows): void
    {
        $fh = fopen($path, 'w');
        if (!$fh) throw new RuntimeException("Não escreveu CSV: $path");

        $header = ['municipio_input', 'populacao_input', 'municipio_ibge', 'uf', 'regiao', 'id_ibge', 'status'];
        fputcsv($fh, $header);

        foreach ($rows as $r) {
            $line = [];
            foreach ($header as $col) $line[] = $r[$col] ?? '';
            fputcsv($fh, $line);
        }
        fclose($fh);
    }
}
