<?php

namespace App\Service\Hf\Atelier\Dit\Soumission\Ors;

use App\Constants\Hf\Atelier\Dit\Soumission\Ors\StatutOrConstant;
use App\Model\Hf\Atelier\Dit\Soumission\Ors\OrsModel;
use App\Repository\Hf\Atelier\Dit\DitRepository;


class OrsBlockingConditionService
{
    private OrsModel $orsModel;
    private DitRepository $ditRepository;

    public function __construct(
        OrsModel $orsModel,
        DitRepository $ditRepository
    ) {
        $this->orsModel = $orsModel;
        $this->ditRepository = $ditRepository;
    }

    public function checkBlockingConditionsDepuisListe(string $numeroDit, $numeroOr): ?string
    {
        // 1. Bloquer si le numéro OR est manquant sur la ligne de la DIT
        if (empty($numeroOr)) {
            return "Le numéro OR est manquant pour cette DIT. Impossible de soumettre l'OR.";
        }

        $infoOrsIps = $this->orsModel->getInfoOrs($numeroDit, (int) $numeroOr);

        // 2. Bloquer si le numéro OR et le numéro DIT ne correspond pas
        if (empty($infoOrsIps)) {
            return "L'OR n'existe pas ou différent pour cette DIT dans IPS. Impossible de soumettre l'OR.";
        }

        // 3. Bloquer si il existe une ou plusieurs interventions non planifiées dans l'OR (la date planning de l'OR n'existe pas)
        $estDatePlanningExiste = array_reduce($infoOrsIps, function ($carry, $objet) {
            return $carry && ($objet['date_planning_existe'] == 1);
        }, true);
        if (!$estDatePlanningExiste) {
            return "Impossible de soumettre l'OR car une ou plusieurs interventions non planifiées dans l'OR";
        }

        // 4. Bloquer si la position de l'OR est parmis 'FC', 'FE', 'CP', 'ST'
        if (in_array($infoOrsIps[0]['position'], ['FC', 'FE', 'CP', 'ST'])) {
            return "Impossible de soumettre l'OR car la position de l'OR est parmis 'FC', 'FE', 'CP', 'ST'";
        }

        // 5. Bloquer si la référence client est vide
        if (empty($infoOrsIps[0]['reference_client'])) {
            return "Impossible de soumettre l'OR car la référence client est vide.";
        }

        // 6. Bloquer si le numéro Client de l'OR n'existe pas dans IPS
        if ($infoOrsIps[0]['numero_client_existe'] === 0) {
            return "Impossible de soumettre l'OR car le client rattaché à l'OR est introuvable";
        }

        //  Récupération de l'information de la DIT dans l'intranet 
        $infoDitIntranet = $this->ditRepository->findOneBy(['numeroDit' => $numeroDit, 'numeroOr' => $numeroOr]);

        // 7. Bloquer si le ID materiel de l'OR ne correspond pas au ID materiel de la DIT
        if ($infoDitIntranet->getIdMateriel() !== (int)$infoOrsIps[0]['id_materiel']) {
            return "Imposible de soumettre l'OR car le materiel de l'OR ne correspond pas au materiel de la DIT";
        }

        // 8. Bloquer si un OR est déjà en cours de validation (statut: Soumis à validation ou le statut contient Validé ou Réfusé ...)
        if (
            str_contains($infoDitIntranet->getStatutOr(), StatutOrConstant::SOUMIS_A_VALIDATION) ||
            str_contains($infoDitIntranet->getStatutOr(), StatutOrConstant::VALIDE) ||
            str_contains($infoDitIntranet->getStatutOr(), StatutOrConstant::REFUSE) ||
            str_contains($infoDitIntranet->getStatutOr(), StatutOrConstant::MODIFICATION_DEMANDE_PAR_CLIENT) ||
            str_contains($infoDitIntranet->getStatutOr(), StatutOrConstant::MODIFICATION_DEMANDE_PAR_CA) ||
            str_contains($infoDitIntranet->getStatutOr(), StatutOrConstant::MODIFICATION_DEMANDE_PAR_DT)
        ) {
            return "Impossible de soumettre l'OR car il est déjà en cours de validation";
        }

        // 9. Bloquer si l'agence et service debiteur de l'OR dans IPS (informix) ne correspond pas à l'agence et service debiteur du DIT dans intranet (sqlserveur)  
        if (
            $infoOrsIps[0]['code_agence_debiteur'] !== $infoDitIntranet->getAgenceDebiteurId()->getCode() ||
            $infoOrsIps[0]['code_service_debiteur'] !== $infoDitIntranet->getServiceDebiteur()->getCode()
        ) {
            return "Impossible de soumettre l'OR car l'agence et service debiteur de l'OR ne correspond pas à l'agence et service debiteur du DIT";
        }

        // 10. Bloquer si un OR a plusieurs service débiteur (si les agence et service debiteur sont différents)
        $agenceServiceDebiteurEgaux = array_reduce($infoOrsIps, function ($carry, $objet) use ($infoDitIntranet) {
            return $carry && ($objet['code_agence_debiteur'] == $infoDitIntranet->getAgenceDebiteurId()->getCode() && $objet['code_service_debiteur'] == $infoDitIntranet->getServiceDebiteur()->getCode());
        }, true);
        if (!$agenceServiceDebiteurEgaux) {
            return "Impossible de soumettre l'OR car il a plusieurs service débiteur";
        }

        // 11. Bloquer si le première soumission de l'OR(statut_or vide) et le date planning est inférieur à la date du jour de soumission
        if (empty($infoDitIntranet->getStatutOr()) && $infoOrsIps[0]['date_planning'] < date('Y-m-d')) {
            return "Impossible de soumettre l'OR car le date planning est inférieur à la date du jour";
        }

        return null;
    }
}
