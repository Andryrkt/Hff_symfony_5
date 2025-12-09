<?php

namespace App\Service\Hf\Rh\Dom;

use App\Dto\Hf\Rh\Dom\SecondFormDto;
use App\Service\Utils\Fichier\AbstractGeneratePdf;
use TCPDF;

class DomPdfService extends AbstractGeneratePdf
{
    private string $projectDir;

    public function __construct(string $projectDir)
    {
        $this->projectDir = $projectDir;
    }

    /**
     * copie la page de garde du BC magasin dans docuware
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
     * Genere le PDF DEMANDE D'ORDRE DE MISSION (DOM)
     */
    public function genererPDF(SecondFormDto $secondFormDto, string $filePathName)
    {
        $dateDebutMission = $secondFormDto->dateHeureMission['debut']->format('d/m/Y');
        $heureDebutMission = $secondFormDto->dateHeureMission['heureDebut']->format('H:i');
        $dateFinMission = $secondFormDto->dateHeureMission['fin']->format('d/m/Y');
        $heureFinMission = $secondFormDto->dateHeureMission['heureFin']->format('H:i');
        $typeMission = $secondFormDto->typeMission->getCodeSousType();
        $site = $secondFormDto->site->getNomZone();
        $categorie = $secondFormDto->categorie->getDescription();
        $codeAgenceDebiteur = $secondFormDto->debiteur['agence']->getCode();
        $codeServiceDebiteur = $secondFormDto->debiteur['service']->getCode();

        $pdf = new TCPDF();
        $font = "pdfatimesbi";

        $w50 = $this->getHalfWidth($pdf);
        $couleurTitre = [64, 64, 64]; // gris foncé

        $pdf->AddPage();

        $pieceJustificatif = $secondFormDto->pieceJustificatif ? 'PIÈCE À JUSTIFIER' : '';

        // tête de page 
        $pdf->setY(0);
        $pdf->SetFont($font, '', 10);
        $pdf->Cell($w50, 10, $pieceJustificatif, 0, 0, 'L');
        $pdf->SetTextColor(0, 0, 0);
        $pdf->SetFont($font, '', 8);
        $pdf->Cell($w50, 8, $secondFormDto->mailUser, 0, 1, 'R');

        // Logo HFF
        $logoPath = $this->projectDir . '/assets/images/logoHff.jpg';
        $pdf->Image($logoPath, 10, 10, 60, 0, 'jpg');

        // Grand titre du pdf
        $pdf->SetFont($font, 'B', 16);
        $pdf->setX($pdf->GetX() + 35);
        $pdf->Cell(0, 10, 'ORDRE DE MISSION ', 0, 0, 'C');
        $pdf->SetFont($font, '', 12);
        $pdf->Cell(0, 10, 'Le: ' . $secondFormDto->dateDemande->format('d/m/Y'), 0, 1, 'R');

        $pdf->SetTextColor(...$couleurTitre);
        $pdf->setX($pdf->GetX() + 35);
        $pdf->Cell(0, 10, 'Agence/Service débiteur : ' . $codeAgenceDebiteur . '-' . $codeServiceDebiteur, 0, 0, 'C');
        $pdf->SetTextColor(0, 0, 0);
        $pdf->setX($w50 + 10);
        $pdf->Cell(0, 10, $secondFormDto->numeroOrdreMission, 0, 1, 'R');


        $pdf->setTextColor(...$couleurTitre);
        $pdf->Cell(12, 10, 'Type : ', 0, 0);
        $pdf->setTextColor(0, 0, 0); // Noir
        $pdf->Cell($w50 - 12, 10, $typeMission, 0, 0);
        $pdf->Rect($pdf->GetX() - $w50, $pdf->GetY(), $w50, 10);  // Bordure englobant tout

        $pdf->SetFont($font, '', 10);

        /** SITE */
        $pdf->setTextColor(...$couleurTitre); // Bleu
        $pdf->Cell(11, 10, 'Site : ', 0, 0);
        $pdf->setTextColor(0, 0, 0); // Noir
        $pdf->Cell($w50 - 11, 10, $site, 0, 0);
        $pdf->Rect($pdf->GetX() - $w50, $pdf->GetY(), $w50, 10);  // Bordure englobant tout

        $pdf->Ln(); // Nouvelle ligne
        $pdf->SetFont($font, '', 12);

        /** AGENCE */
        $pdf->setTextColor(...$couleurTitre); // Bleu
        $pdf->Cell(17, 10, 'Agence : ', 0, 0);
        $pdf->setTextColor(0, 0, 0); // Noir
        $pdf->Cell($w50 - 17, 10, $secondFormDto->agenceUser, 0, 0);
        $pdf->Rect($pdf->GetX() - $w50, $pdf->GetY(), $w50, 10);  // Bordure englobant tout

        $pdf->SetFont($font, '', 10);

        /** SERVICE */
        $pdf->setTextColor(...$couleurTitre); // Bleu
        $pdf->Cell(17, 10, 'Service : ', 0, 0);
        $pdf->setTextColor(0, 0, 0); // Noir
        $pdf->Cell($w50 - 17, 10, $secondFormDto->serviceUser, 0, 0);
        $pdf->Rect($pdf->GetX() - $w50, $pdf->GetY(), $w50, 10);  // Bordure englobant tout

        $pdf->Ln(); // Nouvelle ligne

        $pdf->SetFont($font, '', 12);
        $pdf->Rect($pdf->GetX(), $pdf->GetY(), $w50 * 2, 10);  // Bordure englobant tout
        $pdf->setTextColor(...$couleurTitre);
        $pdf->Cell(12, 10, 'Nom : ', 0, 0);
        $pdf->setTextColor(0, 0, 0); // Noir
        $pdf->Cell((2 * $w50) - 12, 10, $secondFormDto->nom, 0, 1);

        $pdf->Rect($pdf->GetX(), $pdf->GetY(), $w50 * 2, 10);  // Bordure englobant tout
        $pdf->setTextColor(...$couleurTitre);
        $pdf->Cell(19, 10, 'Prénoms : ', 0, 0);
        $pdf->setTextColor(0, 0, 0); // Noir
        $pdf->Cell((2 * $w50) - 19, 10, $secondFormDto->prenom, 0, 1);

        $pdf->Rect($pdf->GetX(), $pdf->GetY(), $w50 * 2, 10);  // Bordure englobant tout
        $pdf->setTextColor(...$couleurTitre);
        $pdf->Cell(21, 10, 'Matricule : ', 0, 0);
        $pdf->setTextColor(0, 0, 0); // Noir
        $pdf->Cell((2 * $w50) - 21, 10, $secondFormDto->matricule, 0, 1);

        $pdf->Rect($pdf->GetX(), $pdf->GetY(), $w50 * 2, 10);  // Bordure englobant tout
        $pdf->setTextColor(...$couleurTitre);
        $pdf->Cell(13, 10, 'Motif : ', 0, 0);
        $pdf->setTextColor(0, 0, 0); // Noir
        $pdf->Cell((2 * $w50) - 13, 10, $secondFormDto->motifDeplacement, 0, 1);

        $pdf->Rect($pdf->GetX(), $pdf->GetY(), $w50 * 2, 10);  // Bordure englobant tout
        $pdf->setTextColor(...$couleurTitre);
        $pdf->Cell(20, 10, 'Catégorie : ', 0, 0);
        $pdf->setTextColor(0, 0, 0); // Noir
        $pdf->Cell((2 * $w50) - 20, 10, $categorie, 0, 1);

        $pdf->Rect($pdf->GetX(), $pdf->GetY(), $w50 * 2, 10);  // Bordure englobant tout
        $pdf->setTextColor(...$couleurTitre);
        $pdf->Cell(17, 10, 'Période : ', 0, 0);
        $pdf->setTextColor(0, 0, 0); // Noir
        $pdf->Cell((2 * $w50) - 17, 10, $secondFormDto->nombreJour . ' jour(s)       Du    ' . $dateDebutMission . '    à    ' . $heureDebutMission . '    Heures    au    ' . $dateFinMission . '    à    ' . $heureFinMission . '    Heures ', 0, 1);

        $pdf->Rect($pdf->GetX(), $pdf->GetY(), $w50 * 2, 10);  // Bordure englobant tout
        $pdf->setTextColor(...$couleurTitre);
        $pdf->Cell(37, 10, 'Lieu d intervention : ', 0, 0);
        $pdf->setTextColor(0, 0, 0); // Noir
        $pdf->Cell((2 * $w50) - 37, 10, $secondFormDto->lieuIntervention, 0, 1);

        $pdf->setTextColor(...$couleurTitre);
        $pdf->Cell(14, 10, 'Client : ', 0, 0);
        $pdf->setTextColor(0, 0, 0); // Noir
        $pdf->Cell($w50 - 14, 10, $secondFormDto->client, 0, 0);
        $pdf->Rect($pdf->GetX() - $w50, $pdf->GetY(), $w50, 10);  // Bordure englobant tout

        $pdf->setTextColor(...$couleurTitre);
        $pdf->Cell(18, 10, 'N° fiche : ', 0, 0);
        $pdf->setTextColor(0, 0, 0); // Noir
        $pdf->Cell($w50 - 18, 10, $secondFormDto->fiche, 0, 0);
        $pdf->Rect($pdf->GetX() - $w50, $pdf->GetY(), $w50, 10);  // Bordure englobant tout

        $pdf->Ln(); // Nouvelle ligne

        $pdf->setTextColor(...$couleurTitre);
        $pdf->Cell(32, 10, 'Véhicule société : ', 0, 0);
        $pdf->setTextColor(0, 0, 0); // Noir
        $pdf->Cell($w50 - 32, 10, $secondFormDto->vehiculeSociete, 0, 0);
        $pdf->Rect($pdf->GetX() - $w50, $pdf->GetY(), $w50, 10);  // Bordure englobant tout

        $pdf->setTextColor(...$couleurTitre);
        $pdf->Cell(29, 10, 'N° de véhicule : ', 0, 0);
        $pdf->setTextColor(0, 0, 0); // Noir
        $pdf->Cell($w50 - 29, 10, $secondFormDto->numVehicule, 0, 0);
        $pdf->Rect($pdf->GetX() - $w50, $pdf->GetY(), $w50, 10);  // Bordure englobant tout

        $pdf->Ln(); // Nouvelle ligne

        $pdf->setTextColor(...$couleurTitre);
        $pdf->Cell(48, 10, 'Indemnité Forfaitaire (+) : ', 0, 0);
        $pdf->setTextColor(0, 0, 0); // Noir
        $pdf->Cell($w50 - 48, 10, $secondFormDto->indemniteForfaitaire . ' ' . $secondFormDto->devis . ' / jour', 0, 0);
        $pdf->Rect($pdf->GetX() - $w50, $pdf->GetY(), $w50, 10);  // Bordure englobant tout

        $pdf->setTextColor(...$couleurTitre);
        $pdf->Cell(30, 10, 'Supplément (+) : ', 0, 0);
        $pdf->setTextColor(0, 0, 0); // Noir
        $pdf->Cell($w50 - 30, 10, $secondFormDto->supplementJournaliere . ' ' . $secondFormDto->devis . ' / jour', 0, 0);
        $pdf->Rect($pdf->GetX() - $w50, $pdf->GetY(), $w50, 10);  // Bordure englobant tout

        $pdf->Ln(); // Nouvelle ligne

        $pdf->setTextColor(...$couleurTitre);
        $pdf->Cell(48, 10, 'Indemnité de chantier (-) : ', 0, 0);
        $pdf->setTextColor(0, 0, 0); // Noir
        $pdf->Cell($w50 - 48, 10, $secondFormDto->idemnityDepl . ' ' . $secondFormDto->devis . ' / jour', 0, 0);
        $pdf->Rect($pdf->GetX() - $w50, $pdf->GetY(), $w50, 10);  // Bordure englobant tout

        $pdf->setTextColor(...$couleurTitre);
        $pdf->Cell(37, 10, 'Total indemnité (=) : ', 0, 0);
        $pdf->setTextColor(0, 0, 0); // Noir
        $pdf->Cell($w50 - 37, 10, $secondFormDto->totalIndemniteForfaitaire . ' ' . $secondFormDto->devis, 0, 0);
        $pdf->Rect($pdf->GetX() - $w50, $pdf->GetY(), $w50, 10);  // Bordure englobant tout

        $pdf->Ln(); // Nouvelle ligne

        $pdf->setY(165);

        $pdf->setTextColor(...$couleurTitre);
        $pdf->Cell(20, 10, 'Autres: ', 0, 1);

        $pdf->setXY(30, 165);
        $pdf->setTextColor(...$couleurTitre);
        $pdf->Cell(80, 10,  'MOTIF', 1, 0, 'C');
        $pdf->Cell(80, 10, '' . 'MONTANT', 1, 1, 'C');
        $pdf->setX(30);

        $titreMontantTotal = "MONTANT TOTAL A " . ($typeMission === 'TROP PERCU' ? 'RETIRER' : 'PAYER');

        $pdf->setTextColor(0, 0, 0); // Noir
        $pdf->Cell(80, 10,  $secondFormDto->motifAutresDepense1, 1, 0, 'L');
        $pdf->Cell(80, 10, '' . $secondFormDto->autresDepense1 . ' ' . $secondFormDto->devis, 1, 1, 'C');
        $pdf->setX(30);
        $pdf->Cell(80, 10,  $secondFormDto->motifAutresDepense2, 1, 0, 'L');
        $pdf->Cell(80, 10, '' . $secondFormDto->autresDepense2 . ' ' . $secondFormDto->devis, 1, 1, 'C');
        $pdf->setX(30);
        $pdf->Cell(80, 10,  $secondFormDto->motifAutresDepense3, 1, 0, 'L');
        $pdf->Cell(80, 10, '' . $secondFormDto->autresDepense3 . ' ' . $secondFormDto->devis, 1, 1, 'C');
        $pdf->setX(30);
        $pdf->Cell(80, 10,  'Total autre ', 1, 0, 'C');
        $pdf->Cell(80, 10,   $secondFormDto->totalAutresDepenses . ' ' . $secondFormDto->devis, 1, 1, 'C');
        $pdf->setX(30);
        $pdf->setTextColor(...$couleurTitre);
        $pdf->Cell(80, 10, $titreMontantTotal, 1, 0, 'C');
        $pdf->setTextColor(0, 0, 0); // Noir
        $pdf->Cell(80, 10, $secondFormDto->totalGeneralPayer . ' ' . $secondFormDto->devis, 1, 1, 'C');

        /** Ligne de NB sur les montants */
        $NB_1 = $secondFormDto->idemnityDepl === '0' ? '' : 'Montant total à payer = ' . $secondFormDto->totalAutresDepenses . ' - (' . $secondFormDto->idemnityDepl . '*' . $secondFormDto->nombreJour . ') = ' . $secondFormDto->totalGeneralPayer;
        $NB_2 = $secondFormDto->idemnityDepl === '0' ? '' : $secondFormDto->idemnityDepl . " étant l'indemnité journalière reçue mensuellement du fait que l'agent se trouve sur un site";
        $pdf->setY(227);
        $pdf->SetFont('helvetica', 'B', 10);
        $pdf->Cell(0, 0, $NB_1, 0, 1);
        $pdf->Cell(0, 0, $NB_2, 0, 1);

        /** Mode de paiement */
        $pdf->SetFont($font, '', 12);
        $pdf->setTextColor(...$couleurTitre);
        $pdf->Cell(35, 10, 'Mode de paiement: ', 0, 0);
        $pdf->setTextColor(0, 0, 0); // Noir
        $pdf->Cell($w50 - 35, 10, $secondFormDto->modePayement, 0, 0);
        $pdf->setX($w50 + 20);
        $pdf->Cell($w50, 10, $secondFormDto->mode, 0, 1);

        /** Génération de fichier */

        $pdf->Output($filePathName, 'F');
    }

    private function getHalfWidth(TCPDF $pdf)
    {
        $w_total = $pdf->GetPageWidth();  // Largeur totale du PDF
        $margins = $pdf->GetMargins();    // Tableau des marges (left, top, right)

        $usable_width = $w_total - $margins['left'] - $margins['right'];
        return $usable_width / 2;
    }
}
