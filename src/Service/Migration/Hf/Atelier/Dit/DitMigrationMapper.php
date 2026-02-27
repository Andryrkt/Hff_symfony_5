<?php

namespace App\Service\Migration\Hf\Atelier\Dit;

use App\Entity\Hf\Atelier\Dit\CategorieAteApp;
use App\Entity\Hf\Atelier\Dit\Dit;
use App\Entity\Hf\Atelier\Dit\WorNiveauUrgence;
use App\Entity\Hf\Atelier\Dit\WorTypeDocument;
use App\Service\Migration\Utils\DateTimeConverter;
use App\Service\Migration\Utils\EntityRelationMapper;
use Doctrine\DBAL\Connection;
use Doctrine\ORM\EntityManagerInterface;
use Psr\Log\LoggerInterface;

class DitMigrationMapper
{
    private EntityRelationMapper $relationMapper;
    private DateTimeConverter $dateTimeConverter;
    private EntityManagerInterface $em;
    private LoggerInterface $logger;
    private Connection $legacyConnection;

    public function __construct(
        EntityRelationMapper $relationMapper,
        DateTimeConverter $dateTimeConverter,
        EntityManagerInterface $em,
        LoggerInterface $logger,
        Connection $legacyConnection
    ) {
        $this->relationMapper = $relationMapper;
        $this->dateTimeConverter = $dateTimeConverter;
        $this->em = $em;
        $this->logger = $logger;
        $this->legacyConnection = $legacyConnection;
    }

    /**
     * Mappe un enregistrement de l'ancien schéma vers une nouvelle entité Badm
     *
     * @param array $oldData Données de l'ancien schéma
     * @return Dit|null Nouvelle entité dit ou null en cas d'erreur
     */
    public function mapOldToNew(array $oldData): ?Dit
    {
        try {
            $dit = new Dit();

            // Mapping des champs simples
            $this->mapSimpleFields($dit, $oldData);

            // Mapping des dates
            $this->mapDateFields($dit, $oldData);

            // Mapping des relations
            $this->mapRelations($dit, $oldData);

            return $dit;
        } catch (\Exception $e) {
            $this->logger->error('Erreur lors du mapping des données DIT', [
                'error' => $e->getMessage(),
                'old_id' => $oldData['ID_DIT'] ?? 'unknown', // ID supposé
            ]);
            return null;
        }
    }

    private function mapSimpleFields(Dit $dit, array $oldData): void
    {
        // Champs correspondants supposés
        $dit
            ->setNumeroDit($oldData['numero_demande_dit'] ?? '')
            ->setTypeReparation($oldData['type_reparation'] ?? '')
            ->setReparationRealise($oldData['reparation_realise'] ?? '')
            ->setInterneExterne($oldData['internet_externe'], '')
            ->setObjectDemande($oldData['objet_demande'] ?? '')
            ->setDetailDemande($oldData['detail_demande'] ?? '')
            ->setLivraisonPartiel($oldData['livraison_partiel'] ?? '')
            ->setAvisRecouvrement($oldData['avis_recouvrement'] ?? null)
            // information client
            ->setNomClient($oldData['nom_client'] ?? null)
            ->setNumeroTelClient($oldData['numero_telephone'] ?? null)
            ->setMailClient($oldData['mail_client'] ?? null)
            ->setClientSousContrat($oldData['client_sous_contrat'] ?? null)
            ->setNumeroClient($oldData['numero_client'] ?? null)
            ->setLibelleClient($oldData['libelle_client'] ?? null)
            // OR
            ->setNumeroOr($oldData['numero_or'] ?? null)
            ->setStatutOr($oldData['statut_or'] ?? null)
            // Devis
            ->setDemandeDevis($oldData['demande_devis'] ?? null)
            ->setNumeroDevisRattacher($oldData['numero_devis_rattache'] ?? null)
            ->setStatutDevis($oldData['statut_devis'] ?? null)
            //Facture
            ->setEtatFacturation($oldData['etat_facturation'] ?? null)
            //RI
            ->setRi($oldData['ri'] ?? '0/0')

            // Annulation
            ->setEstAnnuler($oldData['a_annuler'] ?? false)
            // Avoir
            ->setEstDitAvoir($oldData['a_dit_avoir'] ?? false)
            ->setEstDitRefacturation($oldData['a_dit_refacturation'] ?? false)
            ->setNumeroDemandeDitAvoir($oldData['numero_demande_dit_avoir'] ?? null)
            ->setNumeroDemandeDitRefacturation($oldData['numero_demande_dit_refacturation'] ?? null)
            //Matériel
            ->setIdMateriel($oldData['ID_Materiel'] ?? 0)
            ->setHeureMachine($oldData['heure_machine'] ?? 0)
            ->setKmMachine($oldData['km_machine'] ?? 0)
            // piece joint
            ->setPieceJoint01($oldData['piece_joint1'] ?? null)
            ->setPieceJoint02($oldData['piece_joint2'] ?? null)
            ->setPieceJoint03($oldData['piece_joint'] ?? null)
            // section
            ->setSectionAffectee($oldData['section_affectee'] ?? null)
            ->setSectionSupport1($oldData['section_support_1'] ?? null)
            ->setSectionSupport2($oldData['section_support_2'] ?? null)
            ->setSectionSupport3($oldData['section_support_3'] ?? null)
            // Autres
            ->setEstAtePolTana($oldData['a_ate_pol_tana'] ?? false)
            ->setNumeroMigration($oldData['num_migr'] ?? null)

        ;
    }

