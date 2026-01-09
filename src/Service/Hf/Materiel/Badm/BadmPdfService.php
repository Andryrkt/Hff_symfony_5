<?php

namespace App\Service\Hf\Materiel\Badm;

use TCPDF;
use App\Service\Utils\FormattingService;
use App\Dto\Hf\Materiel\Badm\SecondFormDto;
use App\Service\Utils\Fichier\AbstractGeneratePdf;

class BadmPdfService extends AbstractGeneratePdf
{
    private string $projectDir;
    private FormattingService $formattingService;

    public function __construct(
        string $projectDir,
        FormattingService $formattingService
    ) {
        $this->projectDir = $projectDir;
        $this->formattingService = $formattingService;
    }

    /**
     * copie la page de garde du BADM dans docuware
     *
     * @param string $cheminVersDw
     * @param string $filePathName
     * 
     * @return void
     */
    public function copyToDW(string $cheminVersDw, string $filePathName): void
    {
        $this->copyFile($filePathName, $cheminVersDw);
    }

    /**
     * Genere le PDF BADM
     */
    public function genererPDF(SecondFormDto $dto, string $finalPdfPath): void
    {
        $pdf = new TCPDF();

        $pdf->AddPage();


        $pdf->setFont('helvetica', 'B', 14);
        $pdf->setAbsY(11);
        $logoPath = $this->projectDir . '/assets/images/henrifraise.jpg';
        $pdf->Image($logoPath, '', '', 45, 12);
        $pdf->setAbsX(55);
        //$pdf->Cell(45, 12, 'LOGO', 0, 0, '', false, '', 0, false, 'T', 'M');
        $pdf->Cell(110, 6, 'BORDEREAU DE MOUVEMENT DE MATERIEL', 0, 0, '', false, '', 0, false, 'T', 'M');
        $pdf->setAbsX(170);
        $pdf->setFont('helvetica', 'B', 10);


        $pdf->Cell(35, 6, $dto->numeroBadm, 0, 0, 'L', false, '', 0, false, 'T', 'M');

        $pdf->Ln(6, true);

        $pdf->setFont('helvetica', 'B', 12);
        $pdf->setAbsX(55);
        if ($dto->typeMouvement === 'CHANGEMENT DE CASIER') {
            $pdf->SetFillColor(155, 155, 155);
            $pdf->cell(110, 6, $dto->typeMouvement, 0, 0, 'C', true, '', 0, false, 'T', 'M');
        } elseif ($dto->typeMouvement === 'MISE AU REBUT') {
            $pdf->SetFillColor(255, 69, 0);
            $pdf->cell(110, 6, $dto->typeMouvement, 0, 0, 'C', true, '', 0, false, 'T', 'M');
        } elseif ($dto->typeMouvement === 'CESSION D\'ACTIF') {
            $pdf->SetFillColor(240, 0, 32);
            $pdf->cell(110, 6, $dto->typeMouvement, 0, 0, 'C', true, '', 0, false, 'T', 'M');
        } elseif ($dto->typeMouvement === 'CHANGEMENT AGENCE/SERVICE') {
            $pdf->SetFillColor(0, 128, 255);
            $pdf->cell(110, 6, $dto->typeMouvement, 0, 0, 'C', true, '', 0, false, 'T', 'M');
        } elseif ($dto->typeMouvement === 'ENTREE EN PARC') {
            $pdf->SetFillColor(0, 86, 27);
            $pdf->SetTextColor(255, 255, 255);
            $pdf->cell(110, 6, $dto->typeMouvement, 0, 0, 'C', true, '', 0, false, 'T', 'M');
        }
        $pdf->SetTextColor(0, 0, 0);
        $pdf->setFont('helvetica', 'B', 10);
        $pdf->setAbsX(170);
        $pdf->cell(35, 6, 'Le : ' . $dto->dateDemande->format('d/m/Y'), 0, 0, '', false, '', 0, false, 'T', 'M');
        $pdf->Ln(10, true);

        $pdf->setFont('helvetica', 'B', 12);
        $pdf->SetTextColor(14, 65, 148);
        $pdf->Cell(50, 6, 'Caractéristiques du matériel', 0, 0, '', false, '', 0, false, 'T', 'M');
        $pdf->SetFillColor(14, 65, 148);
        $pdf->setAbsXY(70, 28);
        $pdf->Rect($pdf->GetX(), $pdf->GetY(), 130, 3, 'F');
        $pdf->Ln(10, true);

        $pdf->SetTextColor(0, 0, 0);
        $pdf->setFont('helvetica', 'B', 10);


        $pdf->cell(25, 6, 'Désignation :', 0, 0, '', false, '', 0, false, 'T', 'M');
        $pdf->cell(70, 6, $dto->designation, 1, 0, '', false, '', 0, false, 'T', 'M');
        $pdf->setAbsX(150);
        $pdf->cell(12, 6, 'N° ID :', 0, 0, '', false, '', 0, false, 'T', 'M');
        $pdf->cell(0, 6, $dto->idMateriel, 1, 0, '', false, '', 0, false, 'T', 'M');
        $pdf->Ln(10, true);

        $pdf->cell(25, 6, 'N° Série :', 0, 0, '', false, '', 0, false, 'T', 'M');
        $pdf->cell(70, 6, $dto->numSerie, 1, 0, '', false, '', 0, false, 'T', 'M');
        $pdf->setAbsX(130);
        $pdf->cell(20, 6, 'Groupe :', 0, 0, '', false, '', 0, false, 'T', 'M');
        $pdf->cell(0, 6, $dto->groupe, 1, 0, '', false, '', 0, false, 'T', 'M');
        $pdf->Ln(10, true);


        $pdf->cell(25, 6, 'N° Parc :', 0, 0, '', false, '', 0, false, 'T', 'M');
        $pdf->cell(30, 6, $dto->numParc, 1, 0, '', false, '', 0, false, 'T', 'M');
        $pdf->setAbsX(70);
        $pdf->cell(23, 6, 'Affectation:', 0, 0, '', false, '', 0, false, 'T', 'M');
        $pdf->cell(35, 6, $dto->affectation, 1, 0, '', false, '', 0, false, 'T', 'M');
        $pdf->setAbsX(130);
        $pdf->cell(30, 6, 'Constructeur :', 0, 0, '', false, '', 0, false, 'T', 'M');
        $pdf->cell(0, 6, $dto->constructeur, 1, 0, '', false, '', 0, false, 'T', 'M');
        $pdf->Ln(10, true);

        $pdf->cell(25, 6, 'Date d’achat :', 0, 0, '', false, '', 0, false, 'T', 'M');
        $pdf->cell(30, 6, $dto->dateAchat, 1, 0, '', false, '', 0, false, 'T', 'M');
        $pdf->setAbsX(70);
        $pdf->MultiCell(23, 6, "Année :", 0, 'L', false, 0);
        $pdf->cell(35, 6, $dto->anneeDuModele, 1, 0, '', false, '', 0, false, 'T', 'M');
        $pdf->setAbsX(140);
        $pdf->cell(20, 6, 'Modèle :', 0, 0, '', false, '', 0, false, 'T', 'M');
        $pdf->cell(0, 6, $dto->modele, 1, 0, '', false, '', 0, false, 'T', 'M');
        $pdf->Ln(10, true);


        $pdf->setFont('helvetica', 'B', 12);
        $pdf->SetTextColor(14, 65, 148);
        $pdf->Cell(40, 6, 'Etat machine', 0, 0, '', false, '', 0, false, 'T', 'M');
        $pdf->SetFillColor(14, 65, 148);
        $pdf->setAbsXY(40, 80);
        $pdf->Rect($pdf->GetX(), $pdf->GetY(), 160, 3, 'F');
        $pdf->Ln(10, true);

        $pdf->SetTextColor(0, 0, 0);
        $pdf->setFont('helvetica', 'B', 10);

        $pdf->MultiCell(25, 6, "Heures :", 0, 'L', false, 0);
        $pdf->cell(30, 6, $dto->heureMachine, 1, 0, '', false, '', 0, false, 'T', 'M');
        $pdf->setAbsX(70);
        $pdf->MultiCell(25, 6, "OR :", 0, 'L', false, 0);
        $pdf->cell(35, 6, $dto->estOr, 1, 0, '', false, '', 0, false, 'T', 'M');
        $pdf->setAbsX(135);
        $pdf->cell(25, 6, 'Kilométrage :', 0, 0, '', false, '', 0, false, 'T', 'M');
        $pdf->cell(0, 6, $dto->kmMachine, 1, 0, '', false, '', 0, false, 'T', 'M');
        $pdf->Ln(10, true);



        $pdf->setFont('helvetica', 'B', 12);
        $pdf->SetTextColor(14, 65, 148);
        $pdf->Cell(40, 6, 'Service émetteur', 0, 0, '', false, '', 0, false, 'T', 'M');
        $pdf->SetFillColor(14, 65, 148);
        $pdf->setAbsXY(50, 102);
        $pdf->Rect($pdf->GetX(), $pdf->GetY(), 150, 3, 'F');
        $pdf->Ln(10, true);

        $pdf->SetTextColor(0, 0, 0);
        $pdf->setFont('helvetica', 'B', 10);

        $pdf->cell(35, 6, 'Agence - Service :', 0, 0, '', false, '', 0, false, 'T', 'M');
        $pdf->cell(63, 6, $dto->emetteur['agence']->getCode() . ' - ' . $dto->emetteur['service']->getCode(), 1, 0, '', false, '', 0, false, 'T', 'M');
        $pdf->setAbsX(130);
        $pdf->cell(20, 6, 'Casier :', 0, 0, '', false, '', 0, false, 'T', 'M');
        $pdf->cell(0, 6, $dto->emetteur['casier']->getNom(), 1, 0, '', false, '', 0, false, 'T', 'M');
        $pdf->Ln(10, true);



        $pdf->setFont('helvetica', 'B', 12);
        $pdf->SetTextColor(14, 65, 148);
        $pdf->Cell(40, 6, 'Service destinataire', 0, 0, '', false, '', 0, false, 'T', 'M');
        $pdf->SetFillColor(14, 65, 148);
        $pdf->setAbsXY(54, 124);
        $pdf->Rect($pdf->GetX(), $pdf->GetY(), 147, 3, 'F');
        $pdf->Ln(10, true);

        $pdf->SetTextColor(0, 0, 0);
        $pdf->setFont('helvetica', 'B', 10);

        $pdf->cell(35, 6, 'Agence - Service :', 0, 0, '', false, '', 0, false, 'T', 'M');
        $pdf->cell(63, 6, $dto->destinataire['agence']->getCode() . ' - ' . $dto->destinataire['service']->getCode(), 1, 0, '', false, '', 0, false, 'T', 'M');
        $pdf->setAbsX(130);
        $pdf->cell(20, 6, 'Casier :', 0, 0, '', false, '', 0, false, 'T', 'M');
        $pdf->cell(0, 6, $dto->destinataire['casier']->getNom(), 1, 0, '', false, '', 0, false, 'T', 'M');
        $pdf->Ln(10, true);


        $pdf->cell(35, 6, 'Motif :', 0, 0, '', false, '', 0, false, 'T', 'M');
        $pdf->cell(0, 6, $dto->motifMateriel, 1, 0, '', false, '', 0, false, 'T', 'M');
        $pdf->Ln(10, true);



        $pdf->setFont('helvetica', 'B', 12);
        $pdf->SetTextColor(14, 65, 148);
        $pdf->Cell(40, 6, 'Entrée en parc', 0, 0, '', false, '', 0, false, 'T', 'M');
        $pdf->SetFillColor(14, 65, 148);
        $pdf->setAbsXY(43, 156);
        $pdf->Rect($pdf->GetX(), $pdf->GetY(), 158, 3, 'F');
        $pdf->Ln(10, true);

        $pdf->SetTextColor(0, 0, 0);
        $pdf->setFont('helvetica', 'B', 10);

        $pdf->cell(35, 6, 'Etat à l’achat:', 0, 0, '', false, '', 0, false, 'T', 'M');
        $pdf->cell(63, 6, $dto->etatAchat, 1, 0, '', false, '', 0, false, 'T', 'M');
        $pdf->setAbsX(110);
        $pdf->cell(50, 6, 'Date de mise en location :', 0, 0, '', false, '', 0, false, 'T', 'M');
        $pdf->cell(0, 6, $dto->dateMiseLocation->format('d/m/Y'), 1, 0, '', false, '', 0, false, 'T', 'M');
        $pdf->Ln(10, true);



        $pdf->setFont('helvetica', 'B', 12);
        $pdf->SetTextColor(14, 65, 148);

        $pdf->Cell(40, 6, 'Valeur (MGA)', 0, 0, '', false, '', 0, false, 'T', 'M');
        $pdf->SetFillColor(14, 65, 148);
        $pdf->setAbsXY(41, 178);
        $pdf->Rect($pdf->GetX(), $pdf->GetY(), 160, 3, 'F');
        $pdf->Ln(10, true);

        $pdf->SetTextColor(0, 0, 0);
        $pdf->setFont('helvetica', 'B', 10);


        $pdf->MultiCell(35, 6, "Coût d’acquisition:", 0, 'L', false, 0);
        $pdf->cell(63, 6, $this->formattingService->formatNumber($dto->coutAcquisition), 1, 0, '', false, '', 0, false, 'T', 'M');
        $pdf->setAbsX(110);
        $pdf->cell(20, 6, 'Amort :', 0, 0, '', false, '', 0, false, 'T', 'M');
        $pdf->cell(0, 6, $this->formattingService->formatNumber($dto->amortissement), 1, 0, '', false, '', 0, false, 'T', 'M');
        $pdf->SetFillColor(0, 0, 0);
        $pdf->setAbsXY(130, 196);
        $pdf->Rect($pdf->GetX(), $pdf->GetY(), 70, 1, 'F');
        $pdf->Ln(3, true);
        $pdf->setAbsX(110);
        $pdf->cell(20, 6, 'VNC :', 0, 0, '', false, '', 0, false, 'T', 'M');
        $pdf->cell(0, 6, $this->formattingService->formatNumber($dto->valeurNetComptable), 1, 0, '', false, '', 0, false, 'T', 'M');
        $pdf->Ln(10, true);


        $pdf->setFont('helvetica', 'B', 12);
        $pdf->SetTextColor(14, 65, 148);
        $pdf->Cell(40, 6, 'Cession d’actif', 0, 0, '', false, '', 0, false, 'T', 'M');
        $pdf->SetFillColor(14, 65, 148);
        $pdf->setAbsXY(44, 210);
        $pdf->Rect($pdf->GetX(), $pdf->GetY(), 158, 3, 'F');
        $pdf->Ln(10, true);

        $pdf->SetTextColor(0, 0, 0);
        $pdf->setFont('helvetica', 'B', 10);

        $pdf->MultiCell(35, 6, "Nom client :", 0, 'L', false, 0);
        $pdf->cell(63, 6, $dto->nomClient, 1, 0, '', false, '', 0, false, 'T', 'M');
        $pdf->setAbsX(110);
        $pdf->MultiCell(25, 6, "Modalité de\npaiement :", 0, 'L', false, 0);
        $pdf->cell(0, 6, $dto->modalitePaiement, 1, 0, '', false, '', 0, false, 'T', 'M');
        $pdf->Ln(10, true);
        $pdf->cell(35, 6, 'Prix HT :', 0, 0, '', false, '', 0, false, 'T', 'M');
        $pdf->cell(63, 6, $this->formattingService->formatNumber($dto->prixVenteHt), 1, 0, '', false, '', 0, false, 'T', 'M');
        $pdf->Ln(10, true);

        /** DEBUT MISE AU REBUT */
        $pdf->setFont('helvetica', 'B', 12);
        $pdf->SetTextColor(14, 65, 148);
        $pdf->Cell(40, 6, 'Mise au rebut', 0, 0, '', false, '', 0, false, 'T', 'M');
        $pdf->SetFillColor(14, 65, 148);
        $pdf->setAbsXY(41, 242);
        $pdf->Rect($pdf->GetX(), $pdf->GetY(), 160, 3, 'F');
        $pdf->Ln(10, true);


        $pdf->SetTextColor(0, 0, 0);
        $pdf->setFont('helvetica', 'B', 10);

        $pdf->cell(35, 6, 'Motif :', 0, 0, '', false, '', 0, false, 'T', 'M');
        $pdf->cell(0, 6, $dto->motifMiseRebut, 1, 0, '', false, '', 0, false, 'T', 'M');
        $pdf->Ln(10, true);
        /** FIN MISE AUREBUT */


        // entête email
        $pdf->SetTextColor(0, 0, 0);
        $pdf->SetFont('helvetica', 'BI', 10);
        $pdf->SetXY(118, 2);
        $pdf->Cell(35, 6, 'Email émetteur : ' . $dto->mailUser, 0, 0, 'L');

        //2ème pages
        if ($dto->estOr === 'OUI') {
            $this->affichageListOr($pdf, $dto->ors);
        }


        $pdf->Output($finalPdfPath, 'F');
    }



