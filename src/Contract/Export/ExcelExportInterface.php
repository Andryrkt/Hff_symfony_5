<?php

namespace App\Contract\Export;

use App\Contract\Dto\SearchDtoInterface;
use Symfony\Component\HttpFoundation\Session\SessionInterface;

/**
 * Interface pour les contrôleurs d'export Excel
 * 
 * Cette interface définit le contrat que tous les contrôleurs d'export Excel
 * doivent respecter pour garantir une structure cohérente et standardisée.
 */
interface ExcelExportInterface
{
    /**
     * Retourne les en-têtes de colonnes pour l'export Excel
     * 
     * @return array Tableau des noms de colonnes
     */
    public function getHeaders(): array;

    /**
     * Retourne les lignes de données pour l'export Excel
     * 
     * @return array Tableau de tableaux représentant les lignes de données
     */
    public function getRows(): array;

    /**
     * Retourne le nom du fichier Excel à générer
     * 
     * @return string Nom du fichier (sans extension)
     */
    public function getFilename(): string;

    /**
     * Retourne le DTO de recherche qui est récupérer depuis la session
     * 
     * @return object DTO de recherche
     */
    public function getSearchDto(SessionInterface $session): SearchDtoInterface;
}