    private function mapDateFields(Dit $dit, array $oldData)
    {
        if (!empty($oldData['date_prevue_travaux'])) {
            $date = $this->dateTimeConverter->convertToDate($oldData['date_prevue_travaux']);
            if ($date) {
                $dit->setDatePrevueTravaux($date);
            }
        }
        // OR
        if (!empty($oldData['date_or'])) {
            $date = $this->dateTimeConverter->convertToDate($oldData['date_or']);
            if ($date) {
                $dit->setDateOr($date);
            }
        }
        if (!empty($oldData['date_validation_or'])) {
            $date = $this->dateTimeConverter->convertToDate($oldData['date_validation_or']);
            if ($date) {
                $dit->setDateValidationOr($date);
            }
        }
        // Annulation
        if (!empty($oldData['date_annulation'])) {
            $date = $this->dateTimeConverter->convertToDate($oldData['date_annulation']);
            if ($date) {
                $dit->setDateAnnulation($date);
            }
        }
    }

    private function mapRelations(Dit $dit, array $oldData)
    {

        // type de document
        if (!empty($oldData['type_document'])) {
            $typeDocument = $this->findWorTypeDocument((int)$oldData['type_document']);

            if ($typeDocument) {
                $dit->setWorTypeDocument($typeDocument);
            }
        }

        // niveau d'urgence
        if (!empty($oldData['id_niveau_urgence'])) {
            $niveauUrgence = $this->findWorNiveauUrgence((int)$oldData['id_niveau_urgence']);
            if ($niveauUrgence) {
                $dit->setWorNiveauUrgence($niveauUrgence);
            }
        }

        // categorie ate
        if (!empty($oldData['categorie_demande'])) {
            $categorieAte = $this->findCategorieAte((int) $oldData['categorie_demande']);
            if ($categorieAte) {
                $dit->setCategorieAteApp($categorieAte);
            }
        }

        // Relation StatutDemande
        $statut = $this->relationMapper->mapStatutDemande($oldData, 'DIT');
        if ($statut) {
            $dit->setStatutDemande($statut);
        }

        // CreatedBy (User)
        if (!empty($oldData['utilisateur_demandeur'])) {
            $user = $this->relationMapper->mapUser($oldData['utilisateur_demandeur']);
            if ($user) {
                $dit->setCreatedBy($user);
            }
        }

        // Relations Agence et Service émetteur
        if (!empty($oldData['agence_emetteur_id'])) {
            $agence = $this->relationMapper->mapAgence($oldData['agence_emetteur_id']);
            if ($agence) {
                $dit->setAgenceEmetteurId($agence);
            }
        }

        if (!empty($oldData['service_emetteur_id'])) {
            $service = $this->relationMapper->mapService($oldData['service_emetteur_id']);
            if ($service) {
                $dit->setServiceEmetteurId($service);
            }
        }

        // Relations Agence et Service débiteur
        if (!empty($oldData['agence_debiteur_id'])) {
            $agence = $this->relationMapper->mapAgence($oldData['agence_debiteur_id']);
            if ($agence) {
                $dit->setAgenceDebiteurId($agence);
            }
        }

        if (!empty($oldData['service_debiteur_id'])) {
            $service = $this->relationMapper->mapService($oldData['service_debiteur_id'], true);
            if ($service) {
                $dit->setServiceDebiteur($service);
            }
        }
    }

