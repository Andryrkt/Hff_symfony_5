<?php

namespace App\Service;

class TimelineDataService
{
    /**
     * Prépare les données pour l'affichage sur une chronologie de 12 mois glissants.
     *
     * @param array $data Données brutes à traiter.
     * @param integer $selectedOption Option de période choisie (ex: 3, 6, 9 mois).
     * @param array<string, string> $fieldMapping Mappage des champs à extraire (ex: ['commercial' => 'getCommercial']).
     * @param array $detailConfig Configuration pour le traitement des détails (validator, clés de données).
     * @return array Données préparées et liste des mois uniques.
     */
    public function prepareDataForDisplay(array $data, int $selectedOption, array $fieldMapping, array $detailConfig = []): array
    {
        $months = ['Jan', 'Fév', 'Mar', 'Avr', 'Mai', 'Jun', 'Jul', 'Aoû', 'Sep', 'Oct', 'Nov', 'Déc'];
        $currentMonth = (int)date('n') - 1; // Index du mois actuel (0-11)
        $currentYear = (int)date('Y');

        $selectedMonths = $this->getSelectedMonths($months, $currentMonth, $currentYear, $selectedOption);

        // --- Configuration de la généralisation des détails ---
        $config = array_merge([
            'or_key' => 'orIntv',
            'month_key' => 'mois',
            'year_key' => 'annee',
        ], $detailConfig);

        $validator = $detailConfig['validator'] ?? function ($detail) use ($config) {
            return is_array($detail)
                && isset($detail[$config['or_key']], $detail[$config['month_key']])
                && $detail[$config['or_key']] !== "-";
        };
        // --- Fin de la configuration ---

        $preparedData = array_filter(array_map(function ($item) use ($months, $selectedMonths, $fieldMapping, $validator, $config) {
            $moisDetails = property_exists($item, 'moisDetails') && is_array($item->getMoisDetails())
                ? $item->getMoisDetails()
                : [];

            $filteredMonths = array_filter(array_map(function ($detail) use ($months, $selectedMonths, $validator, $config) {
                if (!$validator($detail)) {
                    return null;
                }

                $monthIndex = (int)$detail[$config['month_key']] - 1;
                $year = $detail[$config['year_key']] ?? '';
                $monthKey = sprintf('%04d-%02d', $year, $monthIndex + 1);

                if (array_search($monthKey, array_column($selectedMonths, 'key')) !== false) {
                    return [
                        'month'   => $months[$monthIndex] ?? '',
                        'year'    => $year,
                        'details' => $detail,
                    ];
                }

                return null;
            }, $moisDetails));

            if (empty($filteredMonths)) {
                return null;
            }

            $preparedItem = [];
            foreach ($fieldMapping as $key => $methodName) {
                if (method_exists($item, $methodName)) {
                    $preparedItem[$key] = $item->{$methodName}() ?? '';
                } else {
                    $preparedItem[$key] = ''; // La méthode n'existe pas, on met une chaîne vide
                }
            }
            $preparedItem['filteredMonths'] = array_values($filteredMonths);

            return $preparedItem;
        }, $data));

        return [
            'preparedData' => array_values($preparedData),
            'uniqueMonths' => $selectedMonths,
        ];
    }

    /**
     * Génère la liste des mois à afficher en fonction de l'option sélectionnée.
     */
    private function getSelectedMonths(array $months, int $currentMonth, int $currentYear, int $selectedOption): array
    {
        $selectedMonths = [];

        switch ($selectedOption) {
            case 3: // 3 mois suivant
            case 6: // 6 mois suivant
                $monthsCount = $selectedOption === 3 ? 4 : 7;
                for ($i = 0; $i < $monthsCount; $i++) {
                    $selectedMonths[] = $this->generateMonthData($months, $currentMonth, $currentYear, $i);
                }
                // Compléter avec les mois précédents pour avoir 12 mois au total
                for ($i = -1; count($selectedMonths) < 12; $i--) {
                    array_unshift($selectedMonths, $this->generateMonthData($months, $currentMonth, $currentYear, $i));
                }
                break;

            case 9: // Année en cours
                for ($i = 0; $i < 12; $i++) {
                    $selectedMonths[] = [
                        'month' => $months[$i],
                        'year' => $currentYear,
                        'key' => sprintf('%04d-%02d', $currentYear, $i + 1),
                    ];
                }
                break;

            case 11: // Année suivante
                for ($i = 0; $i < 12; $i++) {
                    $selectedMonths[] = [
                        'month' => $months[$i],
                        'year' => $currentYear + 1,
                        'key' => sprintf('%04d-%02d', $currentYear + 1, $i + 1),
                    ];
                }
                break;
            case 12: // 12 mois suivant
                for ($i = 0; $i < 12; $i++) {
                    $selectedMonths[] = $this->generateMonthData($months, $currentMonth, $currentYear, $i);
                }
                break;

            case 13: // 12 mois précédent
                for ($i = -11; $i <= 0; $i++) {
                    $selectedMonths[] = $this->generateMonthData($months, $currentMonth, $currentYear, $i);
                }
                break;

            case 14: // Année précédente
                $previousYear = $currentYear - 1;
                for ($i = 0; $i < 12; $i++) {
                    $selectedMonths[] = [
                        'month' => $months[$i],
                        'year' => $previousYear,
                        'key' => sprintf('%04d-%02d', $previousYear, $i + 1),
                    ];
                }
                break;
        }

        return $selectedMonths;
    }

    /**
     * Génère les données pour un mois spécifique basé sur un décalage par rapport au mois courant.
     */
    private function generateMonthData(array $months, int $currentMonth, int $currentYear, int $offset): array
    {
        $totalMonths = $currentMonth + $offset;
        $year = $currentYear + floor($totalMonths / 12);
        $monthIndex = ($totalMonths % 12 + 12) % 12; // Assure un index valide entre 0-11

        return [
            'month' => $months[$monthIndex],
            'year' => $year,
            'key' => sprintf('%04d-%02d', $year, $monthIndex + 1),
        ];
    }
}
