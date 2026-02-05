<?php

namespace App\Service\Hf\Atelier\Dit;

use Doctrine\ORM\QueryBuilder;
use App\Contract\Dto\SearchDtoInterface;
use App\Constants\Hf\Atelier\Dit\StatutDitConstants;

class DitSearchFilter
{
    /**
     * Applique tous les filtres de recherche au QueryBuilder
     */
    public function applyFilters(QueryBuilder $queryBuilder, SearchDtoInterface $searchDto): void
    {
        $this->applyQuandPasDeRecherche($queryBuilder, $searchDto);
        $this->applyRelationFilter($queryBuilder, $searchDto);
        $this->applyInfoMaterielFilter($queryBuilder, $searchDto);
        $this->applyOrFilter($queryBuilder, $searchDto);
        $this->applyDateFilter($queryBuilder, $searchDto);
        $this->applyCommonFilters($queryBuilder, $searchDto);
        $this->applySection($queryBuilder, $searchDto);
    }

    /**
     * permet d'appliquer les filtres quand il n'y a pas de recherche fait par l'utilisateur
     */
    private function applyQuandPasDeRecherche(QueryBuilder $queryBuilder, SearchDtoInterface $searchDto): void
    {
        if (empty($searchDto->numOr) && empty($searchDto->numDevis) && empty($searchDto->numDit)) {
            $queryBuilder->andWhere('d.statutOr NOT LIKE :statutRefuser OR d.statutOr IS NULL')
                ->setParameter('statutRefuser', 'Refusé%');
        }

        $statusesDefault = [
            StatutDitConstants::STATUT_A_AFFECTER,
            StatutDitConstants::STATUT_AFFECTEE_SECTION,
            StatutDitConstants::STATUT_CLOTUREE_VALIDEE
        ];

        if (empty($searchDto->numDit) && empty($searchDto->idMateriel) && empty($searchDto->numParc) && empty($searchDto->numSerie) && (empty($searchDto->numOr) && $searchDto->numOr == 0) && empty($searchDto->etatFacture)) {
            $queryBuilder->andWhere($queryBuilder->expr()->in('s.description', ':excludedStatuses'))
                ->setParameter('excludedStatuses', $statusesDefault);
        }
    }

    /**
     * permet d'appliquer les filtres sur les relations (niveau d'urgence, type de document, statut demande)
     */
    private function applyRelationFilter(QueryBuilder $queryBuilder, SearchDtoInterface $searchDto): void
    {
        if (!empty($searchDto->niveauUrgence)) {
            $queryBuilder->andWhere('wn.worNiveauUrgence LIKE :niveauUrgence')
                ->setParameter('niveauUrgence', '%' . $searchDto->niveauUrgence->getCode() . '%');
        }

        if (!empty($searchDto->typeDocument)) {
            $queryBuilder->andWhere('wd.worTypeDocument LIKE :typeDocument')
                ->setParameter('typeDocument', '%' . $searchDto->typeDocument->getDescription() . '%');
        }

        if (!empty($searchDto->statut)) {
            $queryBuilder->andWhere('s.description LIKE :statut')
                ->setParameter('statut', '%' . $searchDto->statut->getDescription() . '%');
        }
    }

    /**
     * permet d'appliquer les filtres sur les informations du materiel
     */
    private function applyInfoMaterielFilter(QueryBuilder $queryBuilder, SearchDtoInterface $searchDto): void
    {
        if (!empty($searchDto->idMateriel)) {
            $queryBuilder->andWhere('d.idMateriel = :idMateriel')
                ->setParameter('idMateriel', $searchDto->idMateriel);
        }
    }

    /**
     * permet d'appliquer les filtres sur le numero Or et le statut Or
     */
    private function applyOrFilter(QueryBuilder $queryBuilder, SearchDtoInterface $searchDto): void
    {
        //filtre selon le numero Or
        if (!empty($searchDto->numOr) && $searchDto->numOr !== 0) {
            $queryBuilder->andWhere('d.numeroOR = :numOr')
                ->setParameter('numOr', $searchDto->numOr);
        }

        //filtre selon le numero Or mais pour le elseif filtre tous les listes de ne pas afficher les statuts réfusé
        if (!empty($searchDto->statutOr)) {
            $queryBuilder->andWhere('d.statutOr = :statutOr')
                ->setParameter('statutOr',  $searchDto->statutOr);
        }

        // filtre pour les dit sans or
        if ($searchDto->ditSansOr) {
            $queryBuilder->andWhere("d.numeroOR = ''");
        }
    }

