<?php

namespace App\Service\Hf\Atelier\Dit\Soumission\Ors;

use App\Dto\Hf\Atelier\Dit\Soumission\Ors\OrsDto;
use App\Service\Utils\Fichier\AbstractGeneratePdf;
use App\Service\Utils\Fichier\HeaderPdf;
use App\Service\Utils\Fichier\PdfTableGeneratorFlexible;

class OrsPdfService extends AbstractGeneratePdf
{
    /**
     * generer pdf pour la soumission OR
     */
    function GenererPdf(OrsDto $dto, string $nomAvecCheminFichier)
    {
        $pdf = new HeaderPdf($dto->emailDemandeur);
        $tableGenerator = new PdfTableGeneratorFlexible();

        $tableGenerator->setOptions([
            'table_attributes' => 'border="0" cellpadding="0" cellspacing="0" align="center" style="font-size: 8px;"',
            'header_row_style' => 'background-color: #D3D3D3;',
            'footer_row_style' => 'background-color: #D3D3D3;'
        ]);


        $pdf->AddPage();


        $pdf->setFont('helvetica', 'B', 17);
        $pdf->Cell(0, 6, 'Validation OR', 0, 0, 'C', false, '', 0, false, 'T', 'M');
        $pdf->Ln(10, true);

        // Début du bloc
        $pdf->setFont('helvetica', '', 10);
        $startX = $pdf->GetX();
        $startY = $pdf->GetY();

        $pdf->setFont('helvetica', 'B', 10);
        // Date de soumission
        $pdf->Cell(45, 6, 'Date soumission : ', 0, 0, 'L', false, '', 0, false, 'T', 'M');
        $pdf->setFont('helvetica', '', 10);
        $pdf->cell(50, 6, $dto->dateDemande ? $dto->dateDemande->format('d/m/Y') : '', 0, 1, '', false, '', 0, false, 'T', 'M');

        // numero devis
        $pdf->setAbsX(130);
        $pdf->setFont('helvetica', 'B', 10);
        $pdf->cell(20, 6, 'N° Devis :', 0, 0, '', false, '', 0, false, 'T', 'M');
        $pdf->setFont('helvetica', '', 10);
        $pdf->cell(0, 6, $dto->numeroDevis ?: 0, 0, 0, '', false, '', 0, false, 'T', 'M');

        // Numéro OR
        $pdf->SetXY($startX, $pdf->GetY() + 2);
        $pdf->setFont('helvetica', 'B', 10);
        $pdf->Cell(45, 6, 'Numéro OR : ', 0, 0, 'L', false, '', 0, false, 'T', 'M');
        $pdf->setFont('helvetica', '', 10);
        $pdf->cell(50, 6, $dto->numeroOr ?: '', 0, 1, '', false, '', 0, false, 'T', 'M');

        //sortie pol
        $pdf->setAbsX(130);
        $pdf->setFont('helvetica', 'B', 10);
        $pdf->cell(20, 6, 'Sortie POL : ', 0, 0, '', false, '', 0, false, 'T', 'M');
        $pdf->setFont('helvetica', '', 10);
        $pdf->cell(0, 6, ' ' . ($dto->estPiecePol ?: 'NON'), 0, 0, '', false, '', 0, false, 'T', 'M');

        // Version à valider
        $pdf->SetXY($startX, $pdf->GetY() + 2);
        $pdf->setFont('helvetica', 'B', 10);
        $pdf->Cell(45, 6, 'Version à valider : ', 0, 0, 'L', false, '', 0, false, 'T', 'M');
        $pdf->setFont('helvetica', '', 10);
        $pdf->cell(50, 6, $dto->numeroVersion ?: 0, 0, 1, '', false, '', 0, false, 'T', 'M');

        // sortie magasin
        $pdf->SetXY($startX, $pdf->GetY() + 2);
        $pdf->setFont('helvetica', 'B', 10);
        $pdf->Cell(45, 6, 'Sortie magasin : ', 0, 0, 'L', false, '', 0, false, 'T', 'M');
        $pdf->setFont('helvetica', '', 10);
        $pdf->cell(50, 6, $dto->estPieceSortieMagasin ?: 'NON', 0, 1, '', false, '', 0, false, 'T', 'M');

        // Achat locaux
        $pdf->SetXY($startX, $pdf->GetY() + 2);
        $pdf->setFont('helvetica', 'B', 10);
        $pdf->Cell(45, 6, 'Achat locaux : ', 0, 0, 'L', false, '', 0, false, 'T', 'M');
        $pdf->setFont('helvetica', '', 10);
        $pdf->cell(50, 6, $dto->estPieceAchatLocaux ?: 'NON', 0, 1, '', false, '', 0, false, 'T', 'M');

        // Fin du bloc
        $pdf->Ln(10, true);

        // ================================================================================================
        // Tableau pour la situation de l'OR

        $html = $tableGenerator->generateTable(
            $this->headerSituationOr(),
            $dto->orsApresDtos,
            $this->footerSituationOr($dto)
        );

        $pdf->writeHTML($html, true, false, true, false, '');

        //$pdf->Ln(10, true);
        //===========================================================================================
        //Titre: Controle à faire
        $pdf->setFont('helvetica', 'B', 12);
        $pdf->Cell(0, 6, 'Contrôle à faire (par rapport dernière version) : ', 0, 0, 'L', false, '', 0, false, 'T', 'M');
        $pdf->Ln(10, true);

        $stats = $this->calculerStatsControle($dto);

        $pdf->setFont('helvetica', '', 10);
        //Nouvelle intervention
        $pdf->Cell(45, 6, ' - Nouvelle intervention : ', 0, 0, 'L', false, '', 0, false, 'T', 'M');
        $pdf->cell(50, 5, $stats['nbrNouv'], 0, 0, '', false, '', 0, false, 'T', 'M');
        $pdf->Ln(5, true);

        //intervention supprimer

        $pdf->Cell(45, 6, ' - Intervention supprimée : ', 0, 0, 'L', false, '', 0, false, 'T', 'M');
        $pdf->cell(50, 5, $stats['nbrSupp'], 0, 0, '', false, '', 0, false, 'T', 'M');
        $pdf->Ln(5, true);

        //nombre ligne modifiée
        $pdf->Cell(45, 6, ' - Nombre ligne modifiée :', 0, 0, 'L', false, '', 0, false, 'T', 'M');
        $pdf->cell(50, 5, $stats['nbrModif'], 0, 0, '', false, '', 0, false, 'T', 'M');
        $pdf->Ln(5, true);

        //montant total modifié
        $pdf->Cell(45, 6, ' - Montant total modifié :', 0, 0, 'L', false, '', 0, false, 'T', 'M');
        $pdf->cell(50, 5, number_format($stats['mttModif'], 2, ',', ' '), 0, 0, '', false, '', 0, false, 'T', 'M');

        $pdf->Ln(10, true);

        //==========================================================================================================
        //Titre: Récapitulation de l'OR
        $this->addTitle($pdf, "Récapitulation de l'OR", 'helvetica',  'B', 10, 'L',  5);


        $pdf->setFont('helvetica', '', 12);
        $html = $tableGenerator->generateTable(
            $this->headerRecapitulationOR(),
            $dto->orsParInterventionDtos,
            $this->footerRecapitulationOR($dto)
        );

        $pdf->writeHTML($html, true, false, true, false, '');
        $pdf->Ln(10, true);
        //==========================================================================================================
        //Titre: Pièce(s) à faible activité d'achat
        $pdf->SetTextColor(255, 0, 0);
        $this->addTitle($pdf, empty($dto->pieceFaibleAchatDtos) ? '' : "Attention : les prix des pièces ci-dessous sont susceptibles d’augmenter. Merci de les confirmer auprès du service Magasin.", 'helvetica', 'B', 10, 'L', 1);

        $pdf->SetTextColor(0, 0, 0);
        if (!empty($dto->pieceFaibleAchatDtos)) {
            $pdf->setFont('helvetica', '', 12);
            $html = $tableGenerator->generateTable(
                $this->headerPieceFaibleActivite(),
                $dto->pieceFaibleAchatDtos,
                []
            );

            $pdf->writeHTML($html, true, false, true, false, '');
        }
        //==========================================================================================================
        //Titre: Observation
        $pdf->setFont('helvetica', 'B', 12);
        $pdf->Cell(30, 6, 'Observation : ', 0, 0, 'L', false, '', 0, false, 'T', 'M');
        $pdf->setFont('helvetica', '', 9);
        $pdf->MultiCell(164, 60, (string) ($dto->observation ?: ''), 0, '', 0, 0, '', '', true);

        //==========================================================================================================


        $pdf->Output($nomAvecCheminFichier, 'F');
    }

