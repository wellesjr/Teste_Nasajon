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

    /**
     * Converte os dados da linha de resultado para um array formatado para CSV.
     *
     * Este método retorna um array associativo contendo os dados do resultado
     * da linha formatados para exportação em arquivo CSV. Cada chave do array
     * representa o nome da coluna no CSV e o valor corresponde aos dados
     * armazenados no modelo.
     *
     * @return array Array associativo com os seguintes campos:
     *               - municipio_input: Nome do município fornecido na entrada
     *               - populacao_input: População fornecida na entrada
     *               - municipio_ibge: Nome oficial do município segundo IBGE
     *               - uf: Unidade Federativa (estado)
     *               - regiao: Região geográfica do Brasil
     *               - id_ibge: Código identificador do IBGE
     *               - status: Status do processamento da linha
     */
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