    /**
     * permet d'appliquer les filtres sur la date de demande
     */
    private function applyDateFilter(QueryBuilder $queryBuilder, SearchDtoInterface $searchDto): void
    {
        if (!empty($searchDto->dateDemande['dateDebut'])) {
            $queryBuilder->andWhere('d.dateDemande >= :dateDebut')
                ->setParameter('dateDebut', $searchDto->dateDemande['dateDebut']);
        }

        if (!empty($searchDto->dateDemande['dateFin'])) {
            $queryBuilder->andWhere('d.dateDemande <= :dateFin')
                ->setParameter('dateFin', $searchDto->dateDemande['dateFin']);
        }
    }

    private function applyCommonFilters(QueryBuilder $queryBuilder, SearchDtoInterface $searchDto): void
    {
        // Filters for type, urgency, material, etc.
        if (!empty($searchDto->interneExterne)) {
            $queryBuilder->andWhere('d.interneExterne = :interneExterne')
                ->setParameter('interneExterne', $searchDto->interneExterne);
        }

        // filtre sur l'etat du facture
        if (!empty($searchDto->etatFacture)) {
            $queryBuilder->andWhere('d.etatFacturation = :etatFac')
                ->setParameter('etatFac', $searchDto->etatFacture);
        }

        //filtrer selon le numero dit
        if (!empty($searchDto->numDit)) {
            $queryBuilder->andWhere('d.numeroDemandeIntervention = :numDit')
                ->setParameter('numDit', $searchDto->numDit);
        }

        //filtrer selon le numero devis
        if (!empty($searchDto->numDevis)) {
            $queryBuilder->andWhere('d.numeroDevisRattache = :numDevis')
                ->setParameter('numDevis', $searchDto->numDevis);
        }

        //filtre selon le categorie de demande
        if (!empty($searchDto->categorie)) {
            $queryBuilder->andWhere('d.categorieDemande = :categorieDemande')
                ->setParameter('categorieDemande', $searchDto->categorie);
        }

        //filtre selon le categorie de demande
        if (!empty($searchDto->utilisateur)) {
            $queryBuilder->andWhere('d.utilisateurDemandeur LIKE :utilisateur')
                ->setParameter('utilisateur', '%' . $searchDto->utilisateur . '%');
        }

        // filtre pour les reparation realises
        if (!empty($searchDto->reparationRealise)) {
            $queryBuilder->andWhere('d.reparationRealise = :reparationRealise')
                ->setParameter('reparationRealise', $searchDto->reparationRealise);
        }
    }

    /**
     * permet d'appliquer les filtres sur les sections
     */
    private function applySection(QueryBuilder $queryBuilder, SearchDtoInterface $searchDto): void
    {
        $this->applyGenericSectionFilter($queryBuilder, 'sectionAffectee', $searchDto->sectionAffectee);
        $this->applyGenericSectionFilter($queryBuilder, 'sectionSupport1', $searchDto->sectionSupport1);
        $this->applyGenericSectionFilter($queryBuilder, 'sectionSupport2', $searchDto->sectionSupport2);
        $this->applyGenericSectionFilter($queryBuilder, 'sectionSupport3', $searchDto->sectionSupport3);
    }

    /**
     * Applique un filtre de section avec les préfixes de rôles (Chef section, etc.)
     */
    private function applyGenericSectionFilter(QueryBuilder $queryBuilder, string $fieldName, $value): void
    {
        if (empty($value)) {
            return;
        }

        $groupes = ['Chef section', 'Chef de section', 'Responsable section', 'Chef d\'équipe'];
        $orX = $queryBuilder->expr()->orX();

        foreach ($groupes as $index => $groupe) {
            $phraseConstruite = $groupe . $value;
            $paramKey = $fieldName . '_' . md5($phraseConstruite);

            if ($fieldName === 'sectionAffectee') {
                $orX->add($queryBuilder->expr()->like("d.$fieldName", ":$paramKey"));
                $queryBuilder->setParameter($paramKey, '%' . $phraseConstruite . '%');
            } else {
                $orX->add($queryBuilder->expr()->eq("d.$fieldName", ":$paramKey"));
                $queryBuilder->setParameter($paramKey, $phraseConstruite);
            }
        }

        $queryBuilder->andWhere($orX);
    }
}
