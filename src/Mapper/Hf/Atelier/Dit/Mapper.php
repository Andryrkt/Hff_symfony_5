<?php

namespace App\Mapper\Hf\Atelier\Dit;

use App\Entity\Hf\Atelier\Dit\Dit;
use App\Dto\Hf\Atelier\Dit\FormDto;
use Doctrine\ORM\EntityManagerInterface;
use App\Entity\Admin\AgenceService\Agence;
use App\Entity\Admin\AgenceService\Service;


use App\Factory\Hf\Atelier\Dit\ButtonsFactory;


class Mapper
{
    private EntityManagerInterface $em;
    private ButtonsFactory $buttonsFactory;

    public function __construct(
        EntityManagerInterface $em,
        ButtonsFactory $buttonsFactory
    ) {
        $this->em = $em;
        $this->buttonsFactory = $buttonsFactory;
    }

    public function map(FormDto $dto): Dit
    {
        $dit = new Dit();
        $dit
            // -----------------Reparation ----------------
            ->setTypeReparation($dto->typeReparation)
            ->setReparationRealise($dto->reparationRealise)
            // --------------- info sur le DIT ---------------
            ->setNumeroDit($dto->numeroDit)
            ->setInterneExterne($dto->interneExterne)
            ->setObjectDemande($dto->objetDemande)
            ->setDetailDemande($dto->detailDemande)
            ->setLivraisonPartiel($dto->livraisonPartiel)
            ->setAvisRecouvrement($dto->avisRecouvrement)
            ->setWorTypeDocument($dto->typeDocument)
            ->setCategorieAteApp($dto->categorieDemande)
            ->setStatutDemande($dto->statutDemande)
            // ----------------Intervention ---------------
            ->setWorNiveauUrgence($dto->niveauUrgence)
            ->setDatePrevueTravaux($dto->datePrevueTravaux)
            // --------------- info client --------------
            ->setNomClient($dto->nomClient)
            ->setNumeroTelClient($dto->numeroTel)
            ->setMailClient($dto->mailClient)
            ->setClientSousContrat($dto->clientSousContrat)
            ->setNumeroClient($dto->numeroClient)
            ->setLibelleClient($dto->libelleClient)
            // ------ info matériel -----
            ->setIdMateriel($dto->idMateriel)
            ->setHeureMachine($dto->heureMachine)
            ->setKmMachine($dto->kmMachine)
            //  --------------- OR ---------------------
            ->setDateOr($dto->dateOr)
            ->setNumeroOr($dto->numeroOr)
            ->setStatutOr($dto->statutOr)
            ->setDateValidationOr($dto->dateValidationOr)
            // ----------------- Devis ----------------------
            ->setDemandeDevis($dto->demandeDevis)
            ->setNumeroDevisRattacher($dto->numeroDevisRattacher)
            ->setStatutDevis($dto->statutDevis)
            // ---------------- Facture -------------------
            ->setEtatFacturation($dto->etatFacturation)
            // ---------------- RI -------------------
            ->setRi($dto->ri)
            // ------------ Annulation ---------------
            ->setEstAnnuler($dto->estAnnuler)
            ->setDateAnnulation($dto->dateAnnulation)
            //-----------------Section -----------------
            ->setSectionAffectee($dto->sectionAffectee)
            // --------------- Dit Avoir ----------------
            ->setNumeroDemandeDitAvoir($dto->numeroDemandeDitAvoir)
            ->setEstDitAvoir($dto->estDitAvoir)
            // ----------------- Dit Refacturation ----------------------
            ->setNumeroDemandeDitRefacturation($dto->numeroDemandeDitRefacturation)
            ->setEstDitRefacturation($dto->estDitRefacturation)
            // ------------------ Autre --------------
            ->setNumeroMigration($dto->numeroMigration)
            ->setEstAtePolTana($dto->estAtePolTana)

            // ---------------- Agence, service emetteur ----------------
            ->setAgenceEmetteurId($this->reFetchEntity(Agence::class, $dto->emetteur['agence']))
            ->setServiceEmetteurId($this->reFetchEntity(Service::class, $dto->emetteur['service']))
            // ---------------- Agence, service destinataire ----------------
            ->setAgenceDebiteurId($this->reFetchEntity(Agence::class, $dto->debiteur['agence']))
            ->setServiceDebiteur($this->reFetchEntity(Service::class, $dto->debiteur['service']))
        ;

        return $dit;
    }

