<?php

namespace App\Service\Hf\Rh\Dom;

use App\Entity\Hf\Rh\Dom\Dom;
use App\Entity\Hf\Rh\Dom\SousTypeDocument;
use App\Repository\Hf\Rh\Dom\DomRepository;

/**
 * Service dédié à la validation des règles métier pour les ordres de mission (DOM)
 */
class DomValidator
{
    private DomRepository $domRepository;

    public function __construct(DomRepository $domRepository)
    {
        $this->domRepository = $domRepository;
    }

    /**
     * Valide un objet Dom selon les règles métier
     * 
     * @throws \LogicException Si une règle n'est pas respectée
     */
    public function validate(Dom $dom): void
    {
        $typeMission = $dom->getSousTypeDocument()->getCodeSousType();
        $isComplementOrTropPercu = in_array($typeMission, [SousTypeDocument::CODE_COMPLEMENT, SousTypeDocument::CODE_TROP_PERCU]);

        // Vérification du chevauchement de dates
        if (!$isComplementOrTropPercu && $this->hasExistingMissionOnDates($dom->getMatricule(), $dom->getDateDebut(), $dom->getDateFin())) {
            throw new \LogicException(sprintf(
                '%s %s %s a déjà une mission enregistrée sur ces dates, veuillez vérifier.',
                $dom->getMatricule(),
                $dom->getNom(),
                $dom->getPrenom()
            ));
        }

        // Vérification du montant maximum pour les frais non exceptionnels
        $isFraisExceptionnel = $typeMission === SousTypeDocument::CODE_FRAIS_EXCEPTIONNEL;
        $totalGeneral = (int) str_replace('.', '', $dom->getTotalGeneralPayer());
        $isAmountValid = $totalGeneral <= 500000;

        if (!$isFraisExceptionnel && !$isAmountValid) {
            throw new \LogicException("Assurez-vous que le Montant Total est inférieur à 500.000 Ar.");
        }
    }

    /**
     * Vérifie si une mission existe déjà pour le matricule aux dates données
     */
    private function hasExistingMissionOnDates(string $matricule, \DateTimeInterface $dateDebutInput, \DateTimeInterface $dateFinInput): bool
    {
        $existingMissionsDates = $this->domRepository->getInfoDOMMatrSelet($matricule);

        if (empty($existingMissionsDates)) {
            return false;
        }

        foreach ($existingMissionsDates as $missionDates) {
            $dateDebut = new \DateTime($missionDates['Date_Debut']);
            $dateFin = new \DateTime($missionDates['Date_Fin']);

            // if (($dateDebutInput <= $dateFin) && ($dateDebut <= $dateFinInput))
            if (($dateDebutInput <= $dateFin) && ($dateDebut <= $dateFinInput)) {
                return true;
            }
        }

        return false;
    }
}