    private function calculerStatsControle(OrsDto $dto): array
    {
        $stats = ['nbrNouv' => 0, 'nbrSupp' => 0, 'nbrModif' => 0, 'mttModif' => 0.0];
        foreach ($dto->orsApresDtos as $compDto) {
            if ($compDto->statut === 'Nouv') {
                $stats['nbrNouv']++;
            } elseif ($compDto->statut === 'Supp') {
                $stats['nbrSupp']++;
            } elseif ($compDto->statut === 'Modif') {
                $stats['nbrModif']++;
            }
            // On calcule l'écart de montant absolu
            $stats['mttModif'] += abs($compDto->mttTotalAp - $compDto->mttTotalAv);
        }
        return $stats;
    }

    /**===============================================================
     * -------- Pour le tableau Situation de l'OR ------------------
     *================================================================*/

    private function headerSituationOr(): array
    {
        return  [
            [
                'key'          => 'itv',
                'label'        => 'ITV',
                'width'        => 40,
                'header_style' => 'font-weight: 900;',
                'cell_style'   => '',
            ],
            [
                'key'          => 'libelleItv',
                'label'        => 'Libellé ITV',
                'width'        => 150,
                'header_style' => 'font-weight: bold;',
                'cell_style'   => 'text-align: left;',
            ],
            [
                'key'          => 'datePlanning',
                'label'        => 'Date pla',
                'width'        => 50,
                'header_style' => 'font-weight: bold;',
                'cell_style'   => 'text-align: left;',
                'type'         => 'date'
            ],
            [
                'key'          => 'nbLigAv',
                'label'        => 'Nb Lig av',
                'width'        => 50,
                'header_style' => 'font-weight: bold;',
                'cell_style'   => '',
            ],
            [
                'key'          => 'nbLigAp',
                'label'        => 'Nb Lig ap',
                'width'        => 50,
                'header_style' => 'font-weight: bold;',
                'cell_style'   => '',
            ],
            [
                'key'          => 'mttTotalAv',
                'label'        => 'Mtt Total av',
                'width'        => 80,
                'header_style' => 'font-weight: bold; text-align: center;',
                'cell_style'   => 'text-align: right;',
                'type'         => 'number'
            ],
            [
                'key'          => 'mttTotalAp',
                'label'        => 'Mtt total ap',
                'width'        => 80,
                'header_style' => 'font-weight: bold; text-align: center;',
                'cell_style'   => 'text-align: right;',
                'type'         => 'number'
            ],
            [
                'key'          => 'statut',
                'label'        => 'Statut',
                'width'        => 40,
                'header_style' => 'font-weight: bold; text-align: center;',
                'cell_style'   => 'text-align: left;',
                'styler'       => function ($value, $row) {
                    switch ($value) {
                        case 'Supp':
                            return 'background-color: #FF0000;';
                        case 'Modif':
                            return 'background-color: #FFFF00;';
                        case 'Nouv':
                            return 'background-color: #00FF00;';
                        default:
                            return '';
                    }
                }
            ]
        ];
    }