    public function reverseMap(Dit $dit): FormDto
    {
        $dto = new FormDto();
        // -----------------Reparation ----------------
        $dto->typeReparation = $dit->getTypeReparation();
        $dto->reparationRealise = $dit->getReparationRealise();
        // --------------- info sur le DIT ---------------
        $dto->numeroDit = $dit->getNumeroDit();
        $dto->interneExterne = $dit->getInterneExterne();
        $dto->objetDemande = $dit->getObjectDemande();
        $dto->detailDemande = $dit->getDetailDemande();
        $dto->livraisonPartiel = $dit->getLivraisonPartiel();
        $dto->avisRecouvrement = $dit->getAvisRecouvrement();
        $dto->typeDocument = $dit->getWorTypeDocument();
        $dto->categorieDemande = $dit->getCategorieAteApp();
        $dto->statutDemande = $dit->getStatutDemande();
        // ----------------Intervention ---------------
        $dto->niveauUrgence = $dit->getWorNiveauUrgence();
        $dto->datePrevueTravaux = $dit->getDatePrevueTravaux();
        // --------------- info client --------------
        $dto->nomClient = $dit->getNomClient();
        $dto->numeroTel = $dit->getNumeroTelClient();
        $dto->mailClient = $dit->getMailClient();
        $dto->clientSousContrat = $dit->getClientSousContrat();
        $dto->numeroClient = $dit->getNumeroClient();
        $dto->libelleClient = $dit->getLibelleClient();
        // ------ info matériel -----
        $dto->idMateriel = $dit->getIdMateriel();
        $dto->heureMachine = $dit->getHeureMachine();
        $dto->kmMachine = $dit->getKmMachine();
        //  --------------- OR ---------------------
        $dto->dateOr = $dit->getDateOr();
        $dto->numeroOr = $dit->getNumeroOr();
        $dto->statutOr = $dit->getStatutOr();
        $dto->dateValidationOr = $dit->getDateValidationOr();
        // ----------------- Devis ----------------------
        $dto->demandeDevis = $dit->getDemandeDevis();
        $dto->numeroDevisRattacher = $dit->getNumeroDevisRattacher();
        $dto->statutDevis = $dit->getStatutDevis();
        // ---------------- Facture -------------------
        $dto->etatFacturation = $dit->getEtatFacturation();
        // ---------------- RI -------------------
        $dto->ri = $dit->getRi();
        // ------------ Annulation ---------------
        $dto->estAnnuler = $dit->isEstAnnuler();
        $dto->dateAnnulation = $dit->getDateAnnulation();
        //-----------------Section -----------------
        $dto->sectionAffectee = $dit->getSectionAffectee();
        // --------------- Dit Avoir ----------------
        $dto->numeroDemandeDitAvoir = $dit->getNumeroDemandeDitAvoir();
        $dto->estDitAvoir = $dit->isEstDitAvoir();
        // ----------------- Dit Refacturation ----------------------
        $dto->numeroDemandeDitRefacturation = $dit->getNumeroDemandeDitRefacturation();
        $dto->estDitRefacturation = $dit->isEstDitRefacturation();

        // ------------------ Autre --------------
        $dto->numeroMigration = $dit->getNumeroMigration();
        $dto->estAtePolTana = $dit->isEstAtePolTana();

        // ---------------- Agence, service emetteur ----------------
        $dto->emetteur = [
            'agence' => $dit->getAgenceEmetteurId(),
            'service' => $dit->getServiceEmetteurId()
        ];
        // ---------------- Agence, service destinataire ----------------
        $dto->debiteur = [
            'agence' => $dit->getAgenceDebiteurId(),
            'service' => $dit->getServiceDebiteur(),
        ];

        // ---------------- Conditions et Boutons ----------------
        $dto->estOrASoumi = $dit->estOrASoumi();
        $dto->estAnnulable = $dit->estAnnulable();
        $dto->buttons = $this->buttonsFactory->generateEllipsisButtons($dto);

        return $dto;
    }

    /**
     * Récupère une entité fraîche pour s'assurer qu'elle est gérée par l'EM.
     */
    private function reFetchEntity(string $class, $entity)
    {
        if (null === $entity) {
            return null;
        }

        if (method_exists($entity, 'getId') && $entity->getId()) {
            return $this->em->find($class, $entity->getId());
        }

        return $entity;
    }
}
