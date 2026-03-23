<?php

namespace App\Service\Hf\Materiel\Badm;

use Psr\Log\LoggerInterface;
use App\Dto\Hf\Materiel\Badm\FirstFormDto;
use App\Dto\Hf\Materiel\Badm\SecondFormDto;
use App\Repository\Hf\Materiel\Badm\BadmRepository;
use App\Constants\Hf\Materiel\Badm\StatutBadmConstants;
use App\Constants\Hf\Materiel\Badm\TypeMouvementConstants;
use App\Service\Historique_operation\HistoriqueOperationService;

class BadmBlockingConditionService
{
    private LoggerInterface $logger;
    private HistoriqueOperationService $historiqueOperationService;
    private BadmRepository $badmRepository;

    public function __construct(
        LoggerInterface $logger,
        HistoriqueOperationService $historiqueOperationService,
        BadmRepository $badmRepository
    ) {
        $this->logger = $logger;
        $this->historiqueOperationService = $historiqueOperationService;
        $this->badmRepository = $badmRepository;
    }

    /**
     * Vérifie les conditions de blocage pour la création d'un BADM.
     * Retourne un message d'erreur si une condition bloquante est rencontrée, sinon null.
     *
     * @param FirstFormDto $firstFormDto Le DTO du premier formulaireformulaire 
     * @return string|null Le message d'erreur ou null si tout est OK
     */
    public function checkBlockingConditionsAvantSoumissionForm(FirstFormDto $firstFormDto, array $infoMaterielDansIps): ?string
    {

        // bloqué si:
        // 2.1 le matériel n'existe pas
        if (empty($infoMaterielDansIps)) {
            $message = 'Le matériel peut être déjà vendu ou vous avez mal saisi le numéro de série ou le numéro de parc ou ID matériel.';
            $this->handleBlockingCondition($message);
            return $message;
        }

        // Récupération des infos matériel depuis l'Intranet
        $infoMaterielDansIntranet = $this->badmRepository->findOneBy(['idMateriel' => $infoMaterielDansIps['idMateriel']], ['numeroBadm', 'DESC']);

        // 2.2 le statut du badm dans la base de donnée est encore sur l'un des statut encours de traitement pour l'une des type de mouvement
        if (
            $infoMaterielDansIntranet
            && $firstFormDto->typeMouvement->getDescription() === $infoMaterielDansIntranet->getTypeMouvement()->getDescription()
            && in_array($infoMaterielDansIntranet->getStatutDemande()->getDescription(), StatutBadmConstants::getStatutsEncoursDeTraitement())
        ) {
            $message = 'ce matériel est encours de traitement pour ce type de mouvement.';
            $this->handleBlockingCondition($message);
            return $message;
        }

        // 2.3 le type de mouvement est ENTREE EN PARC et le code affect n'est pas 'VTE'
        if (
            $infoMaterielDansIntranet
            && $firstFormDto->typeMouvement->getDescription() === TypeMouvementConstants::TYPE_MOUVEMENT_ENTREE_EN_PARC
            && $infoMaterielDansIps['code_affect'] !== 'VTE'
        ) {
            $message = 'Ce matériel est déjà en PARC.';
            $this->handleBlockingCondition($message);
            return $message;
        }

        // 2.4 le type de mouvement est CHANGEMENT AGENCE/SERVICE et le code affect est 'VTE'
        if (
            $infoMaterielDansIntranet
            && $firstFormDto->typeMouvement->getDescription() === TypeMouvementConstants::TYPE_MOUVEMENT_CHANGEMENT_AGENCE_SERVICE
            && $infoMaterielDansIps['code_affect'] === 'VTE'
        ) {
            $message = 'L\'agence et le service associés à ce matériel ne peuvent pas être modifiés.';
            $this->handleBlockingCondition($message);
            return $message;
        }

        // 2.5 le type de mouvement est CHANGEMENT AGENCE/SERVICE et le code affect n'est ni 'LCD' ni 'IMM'
        if (
            $infoMaterielDansIntranet
            && $firstFormDto->typeMouvement->getDescription() === TypeMouvementConstants::TYPE_MOUVEMENT_CHANGEMENT_AGENCE_SERVICE
            && !in_array($infoMaterielDansIps['code_affect'], ['LCD', 'IMM'])
        ) {
            $message = 'L\'affectation du matériel ne permet pas cette opération.';
            $this->handleBlockingCondition($message);
            return $message;
        }

        // 2.6 le type de mouvement est CESSION D'ACTIF et le code affect n'est ni 'LCD' ni 'IMM'
        if (
            $infoMaterielDansIntranet
            && $firstFormDto->typeMouvement->getDescription() === TypeMouvementConstants::TYPE_MOUVEMENT_CESSION_DACTIF
            && !in_array($infoMaterielDansIps['code_affect'], ['LCD', 'IMM'])
        ) {
            $message = 'Ce matériel ne peut pas mise en cession d\'actif.';
            $this->handleBlockingCondition($message);
            return $message;
        }

        // 2.7 le type de mouvement est MISE AU REBUT et le code affect est 'CAS'
        if (
            $infoMaterielDansIntranet
            && $firstFormDto->typeMouvement->getDescription() === TypeMouvementConstants::TYPE_MOUVEMENT_MISE_AU_REBUT
            && $infoMaterielDansIps['code_affect'] === 'CAS'
        ) {
            $message = 'Ce matériel ne peut pas être mis au rebut.';
            $this->handleBlockingCondition($message);
            return $message;
        }

        // TODO : 2.8 l'utilisateur n'a pas le permission sur l'agence et service du materiel
        // if (in_array($agenceMaterielId, $user->getAgenceAutoriserIds()) && in_array($serviceMaterilId, $user->getServiceAutoriserIds())) {
        //     $message = 'vous n\'êtes pas autoriser à consulter ce matériel.';
        //     $this->handleBlockingCondition($message);
        //     return $message;
        // }


        return null;
    }

    public function checkBlockingConditionsApresSoumissionForm(SecondFormDto $dto): ?string
    {

        // 1. le choix du type devrait être changement de casier
        if (
            ($dto->typeMouvement->getDescription() === TypeMouvementConstants::TYPE_MOUVEMENT_CHANGEMENT_AGENCE_SERVICE
                && $dto->emetteur['agence'] === $dto->destinataire['agence']
                && $dto->emetteur['service'] === $dto->destinataire['service']
            )
            ||
            ($dto->destinataire['agence'] === null
                && $dto->destinataire['service'] === null)
        ) {
            $message = 'le choix du type devrait être Changement de Casier.';
            $this->handleBlockingCondition($message);
            return $message;
        }
        return null;
    }

    private function handleBlockingCondition(string $message): void
    {
        $this->logger->warning($message);
        $this->historiqueOperationService->enregistrer(
            '',
            'CREATION',
            'BADM',
            false,
            $message
        );
    }
}
