<?php

namespace App\Controller\Base;

use App\Contract\Export\ExcelExportInterface;
use App\Service\Utils\Export\ExcelService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;

/**
 * Classe abstraite pour les contrôleurs d'export Excel
 * 
 * Cette classe fournit une implémentation de base pour tous les exports Excel,
 * en gérant automatiquement la combinaison des en-têtes et des données,
 * ainsi que l'appel au service d'export.
 */
abstract class AbstractExcelExportController extends AbstractController implements ExcelExportInterface
{
    /**
     * Exporte les données vers un fichier Excel et le retourne au navigateur
     * 
     * Cette méthode combine automatiquement les en-têtes (getHeaders) et les lignes (getRows)
     * puis utilise le ExcelService pour générer et retourner le fichier Excel.
     * 
     * @param ExcelService $excelService Service d'export Excel
     * @return Response Réponse HTTP contenant le fichier Excel
     */
    protected function exportToExcel(ExcelService $excelService): Response
    {
        $data = [$this->getHeaders(), ...$this->getRows()];

        return $excelService->exportToBrowser($data, $this->getFilename());
    }
}
