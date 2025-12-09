<?php

namespace App\Service\Utils;

use DateTimeInterface;
use Exception;

class FormattingService
{
    /**
     * Formate une date/heure (chaîne ou objet) dans un format spécifié.
     *
     * @param string|DateTimeInterface|null $date La date à formater.
     * @param string $format Le format de sortie souhaité (ex: 'd/m/Y H:i:s').
     * @return string|null La date formatée ou null en cas d'échec.
     */
    public function formatDate($date, string $format = 'd/m/Y'): ?string
    {
        if ($date === null || $date === '') {
            return null;
        }

        if ($date instanceof DateTimeInterface) {
            return $date->format($format);
        }

        try {
            return (new \DateTime($date))->format($format);
        } catch (Exception $e) {
            // Gère les chaînes de date invalides
            return null;
        }
    }

    /**
     * Formate un nombre en chaîne de caractères avec les séparateurs et la précision souhaités.
     *
     * @param float|int|string|null $number Le nombre à formater.
     * @param int $decimals Le nombre de décimales.
     * @param string $decimalSeparator Le séparateur pour la partie décimale.
     * @param string $thousandsSeparator Le séparateur pour les milliers.
     * @return string|null Le nombre formaté.
     */
    public function formatNumber($number, int $decimals = 2, string $decimalSeparator = ',', string $thousandsSeparator = '.'): ?string
    {
        if ($number === null || $number === '') {
            return null;
        }

        // Si l'entrée est une chaîne formatée, on la convertit d'abord en nombre standard.
        if (is_string($number)) {
            $number = $this->stringToNumber($number);
        }

        if (!is_numeric($number)) {
            return null;
        }

        return number_format($number, $decimals, $decimalSeparator, $thousandsSeparator);
    }

    /**
     * Convertit une chaîne de nombre formatée (ex: "1.234,56") en un nombre standard PHP (float ou int).
     *
     * @param string|null $numberString La chaîne à convertir.
     * @return float|int|null Le nombre converti.
     */
    public function stringToNumber(?string $numberString)
    {
        if ($numberString === null || trim($numberString) === '') {
            return null;
        }

        // Supprimer les séparateurs de milliers (points)
        $numberString = str_replace('.', '', $numberString);
        // Remplacer le séparateur décimal (virgule) par un point
        $numberString = str_replace(',', '.', $numberString);

        if (!is_numeric($numberString)) {
            return null;
        }

        // Convertir en float ou int
        return strpos($numberString, '.') !== false ? (float)$numberString : (int)$numberString;
    }
}
