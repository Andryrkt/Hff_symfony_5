<?php

namespace App\Mapper\Hf\Materiel\Badm;

use App\Entity\Hf\Materiel\Badm\Badm;
use Doctrine\ORM\EntityManagerInterface;
use App\Entity\Hf\Materiel\Casier\Casier;
use App\Model\Hf\Materiel\Badm\BadmModel;
use App\Entity\Admin\AgenceService\Agence;
use App\Entity\Admin\Statut\StatutDemande;
use App\Dto\Hf\Materiel\Badm\SecondFormDto;
use App\Entity\Admin\AgenceService\Service;
use App\Entity\Hf\Materiel\Badm\TypeMouvement;

class BadmMapper
{
    private EntityManagerInterface $em;
    private BadmModel $badmModel;

    public function __construct(
        EntityManagerInterface $em,
        BadmModel $badmModel
    ) {
        $this->em = $em;
        $this->badmModel = $badmModel;
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
     * Maps a Badm entity to a SecondFormDto.
     * This method is the inverse of the `map` method.
     *
     * @param Badm $badm The Badm entity to map.
     * @return SecondFormDto The mapped SecondFormDto.
     */
    public function reverseMap(Badm $badm): ?SecondFormDto
    {
        $infoMaterielDansIps = $this->badmModel->getInfoMateriel($badm);

        if ($infoMaterielDansIps) {

            $secondFormDto = new SecondFormDto();

            // --------------- Caracteristique du matériel ---------------
            // Ces champs ne sont pas directement stockés dans l'entité Badm
            // ou ne sont pas mappés par la méthode 'map' d'origine.
            // Ils devraient être récupérés d'une autre source si nécessaire pour le DTO.
            $secondFormDto->designation = $infoMaterielDansIps['designation'];
            $secondFormDto->idMateriel = $badm->getIdMateriel();
            $secondFormDto->numParc = $infoMaterielDansIps['num_parc'];
            $secondFormDto->numSerie = $infoMaterielDansIps['num_serie'];
            $secondFormDto->groupe = $infoMaterielDansIps['famille'];
            $secondFormDto->constructeur = $infoMaterielDansIps['constructeur'];
            $secondFormDto->modele = $infoMaterielDansIps['modele'];
            $secondFormDto->anneeDuModele = $infoMaterielDansIps['annee_du_modele'];
            $secondFormDto->affectation = $infoMaterielDansIps['affectation'];
            $secondFormDto->dateAchat = $infoMaterielDansIps['date_achat'];

            // --------------- Etat machine -----------------
            $secondFormDto->heureMachine = $badm->getHeureMachine();
            $secondFormDto->kmMachine = $badm->getKmMachine();

            // ---------------- Agence, service et casier emetteur ----------------
            // On récupère les entités liées si elles existent
            $secondFormDto->emetteur = [
                'agence' => $badm->getAgenceEmetteurId() ? $badm->getAgenceEmetteurId() : null,
                'service' => $badm->getServiceEmetteurId() ? $badm->getServiceEmetteurId() : null,
                'casier' => $badm->getCasierEmetteur() ? $badm->getCasierEmetteur() : null,
            ];

            // ---------------- Agence, service et casier destinataire ----------------
            $secondFormDto->destinataire = [
                'agence' => $badm->getAgenceDebiteurId() ? $badm->getAgenceDebiteurId() : null,
                'service' => $badm->getServiceDebiteur() ? $badm->getServiceDebiteur() : null,
                'casier' => $badm->getCasierDestinataire() ? $badm->getCasierDestinataire() : null,
            ];
            $secondFormDto->motifMateriel = $badm->getMotifMateriel();

            // ---------------- Entrée en parc ----------------
            $secondFormDto->etatAchat = $badm->getEtatAchat();
            $secondFormDto->dateMiseLocation = $badm->getDateMiseLocation();

            // ---------------- Valeur ----------------
            $secondFormDto->coutAcquisition = $badm->getCoutAcquisition();
            $secondFormDto->amortissement = $badm->getAmortissement();
            $secondFormDto->valeurNetComptable = $badm->getValeurNetComptable();

            // ---------------- cession d'actif ----------------
            $secondFormDto->nomClient = $badm->getNomClient();
            $secondFormDto->modalitePaiement = $badm->getModalitePaiement();
            $secondFormDto->prixVenteHt = $badm->getPrixVenteHt();

            // ---------------- Mise au rebut -----------------
            $secondFormDto->motifMiseRebut = $badm->getMotifMiseRebut();
            $secondFormDto->pieceJoint01 = $badm->getNomImage();
            $secondFormDto->pieceJoint02 = $badm->getNomFichier();

            // --------------- mouvement materiel ---------------
            $secondFormDto->typeMouvement = $badm->getTypeMouvement();
            $secondFormDto->dateDemande = $badm->getCreatedAt(); // Assuming dateDemande is createdAt
            $secondFormDto->numeroBadm = $badm->getNumeroBadm();
            $secondFormDto->statutDemande = $badm->getStatutDemande();

            // ---------------- OR -------------------------
            // Ces champs ne sont pas mappés par la méthode 'map' d'origine.
            // $secondFormDto->estOr = $badm->getEstOr();
            // $secondFormDto->ors = $badm->getOrs();

            return $secondFormDto;
        } else {
            return null;
        }
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
