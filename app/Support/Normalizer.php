<?php
declare(strict_types=1);

namespace App\Support;

/**
 * Classe responsável por normalizar strings.
 * 
 * Esta classe fornece métodos estáticos para normalização de dados,
 * garantindo consistência e padronização de informações.
 */

/**
 * Normaliza uma string de nome removendo acentos, espaços e caracteres especiais.
 * 
 * O processo de normalização realiza as seguintes transformações:
 * - Remove espaços em branco no início e fim da string
 * - Converte todos os caracteres para minúsculas
 * - Remove todos os espaços em branco
 * - Transliteração de caracteres UTF-8 para ASCII (remove acentos)
 * - Remove todos os caracteres que não sejam letras (a-z) ou números (0-9)
 * 
 * @param string $s A string a ser normalizada
 * @return string A string normalizada contendo apenas letras minúsculas e números
 * 
 * @example
 * Normalizer::name("José da Silva"); // retorna "josedasilva"
 * Normalizer::name("María González"); // retorna "mariagonzalez"
 * Normalizer::name("Test@123"); // retorna "test123"
 */

final class Normalizer
{
    public static function name(string $s): string
    {
        $s = trim(mb_strtolower($s, 'UTF-8'));

        $s = str_replace(' ', '', $s);
        
        $conv = @iconv('UTF-8', 'ASCII//TRANSLIT', $s);
        if ($conv !== false) $s = $conv;

        $s = preg_replace('/[^a-z0-9]/', '', $s) ?? $s;

        return $s;
    }
}
