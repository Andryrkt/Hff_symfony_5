<?php

namespace App\Service\Utils\Fichier;

use Symfony\Component\PropertyAccess\PropertyAccess;
use Symfony\Component\PropertyAccess\PropertyAccessorInterface;

/**
 * Générateur de tableaux HTML ultra-flexible pour TCPDF.
 * 
 * Ce service permet de générer des structures de tableaux complexes avec support
 * des propriétés imbriquées, formatage automatique et styles dynamiques.
 * 
 * Les options globales (via setOptions()) incluent :
 * - `table_attributes`: Attributs HTML de la balise <table>
 * - `header_row_style`: Style CSS de la ligne d'en-tête
 * - `footer_row_style`: Style CSS de la ligne de pied de page
 * - `row_styler`: Callable function($row) retournant un style CSS pour le <tr>
 * - `empty_message`: Message à afficher si aucune donnée
 * - `default_number_format`: array ['decimals', 'dec_point', 'thousands_sep']
 * - `default_date_format`: format de date PHP (ex: 'd/m/Y')
 * - `default_empty_value`: Valeur par défaut pour les champs vides
 * 
 * La configuration des colonnes ($headerConfig) accepte :
 * - `key`: Chemin de la propriété (ex: 'id', 'client.nom', '[piece][libelle]')
 * - `label`: Libellé d'en-tête
 * - `width`: Largeur en pixels
 * - `type`: 'number', 'date', 'datetime', 'boolean', 'percent', 'text'
 * - `style`: Style CSS de base (appliqué à th et td)
 * - `header_style`: Surchage de style pour th
 * - `cell_style`: Surchage de style pour td
 * - `footer_style`: Surchage de style pour le pied de page
 * - `formatter`: Callable function($value, $row) pour transformer la valeur
 * - `styler`: Callable function($value, $row) pour un style de cellule dynamique
 */
class PdfTableGeneratorFlexible
{
    private array $options = [];
    private PropertyAccessorInterface $accessor;

    public function __construct(?PropertyAccessorInterface $accessor = null)
    {
        $this->accessor = $accessor ?? PropertyAccess::createPropertyAccessor();
    }

    /**
     * Définit les options pour la génération. Les options sont fusionnées avec les valeurs par défaut.
     */
    public function setOptions(array $options): self
    {
        $this->options = array_merge([
            'table_attributes'      => 'border="0" cellpadding="0" cellspacing="0" align="center" style="font-size: 8px;"',
            'header_row_style'      => 'background-color: #D3D3D3;',
            'footer_row_style'      => 'background-color: #D3D3D3;',
            'empty_message'         => 'Aucune donnée disponible',
            'default_number_format' => ['decimals' => 2, 'dec_point' => ',', 'thousands_sep' => '.'],
            'default_date_format'   => 'd/m/Y',
            'default_empty_value'   => '',
        ], $options);

        return $this;
    }

    /**
     * Génère le HTML du tableau.
     * 
     * @param array $headerConfig Configuration des colonnes
     * @param array $rows Données du corps
     * @param array $totals Données du pied de page (optionnel)
     * @param bool $hideEmptyMessage Si vrai, n'affiche rien si $rows est vide
     */
    public function generateTable(array $headerConfig, array $rows, array $totals = [], bool $hideEmptyMessage = false): string
    {
        if (empty($this->options)) {
            $this->setOptions([]);
        }

        $html = '<table ' . $this->options['table_attributes'] . '>';
        $html .= $this->generateHeader($headerConfig);
        $html .= $this->generateBody($headerConfig, $rows, $hideEmptyMessage);

        if (!empty($totals)) {
            $html .= $this->generateFooter($headerConfig, $totals);
        }

        $html .= '</table>';

        // Reset des options après génération pour isolation
        $this->options = [];

        return $html;
    }

    private function generateHeader(array $headerConfig): string
    {
        $html = '<thead><tr style="' . $this->options['header_row_style'] . '">';
        foreach ($headerConfig as $config) {
            $style = $config['header_style'] ?? $config['style'] ?? '';
            $width = isset($config['width']) ? 'width: ' . $config['width'] . 'px; ' : '';
            $html .= sprintf('<th style="%s%s">%s</th>', $width, $style, $config['label'] ?? '');
        }
        $html .= '</tr></thead>';
        return $html;
    }