    private function footerSituationOr(OrsDto $dto): array
    {
        $totalNbLigAv = 0;
        $totalNbLigAp = 0;
        $totalMttTotalAv = 0.0;
        $totalMttTotalAp = 0.0;

        foreach ($dto->orsApresDtos as $itv) {
            $totalNbLigAv += $itv->nbLigAv;
            $totalNbLigAp += $itv->nbLigAp;
            $totalMttTotalAv += $itv->mttTotalAv;
            $totalMttTotalAp += $itv->mttTotalAp;
        }

        return [
            'itv'              => '',
            'libelleItv'       => '',
            'datePlanning'     => 'TOTAL',
            'nbLigAv'          => $totalNbLigAv,
            'nbLigAp'          => $totalNbLigAp,
            'mttTotalAv'       => $totalMttTotalAv,
            'mttTotalAp'       => $totalMttTotalAp,
            'statut'           => ''
        ];
    }

    private function getDynamicStyle($key, $value)
    {
        $styles = '';
        if ($key === 'statut') {
            switch ($value) {
                case 'Supp':
                    $styles .= 'background-color: #FF0000;';
                    break;
                case 'Modif':
                    $styles .= 'background-color: #FFFF00;';
                    break;
                case 'Nouv':
                    $styles .= 'background-color: #00FF00;';
                    break;
            }
        }
        return $styles;
    }

    /**===============================================================
     * -------- Pour le tableau Recapitulation de l'OR ------------------
     *================================================================*/

