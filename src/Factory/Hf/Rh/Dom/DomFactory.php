<?php

namespace App\Factory\Hf\Rh\Dom;

use App\Dto\Hf\Rh\Dom\SecondFormDto;
use App\Entity\Hf\Rh\Dom\Dom;
use App\Repository\Admin\AgenceService\AgenceRepository;
use App\Repository\Admin\AgenceService\ServiceRepository;
use App\Repository\Admin\Statut\StatutDemandeRepository;
use App\Service\Utils\ExtractorStringService;
use App\Service\Utils\NumeroGeneratorService;
use Symfony\Component\Security\Core\Security;
use Symfony\Component\Security\Core\User\UserInterface;

class DomFactory
{

    private NumeroGeneratorService $numeroGeneratorService;
    private StatutDemandeRepository $statutDemandeRepository;
    private Security $security;

    public function __construct(
        NumeroGeneratorService $numeroGeneratorService,
        StatutDemandeRepository $statutDemandeRepository,
        Security $security
    ) {
        $this->numeroGeneratorService = $numeroGeneratorService;
        $this->statutDemandeRepository = $statutDemandeRepository;
        $this->security = $security;
    }

    public function create(SecondFormDto $secondFormDto): Dom
    {
        $dom = new Dom();
        /** @var User $user */
        $user = $this->security->getUser();
        $statut = $this->statutDemandeRepository->findOneBy(['codeApplication' => Dom::CODE_APPLICATION, 'description' => 'OUVERT']);
        $numTel = $secondFormDto->modePayement === "MOBILE MONEY" ? $secondFormDto->mode : null;
        $matricule = $secondFormDto->salarier === "TEMPORAIRE" ? $secondFormDto->cin : $secondFormDto->matricule;
        $agenceEmetteur = $user->getAgenceUser();
        $serviceEmetteur = $user->getServiceUser();
        $codeAgenceEmetteur = $agenceEmetteur->getCode();
        $libelleAgenceEmetteur = $agenceEmetteur->getNom();
        $codeSeviceEmetteur = $serviceEmetteur->getCode();
        $libelleServiceEmetteur = $serviceEmetteur->getNom();


        $dom->setDateDemande($secondFormDto->dateDemande);
        $dom->setNumeroOrdreMission($this->numeroGeneratorService->autoGenerateNumero(Dom::CODE_APPLICATION, true));
        $dom->setMatricule($matricule);
        $dom->setNomSessionUtilisateur($user->getUserIdentifier());
        //Date debut et fin mission / et nombre de jour
        $dom->setDateDebut($secondFormDto->dateHeureMission['debut']);
        $dom->setHeureDebut($secondFormDto->dateHeureMission['heureDebut']->format('H:i'));
        $dom->setDateFin($secondFormDto->dateHeureMission['fin']);
        $dom->setHeureFin($secondFormDto->dateHeureMission['heureFin']->format('H:i'));
        $dom->setNombreJour($secondFormDto->nombreJour);

        //motif deplacmene , client, lieu d'intervention, vehicule societe
        $dom->setMotifDeplacement($secondFormDto->motifDeplacement);
        $dom->setClient($secondFormDto->client);
        $dom->setLieuIntervention($secondFormDto->lieuIntervention);
        $dom->setVehiculeSociete($secondFormDto->vehiculeSociete);
        // indemnite forfaitaire
        $dom->setIndemniteForfaitaire($secondFormDto->indemniteForfaitaire);
        $dom->setTotalIndemniteForfaitaire($secondFormDto->totalIndemniteForfaitaire);
        // Autres depenses
        $dom->setMotifAutreDepense1($secondFormDto->motifAutresDepense1);
        $dom->setAutresDepense1($secondFormDto->autresDepense1);
        $dom->setMotifAutresDepense2($secondFormDto->motifAutresDepense2);
        $dom->setAutresDepense2($secondFormDto->autresDepense2);
        $dom->setMotifAutresDepense3($secondFormDto->motifAutresDepense3);
        $dom->setAutresDepense3($secondFormDto->autresDepense3);
        $dom->setTotalAutresDepenses($secondFormDto->totalAutresDepenses);
        // total generale et mode de paiement et devis
        $dom->setTotalGeneralPayer($secondFormDto->totalGeneralPayer);
        $dom->setModePayement($secondFormDto->modePayement . ':' . $numTel);
        $dom->setDevis($secondFormDto->devis);
        //pieces joint
        $dom->setPieceJoint01($secondFormDto->pieceJoint01);
        $dom->setPieceJoint02($secondFormDto->pieceJoint02);
        // code statut , num tel, nom, prenom
        $dom->setCodeStatut($statut->getCodeStatut());
        $dom->setNumeroTel($numTel);
        $dom->setNom($secondFormDto->nom);
        $dom->setPrenom($secondFormDto->prenom);
        // agence et service
        $dom->setLibelleCodeAgenceService($libelleAgenceEmetteur . '-' . $libelleServiceEmetteur);
        $dom->setAgenceEmetteurId($agenceEmetteur);
        $dom->setServiceEmetteurId($serviceEmetteur);
        $dom->setAgenceDebiteurId($secondFormDto->debiteur['agence']);
        $dom->setServiceDebiteur($secondFormDto->debiteur['service']);
        //fichet et num vehicule
        $dom->setFiche($secondFormDto->fiche);
        $dom->setNumVehicule($secondFormDto->numVehicule);
        $dom->setDroitIndemnite($secondFormDto->supplementJournaliere);
        //
        $dom->setIdemnityDepl($secondFormDto->idemnityDepl);

        $dom->setPieceJustificatif($secondFormDto->pieceJustificatif);
        $dom->setIdStatutDemande($statut);

        // type mission, categorie et site
        $dom->setSousTypeDocument($secondFormDto->typeMission);
        $dom->setCategoryId($secondFormDto->categorie);
        $dom->setCategorie($secondFormDto->categorie->getDescription());
        $dom->setSiteId($secondFormDto->site);
        $dom->setSite($secondFormDto->site->getNomZone());

        return $dom;
    }
}
