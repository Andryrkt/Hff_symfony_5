<?php
// src/Form/DataTransformer/NumberTransformer.php

namespace App\Form\DataTransformer;

use Symfony\Component\Form\DataTransformerInterface;
use Symfony\Component\Form\Exception\TransformationFailedException;

class NumberTransformer implements DataTransformerInterface
{
    /**
     * Transforms a numeric value to a formatted string for the view.
     * (e.g., 1234.56 => "1 234,56")
     */
    public function transform($value): ?string
    {
        if (null === $value) {
            return '';
        }

        // Force la conversion en float pour un traitement uniforme
        $number = (float) $value;

        // Formate avec 2 décimales, une virgule comme séparateur décimal, et un espace pour les milliers.
        return number_format($number, 2, ',', ' ');
    }

    /**
     * Transforms a formatted string from the view back to a numeric value.
     * (e.g., "1 234,56" => 1234.56)
     */
    public function reverseTransform($value): ?float
    {
        if (!$value) {
            return null;
        }

        // Supprimer les espaces et remplacer la virgule décimale par un point.
        $cleanedValue = str_replace(' ', '', $value);
        $cleanedValue = str_replace(',', '.', $cleanedValue);

        // Vérifier si la valeur est numérique après le nettoyage.
        if (!is_numeric($cleanedValue)) {
            // Lève une exception si la transformation échoue, ce qui générera une erreur de formulaire.
            throw new TransformationFailedException(sprintf(
                'La valeur "%s" n\'est pas un nombre valide.',
                $value
            ));
        }

        return (float) $cleanedValue;
    }
}
