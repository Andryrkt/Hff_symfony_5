<?php

namespace App\Service\Hf\Atelier\Dit\Soumission\Ors;

use App\Dto\Hf\Atelier\Dit\Soumission\Ors\OrsDto;
use App\Service\Utils\Fichier\AbstractGeneratePdf;
use App\Service\Utils\Fichier\HeaderPdf;

/**
 * Service de génération de PDF pour la soumission OR
 * Orchestre la mise en page en utilisant des services dédiés aux calculs et à la config
 */
class OrsPdfService extends AbstractGeneratePdf
{
    private OrsPdfCalculator $calculator;
    private OrsPdfTableConfig $tableConfig;

    public function __construct(OrsPdfCalculator $calculator, OrsPdfTableConfig $tableConfig)
    {
        $this->calculator = $calculator;
        $this->tableConfig = $tableConfig;
    }

    /**
     * Génère le PDF pour la soumission OR
     */
    public function GenererPdf(OrsDto $dto, string $nomAvecCheminFichier): void
    {
        $pdf = new HeaderPdf($dto->emailDemandeur);
        $pdf->AddPage();

        // 1. Titre principal
        $this->addTitle($pdf, 'Validation OR', 'helvetica', 'B', 17, 'C', 10);

        // 2. Bloc d'informations générales
        $this->renderHeaderInfos($pdf, $dto);

        $pdf->Ln(5);

        // 3. Situation de l'OR (Tableau)
        $this->addTable(
            $pdf,
            $this->tableConfig->getHeaderSituationOr(),
            $dto->orsApresDtos,
            $this->calculator->getFooterSituationOr($dto),
            [
                'table_attributes' => 'border="0" cellpadding="0" cellspacing="0" align="center" style="font-size: 8px;"',
                'header_row_style' => 'background-color: #D3D3D3;',
                'footer_row_style' => 'background-color: #D3D3D3;'
            ]
        );

        $pdf->Ln(5);

        // 4. Contrôle à faire (Statistiques)
        $this->renderControleSection($pdf, $dto);

        // 5. Récapitulation de l'OR
        $this->addTitle($pdf, "Récapitulation de l'OR", 'helvetica', 'B', 10, 'L', 5);
        $this->addTable(
            $pdf,
            $this->tableConfig->getHeaderRecapitulationOR(),
            $dto->orsParInterventionDtos,
            $this->calculator->getFooterRecapitulationOR($dto)
        );

        $pdf->Ln(5);

        // 6. Pièce(s) à faible activité d'achat (Conditionnel)
        $this->renderPieceFaibleSection($pdf, $dto);

        // 7. Observation
        $this->renderObservationSection($pdf, $dto);

        $pdf->Output($nomAvecCheminFichier, 'F');
    }

    /**
     * Affiche les informations d'en-tête (Date, N° Devis, Numéro OR, etc.)
     */
    private function renderHeaderInfos(HeaderPdf $pdf, OrsDto $dto): void
    {
        $pdf->setFont('helvetica', '', 10);
        $startY = $pdf->GetY();

        $detailsCol1 = [
            'Date soumission'    => $dto->dateDemande ? $dto->dateDemande->format('d/m/Y') : '',
            'Numéro OR'          => (string) ($dto->numeroOr ?: ''),
            'Version à valider'  => (string) ($dto->numeroVersion ?: '0'),
            'Sortie magasin'     => $dto->estPieceSortieMagasin ?: 'NON',
            'Achat locaux'       => $dto->estPieceAchatLocaux ?: 'NON',
        ];

        $detailsCol2 = [
            'N° Devis'           => (string) ($dto->numeroDevis ?: '0'),
            'Sortie POL'         => $dto->estPiecePol ?: 'NON',
        ];

        // Rendu Colonne 1
        $pdf->SetY($startY);
        foreach ($detailsCol1 as $label => $value) {
            $pdf->setFont('helvetica', 'B', 10);
            $pdf->Cell(45, 6, $label . ' : ', 0, 0, 'L');
            $pdf->setFont('helvetica', '', 10);
            $pdf->Cell(50, 6, $value, 0, 1, 'L');
        }
        $maxY = $pdf->GetY();

        // Rendu Colonne 2
        $pdf->SetY($startY);
        foreach ($detailsCol2 as $label => $value) {
            $pdf->setAbsX(130);
            $pdf->setFont('helvetica', 'B', 10);
            $pdf->Cell(20, 6, $label . ' :', 0, 0, 'L');
            $pdf->setFont('helvetica', '', 10);
            $pdf->Cell(0, 6, $value, 0, 1, 'L');
        }

        // On prend le Y le plus bas des deux colonnes pour la suite
        $pdf->SetY(max($maxY, $pdf->GetY()));
        $pdf->Ln(5);
    }

    /**
     * Affiche la section des statistiques de contrôle
     */
    private function renderControleSection(HeaderPdf $pdf, OrsDto $dto): void
    {
        $stats = $this->calculator->getStatsControle($dto);

        $this->addTitle($pdf, 'Contrôle à faire (par rapport dernière version) : ', 'helvetica', 'B', 12);

        $details = [
            'Nouvelle intervention'  => $stats['nbrNouv'],
            'Intervention supprimée' => $stats['nbrSupp'],
            'Nombre ligne modifiée'  => $stats['nbrModif'],
            'Montant total modifié'  => number_format($stats['mttModif'], 2, ',', ' ') . ' €',
        ];

        $this->addSummaryDetails($pdf, $details, 'helvetica', 10, 45, 50, 5, 10);
    }

    /**
     * Affiche la section des pièces à faible activité si nécessaire
     */
    private function renderPieceFaibleSection(HeaderPdf $pdf, OrsDto $dto): void
    {
        if (empty($dto->pieceFaibleAchatDtos)) {
            return;
        }

        $pdf->SetTextColor(255, 0, 0);
        $this->addTitle($pdf, "Attention : les prix des pièces ci-dessous sont susceptibles d’augmenter. Merci de les confirmer auprès du service Magasin.", 'helvetica', 'B', 10, 'L', 1);
        $pdf->SetTextColor(0, 0, 0);

        $this->addTable($pdf, $this->tableConfig->getHeaderPieceFaibleActivite(), $dto->pieceFaibleAchatDtos);
        $pdf->Ln(5);
    }

    /**
     * Affiche la section Observation
     */
    private function renderObservationSection(HeaderPdf $pdf, OrsDto $dto): void
    {
        $pdf->setFont('helvetica', 'B', 12);
        $pdf->Cell(30, 6, 'Observation : ', 0, 0, 'L');

        $pdf->setFont('helvetica', '', 9);
        $observation = (string) ($dto->observation ?: '');

        $pdf->MultiCell(0, 0, $observation, 0, 'L', false, 1, '', '', true);
    }
}