    private function headerRecapitulationOR(): array
    {
        return [
            [
                'key'          => 'itv',
                'label'        => 'ITV',
                'width'        => 40,
                'style'        => 'font-weight: 900;',
                'header_style' => 'font-weight: 900;',
                'cell_style'   => '',
                'footer_style' => 'font-weight: 900;'
            ],
            [
                'key'          => 'mttTotal',
                'label'        => 'Mtt Total',
                'width'        => 70,
                'style'        => 'font-weight: bold; text-align: center;',
                'header_style' => 'font-weight: bold; text-align: center;',
                'cell_style'   => 'text-align: right;',
                'footer_style' => 'font-weight: bold; text-align: right;',
                'type'         => 'number'
            ],
            [
                'key'          => 'mttPieces',
                'label'        => 'Mtt Pièces',
                'width'        => 60,
                'style'        => 'font-weight: bold; text-align: center;',
                'header_style' => 'font-weight: bold; text-align: center;',
                'cell_style'   => 'text-align: right;',
                'footer_style' => 'font-weight: bold; text-align: right;',
                'type'         => 'number'
            ],
            [
                'key'          => 'mttMo',
                'label'        => 'Mtt MO',
                'width'        => 60,
                'style'        => 'font-weight: bold; text-align: center;',
                'header_style' => 'font-weight: bold; text-align: center;',
                'cell_style'   => 'text-align: right;',
                'footer_style' => 'font-weight: bold; text-align: right;',
                'type'         => 'number'
            ],
            [
                'key'          => 'mttSt',
                'label'        => 'Mtt ST',
                'width'        => 80,
                'style'        => 'font-weight: bold; text-align: center;',
                'header_style' => 'font-weight: bold; text-align: center;',
                'cell_style'   => 'text-align: right;',
                'footer_style' => 'font-weight: bold; text-align: right;',
                'type'         => 'number'
            ],
            [
                'key'          => 'mttLub',
                'label'        => 'Mtt LUB',
                'width'        => 80,
                'style'        => 'font-weight: bold; text-align: center;',
                'header_style' => 'font-weight: bold; text-align: center;',
                'cell_style'   => 'text-align: right;',
                'footer_style' => 'font-weight: bold; text-align: right;',
                'type'         => 'number'
            ],
            [
                'key'          => 'mttAutres',
                'label'        => 'Mtt Autres',
                'width'        => 80,
                'style'        => 'font-weight: bold; text-align: center;',
                'header_style' => 'font-weight: bold; text-align: center;',
                'cell_style'   => 'text-align: right;',
                'footer_style' => 'font-weight: bold; text-align: right;',
                'type'         => 'number'
            ]
        ];
    }

    private function footerRecapitulationOR(OrsDto $dto): array
    {
        $total = $dto->totalOrsParIntervention[0] ?? null;
        return [
            'itv'              => 'TOTAL',
            'mttTotal'         => $total ? $total->montantItv : 0,
            'mttPieces'        => $total ? $total->montantPiece : 0,
            'mttMo'            => $total ? $total->montantMo : 0,
            'mttSt'            => $total ? $total->montantAchatLocaux : 0,
            'mttLub'           => $total ? $total->montantLubrifiants : 0,
            'mttAutres'        => $total ? $total->montantFraisDivers : 0
        ];
    }

    /**============================================================================
     * -------- Pour le tableau pièces à faible activité d'achat ------------------
     *=============================================================================*/
    private function headerPieceFaibleActivite()
    {
        return [
            [
                'key'          => 'numero_itv',
                'label'        => 'ITV',
                'width'        => 40,
                'style'        => 'font-weight: bold;',
                'header_style' => 'font-weight: bold;',
                'cell_style'   => '',
                'footer_style' => 'font-weight: 900;'
            ],
            [
                'key'          => 'libelle_itv',
                'label'        => 'Libellé ITV',
                'width'        => 150,
                'style'        => 'font-weight: bold;',
                'header_style' => 'font-weight: bold;',
                'cell_style'   => 'text-align: left;',
                'footer_style' => 'font-weight: 900;'
            ],
            [
                'key'          => 'constructeur',
                'label'        => 'Const',
                'width'        => 40,
                'style'        => 'font-weight: bold;',
                'header_style' => 'font-weight: bold;',
                'cell_style'   => '',
                'footer_style' => 'font-weight: 900;'
            ],
            [
                'key'          => 'reference',
                'label'        => 'Réfp.',
                'width'        => 40,
                'style'        => 'font-weight: bold;',
                'header_style' => 'font-weight: bold;',
                'cell_style'   => '',
                'footer_style' => 'font-weight: 900;'
            ],
            [
                'key'          => 'designation',
                'label'        => 'Designation',
                'width'        => 150,
                'style'        => 'font-weight: bold;',
                'header_style' => 'font-weight: bold;',
                'cell_style'   => 'text-align: left;',
                'footer_style' => 'font-weight: 900;'
            ],
            [
                'key'          => 'pmp',
                'label'        => 'Pmp',
                'width'        => 80,
                'style'        => 'font-weight: bold;',
                'header_style' => 'font-weight: bold;',
                'cell_style'   => 'text-align: right;',
                'footer_style' => 'font-weight: 900;',
                'type'         => 'number'
            ],
            [
                'key'          => 'date_derniere_cde',
                'label'        => 'Date dern cmd',
                'width'        => 50,
                'style'        => 'font-weight: bold; text-align: center;',
                'header_style' => 'font-weight: bold;',
                'cell_style'   => 'text-align: center;',
                'footer_style' => 'font-weight: bold;',
                'default_value' => 'jamais commandé',
                'type'         => 'date'
            ],
        ];
    }
}
