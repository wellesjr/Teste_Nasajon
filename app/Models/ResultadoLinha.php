<?php
declare(strict_types=1);

namespace App\Models;

final class ResultadoLinha
{
    public function __construct(
        public string $municipioInput,
        public int $populacaoInput,
        public string $municipioIbge,
        public string $uf,
        public string $regiao,
        public string $idIbge,
        public string $status
    ) {}

    public function toCsvRow(): array
    {
        return [
            'municipio_input' => $this->municipioInput,
            'populacao_input' => $this->populacaoInput,
            'municipio_ibge' => $this->municipioIbge,
            'uf' => $this->uf,
            'regiao' => $this->regiao,
            'id_ibge' => $this->idIbge,
            'status' => $this->status,
        ];
    }
}
