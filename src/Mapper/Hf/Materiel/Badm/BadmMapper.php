<?php

namespace App\Mapper\Hf\Materiel\Badm;

use App\Entity\Hf\Materiel\Badm\Badm;
use App\Dto\Hf\Materiel\Badm\SecondFormDto;
use Doctrine\ORM\EntityManagerInterface;
use App\Entity\Admin\AgenceService\Agence;
use App\Entity\Admin\AgenceService\Service;
use App\Entity\Hf\Materiel\Casier\Casier;
use App\Entity\Hf\Materiel\Badm\TypeMouvement;
use App\Entity\Admin\Statut\StatutDemande;

class BadmMapper
{
    private EntityManagerInterface $em;

    public function __construct(EntityManagerInterface $em)
    {
        $this->em = $em;
    }

    public function map(Badm $badm, SecondFormDto $secondFormDto): Badm
    {
        $badm
            // --------------- Caracteristique du matériel ---------------
            ->setIdMateriel($secondFormDto->idMateriel)
            ->setNumParc($secondFormDto->numParc)
            // --------------- Etat machine -----------------
            ->setHeureMachine($secondFormDto->heureMachine)
            ->setKmMachine($secondFormDto->kmMachine)
            // ---------------- Agence, service et casier emetteur ----------------
            ->setAgenceEmetteurId($this->reFetchEntity(Agence::class, $secondFormDto->emetteur['agence']))
            ->setServiceEmetteurId($this->reFetchEntity(Service::class, $secondFormDto->emetteur['service']))
            ->setCasierEmetteur($this->reFetchEntity(Casier::class, $secondFormDto->emetteur['casier']))
            // ---------------- Agence, service et casier destinataire ----------------
            ->setAgenceDebiteurId($this->reFetchEntity(Agence::class, $secondFormDto->destinataire['agence']))
            ->setServiceDebiteur($this->reFetchEntity(Service::class, $secondFormDto->destinataire['service']))
            ->setCasierDestinataire($this->reFetchEntity(Casier::class, $secondFormDto->destinataire['casier']))
            ->setMotifMateriel($secondFormDto->motifMateriel)
            // ---------------- Entrée en parc ----------------
            ->setEtatAchat($secondFormDto->etatAchat)
            ->setDateMiseLocation($secondFormDto->dateMiseLocation)
            // ---------------- Valeur ----------------
            ->setCoutAcquisition($secondFormDto->coutAcquisition)
            ->setAmortissement($secondFormDto->amortissement)
            ->setValeurNetComptable($secondFormDto->valeurNetComptable)
            // ---------------- cession d'actif ----------------
            ->setNomClient($secondFormDto->nomClient)
            ->setModalitePaiement($secondFormDto->modalitePaiement)
            ->setPrixVenteHt($secondFormDto->prixVenteHt)
            // ---------------- Mise au rebut -----------------
            ->setMotifMiseRebut($secondFormDto->motifMiseRebut)
            // ->setNomImage($secondFormDto->pieceJoint01)
            // ->setNomFichier($secondFormDto->pieceJoint02)
            // --------------- mouvement materiel ---------------
            ->setTypeMouvement($this->reFetchEntity(TypeMouvement::class, $secondFormDto->typeMouvement))
            ->setNumeroBadm($secondFormDto->numeroBadm)
            ->setStatutDemande($this->reFetchEntity(StatutDemande::class, $secondFormDto->statutDemande))

        ;
        return $badm;
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
