<?php
declare(strict_types=1);

namespace App\Services;

use RuntimeException;

final class CsvService
{
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

    public function writeOutput(string $path, array $rows): void
    {
        $fh = fopen($path, 'w');
        if (!$fh) throw new RuntimeException("Não escreveu CSV: $path");

        $header = ['municipio_input','populacao_input','municipio_ibge','uf','regiao','id_ibge','status'];
        fputcsv($fh, $header);

        foreach ($rows as $r) {
            $line = [];
            foreach ($header as $col) $line[] = $r[$col] ?? '';
            fputcsv($fh, $line);
        }
        fclose($fh);
    }
}
