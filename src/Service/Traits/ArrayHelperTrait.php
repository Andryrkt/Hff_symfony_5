<?php

namespace App\Service\Traits;

use InvalidArgumentException;

/**
 * Trait pour fournir des méthodes utilitaires de manipulation de tableaux.
 */
trait ArrayHelperTrait
{
    /**
     * Transforme un tableau multidimensionnel en un tableau unidimensionnel.
     *
     * @param array $tabs
     * @return array
     */
    private function flattenArray(array $tabs): array
    {
        $result = [];
        foreach ($tabs as $values) {
            if (is_array($values)) {
                // Fusionne les sous-tableaux récursivement
                $result = array_merge($result, $this->flattenArray($values));
            } else {
                $result[] = (string) $values; // Convertit les valeurs en chaînes
            }
        }

        return $result;
    }

    /**
     * Methode general pour transformer un tableau en string
     *
     * @param array $tab tableau contenant les elements à mettre en string qui peut être une simple tableau ou tableau associatif
     * @param string $separateur séparateur entre les elements
     * @param string $quote quote pour les elements
     * @return string
     */
    public function TableauEnString(array $tab, string $separateur = ',',  string $quote = "'"): string
    {
        // Fonction de validation et de transformation
        $flattenedArray = $this->flattenArray($tab);

        // Si le tableau est vide, renvoyer deux quotes simples
        if (empty($flattenedArray)) {
            return $quote . $quote;
        }

        // Échappe les caractères spéciaux si nécessaire
        $escapedArray = array_map(function ($el) use ($quote) {
            // Convertir en chaîne de caractères si ce n'est pas déjà une
            if (!is_scalar($el)) {
                throw new InvalidArgumentException("Tous les éléments du tableau doivent être scalaires.");
            }
            return $quote . $el . $quote;
        }, $flattenedArray);

        // Joindre les éléments avec le séparateur
        return implode($separateur, $escapedArray);
    }

    /**
     * Méthode pour transformer une chaîne de caractères formatée en tableau.
     * C'est l'opération inverse de TableauEnString.
     *
     * @param string $str La chaîne de caractères à convertir.
     * @param string $separateur Le séparateur utilisé dans la chaîne.
     * @param string $quote Le caractère de citation utilisé.
     * @return array Le tableau résultant.
     */
    public function StringEnTableau(string $str, string $separateur = ',', string $quote = "'"): array
    {
        if (empty($str) || $str === $quote . $quote) {
            return [];
        }

        // Supprimer les guillemets de début et de fin de la chaîne globale
        $innerStr = substr($str, strlen($quote), -strlen($quote));
        
        // Diviser par le délimiteur qui est quote + séparateur + quote
        $delimiter = $quote . $separateur . $quote;
        $elements = explode($delimiter, $innerStr);

        return $elements;
    }
}
