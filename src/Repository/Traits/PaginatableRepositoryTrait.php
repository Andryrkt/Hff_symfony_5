<?php

namespace App\Repository\Traits;

trait PaginatableRepositoryTrait
{
    /**
     * Valide et retourne les paramètres de tri et la limite à partir du DTO.
     *
     * @param mixed  $searchDto
     * @param array  $sortableColumns Mapping des colonnes autorisées
     * @param string $defaultValue    Colonne de tri par défaut
     * @return array [int $limit, string $sortBy, string $sortOrder]
     */
    protected function sortAndLimit($searchDto, array $sortableColumns, string $defaultValue): array
    {
        // Récupérer les paramètres de tri depuis le DTO
        $sortBy = $searchDto->sortBy ?? $defaultValue;
        $sortOrder = strtoupper($searchDto->sortOrder ?? 'DESC');

        // Validation de sécurité
        if (!isset($sortableColumns[$sortBy])) {
            $sortBy = $defaultValue; // Valeur par défaut sécurisée
        }
        if (!in_array($sortOrder, ['ASC', 'DESC'])) {
            $sortOrder = 'DESC'; // Valeur par défaut sécurisée
        }

        // Récupérer la limite depuis le DTO
        $limit = $searchDto->limit ?? 50;

        return [$limit, $sortBy, $sortOrder];
    }
}