    /**
     * Recuperation et affichage des or dans une tableau
     *
     * @param [type] $pdf
     * @param array  $ors
     * @return void
     */
    public function affichageListOr($pdf, array $ors)
    {
        //ajouter une nouvelle page et changer l'orientation de la page
        $pdf->AddPage('L');

        // Commencer le tableau HTML
        $this->addTitle($pdf, 'Liste OR encours');

        $this->addTable($pdf, $this->headerTableau(), $ors, [], [], false);
    }

    private function headerTableau(): array
    {
        $formatterCodeAgenceService = function ($value) {
            return implode(' - ', array_map('trim', explode('-', $value)));
        };

        $formatterPourcentage = function ($value) {
            return $value . '%';
        };

        $styleBoldCenter = 'font-weight: bold; text-align: center;';
        $styleBoldLeft = 'font-weight: bold; text-align: left;';
        $styleBoldRight = 'font-weight: bold; text-align: right;';

        return [
            [
                'key' => 'nom_agence',
                'label' => 'Agence',
                'width' => 75,
                'style' => $styleBoldCenter,
            ],
            [
                'key' => 'nom_service',
                'label' => 'Service',
                'width' => 50,
                'style' => $styleBoldCenter,
            ],
            [
                'key' => 'numero_or',
                'label' => 'numor',
                'width' => 50,
                'style' => $styleBoldCenter,
            ],
            [
                'key' => 'date_deb',
                'label' => 'Date',
                'width' => 50,
                'style' => $styleBoldCenter,
                'type' => 'date',
            ],
            [
                'key' => 'ref_lib',
                'label' => 'ref',
                'width' => 90,
                'style' => $styleBoldLeft,
            ],
            [
                'key' => 'numero_intervention',
                'label' => 'interv',
                'width' => 30,
                'style' => $styleBoldCenter,
            ],
            [
                'key' => 'intitule_travaux',
                'label' => 'intitulé travaux',
                'width' => 230,
                'style' => $styleBoldLeft,
            ],
            [
                'key' => 'code_agence_service',
                'label' => 'Ag/Serv débiteur',
                'width' => 50,
                'style' => $styleBoldCenter,
                'formatter' => $formatterCodeAgenceService,
            ],
            [
                'key' => 'montant_total',
                'label' => 'Montant total',
                'width' => 50,
                'style' => $styleBoldRight,
                'type' => 'number',
            ],
            [
                'key' => 'montant_pieces',
                'label' => 'Montant pièces',
                'width' => 50,
                'style' => $styleBoldRight,
                'type' => 'number',
            ],
            [
                'key' => 'montant_pieces_livrees',
                'label' => 'Montant pièces livrées',
                'width' => 50,
                'style' => $styleBoldRight,
                'type' => 'number',
            ]
        ];
    }
}