    private function generateBody(array $headerConfig, array $rows, bool $hideEmptyMessage): string
    {
        $html = '<tbody>';

        if (empty($rows)) {
            if (!$hideEmptyMessage) {
                $html .= sprintf(
                    '<tr><td colspan="%d" style="text-align: center; font-weight: bold; padding: 5px;">%s</td></tr>',
                    count($headerConfig),
                    $this->options['empty_message']
                );
            }
            return $html . '</tbody>';
        }

        foreach ($rows as $row) {
            $rowStyle = '';
            if (isset($this->options['row_styler']) && is_callable($this->options['row_styler'])) {
                $rowStyle = $this->options['row_styler']($row);
            }

            $html .= sprintf('<tr style="%s">', $rowStyle);

            foreach ($headerConfig as $config) {
                $value = $this->getValueFromData($row, $config['key'] ?? '');

                $baseStyle = $config['cell_style'] ?? str_replace('font-weight: bold;', '', $config['style'] ?? '');
                $dynamicStyle = (isset($config['styler']) && is_callable($config['styler']))
                    ? $config['styler']($value, $row)
                    : '';

                $style = $baseStyle . $dynamicStyle;
                $width = isset($config['width']) ? 'width: ' . $config['width'] . 'px; ' : '';

                $formattedValue = (isset($config['formatter']) && is_callable($config['formatter']))
                    ? $config['formatter']($value, $row)
                    : $this->formatValue($value, $config);

                $html .= sprintf('<td style="%s%s">%s</td>', $width, $style, $formattedValue);
            }
            $html .= '</tr>';
        }

        return $html . '</tbody>';
    }

    private function generateFooter(array $headerConfig, array $totals): string
    {
        $html = '<tfoot><tr style="' . $this->options['footer_row_style'] . '">';
        $skipCount = 0;

        foreach ($headerConfig as $index => $config) {
            if ($skipCount > 0) {
                $skipCount--;
                continue;
            }

            $colspan = $config['footer_colspan'] ?? 1;
            $value = $this->getValueFromData($totals, $config['key'] ?? '');
            $style = $config['footer_style'] ?? $config['style'] ?? '';

            // Calculer la largeur cumulée si colspan > 1
            $totalWidth = $config['width'] ?? 0;
            if ($colspan > 1) {
                for ($i = 1; $i < $colspan; $i++) {
                    if (isset($headerConfig[$index + $i])) {
                        $totalWidth += $headerConfig[$index + $i]['width'] ?? 0;
                    }
                }
                $skipCount = $colspan - 1;
            }

            $widthAttr = $totalWidth > 0 ? sprintf('width: %dpx; ', $totalWidth) : '';
            $colspanAttr = $colspan > 1 ? sprintf(' colspan="%d"', $colspan) : '';

            $formattedValue = (isset($config['formatter']) && is_callable($config['formatter']))
                ? $config['formatter']($value, $totals)
                : $this->formatValue($value, $config);

            $html .= sprintf('<th%s style="%s%s">%s</th>', $colspanAttr, $widthAttr, $style, $formattedValue);
        }
        $html .= '</tr></tfoot>';
        return $html;
    }

    /**
     * Récupère de manière sécurisée une valeur depuis un tableau ou un objet.
     */
    private function getValueFromData($row, string $key)
    {
        if ($key === '') {
            return null;
        }

        try {
            // Normalisation pour PropertyAccessor si c'est un tableau simple et une clé simple
            $propertyPath = (is_array($row) && strpos($key, '.') === false && strpos($key, '[') === false)
                ? '[' . $key . ']'
                : $key;

            return $this->accessor->getValue($row, $propertyPath);
        } catch (\Exception $e) {
            // En cas d'échec de l'accessor, on tente un fallback basique si c'est un objet
            if (is_object($row) && isset($row->{$key})) {
                return $row->{$key};
            }
            return null;
        }
    }

    /**
     * Formate la valeur selon la configuration.
     */
    private function formatValue($value, array $config): string
    {
        if ($value === null || $value === '') {
            return $config['default_value'] ?? $this->options['default_empty_value'];
        }

        $type = $config['type'] ?? 'text';

        switch ($type) {
            case 'number':
                $fmt = $this->options['default_number_format'];
                if (is_numeric($value)) {
                    return number_format((float)$value, $fmt['decimals'], $fmt['dec_point'], $fmt['thousands_sep']);
                }
                return $value;

            case 'date':
            case 'datetime':
                if ($value instanceof \DateTimeInterface) {
                    return $value->format($config['format'] ?? $this->options['default_date_format']);
                }
                try {
                    // Tente de parser si c'est une string
                    if (is_string($value) && !empty($value)) {
                        return (new \DateTime($value))->format($config['format'] ?? $this->options['default_date_format']);
                    }
                } catch (\Exception $e) {
                    return $value;
                }
                return $value;

            case 'boolean':
                return $value ? ($config['true_label'] ?? 'Oui') : ($config['false_label'] ?? 'Non');

            case 'percent':
                return number_format((float)$value, 2, ',', '.') . ' %';

            default:
                return (string)$value;
        }
    }
}
