<?php
declare(strict_types=1);

namespace App\Models;

final class MunicipioIbge
{
    public function __construct(
        public int $idIbge,
        public string $nome,
        public string $uf,
        public string $regiao,
        public string $key
    ) {}
}