    /** ================ TYPE DE DOCUMENT =========================== */
    /**
     * Récupère le type du document par le code du ancien type document
     * Cette fonction recupère l'entité type de document dans la nouvelle base de données 
     * à partir du code de l'ancien base
     * @param int $id
     * @return WorTypeDocument|null
     */
    private function findWorTypeDocument(int $id): ?WorTypeDocument
    {
        if (empty($id)) return null;

        $codeDocument = $this->findCodeDocumentWorTypeDocument($id);
        if (empty($codeDocument)) return null;

        $worTypeDocumentRepository = $this->em->getRepository(WorTypeDocument::class);
        return $worTypeDocumentRepository->findOneBy(['codeDocument' => $codeDocument]);
    }

    /**
     *  Find Code Document by id
     * Cette fonction recupère le code du type de document dans l'ancien base de donnée
     * @param int $id
     * @return string|null
     */
    private function findCodeDocumentWorTypeDocument(int $id): ?string
    {
        try {
            $result = $this->legacyConnection->fetchAssociative(
                'SELECT code_document FROM wor_type_document WHERE id = :id',
                ['id' => $id]
            );
            return $result['code_document'] ?? null;
        } catch (\Exception $e) {
            $this->logger->error('Erreur récupération du code du type de document', [
                'id' => $id,
                'error' => $e->getMessage(),
            ]);
            return null;
        }
    }

    /** =============== NIVEAU D'URGENCE ===================== */
    /**
     * Récupère le niveau d'urgence par le description du ancien niveau d'urgence
     * Cette fonction recupère l'entité niveau d'urgence dans la nouvelle base de données 
     * à partir du description de l'ancien base de donnée
     * @param int $id
     * @return WorNiveauUrgence|null
     */
    private function findWorNiveauUrgence(int $id): ?WorNiveauUrgence
    {
        if (empty($id)) return null;

        $codeDocument = $this->findDescriptionNiveauUrgence($id);
        if (empty($codeDocument)) return null;

        $worNiveauUrgenceRepository = $this->em->getRepository(WorNiveauUrgence::class);
        return $worNiveauUrgenceRepository->findOneBy(['code' => $codeDocument]);
    }

    private function findDescriptionNiveauUrgence(int $id)
    {
        try {
            $result = $this->legacyConnection->fetchAssociative(
                'SELECT description FROM wor_niveau_urgence WHERE id = :id',
                ['id' => $id]
            );
            return $result['description'] ?? null;
        } catch (\Exception $e) {
            $this->logger->error('Erreur récupération du description du niveau d\'urgence', [
                'id' => $id,
                'error' => $e->getMessage(),
            ]);
            return null;
        }
    }


    /** =============== CATEGORIE ATE ===================== */
    /**
     * Récupère le catégorie ate app par le libelle de l'ancien base
     * 
     * @param int $id
     * @return CategorieAteApp|null
     */
    private function findCategorieAte(int $id): ?CategorieAteApp
    {
        if (empty($id)) return null;

        $libelle = $this->findLibelleCategorieAte($id);
        if (empty($libelle)) return null;

        $categorieAteRepository = $this->em->getRepository(CategorieAteApp::class);
        return $categorieAteRepository->findOneBy(['libelleCategorieAteApp' => $libelle]);
    }

    private function findLibelleCategorieAte(int $id)
    {
        try {
            $result = $this->legacyConnection->fetchAssociative(
                'SELECT libelle_categorie_ate_app FROM categorie_ate_app WHERE id = :id',
                ['id' => $id]
            );
            return $result['libelle_categorie_ate_app'] ?? null;
        } catch (\Exception $e) {
            $this->logger->error('Erreur récupération du libele catégorie ate', [
                'id' => $id,
                'error' => $e->getMessage(),
            ]);
            return null;
        }
    }
}
