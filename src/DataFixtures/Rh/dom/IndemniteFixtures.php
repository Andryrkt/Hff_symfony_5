<?php

namespace App\DataFixtures\Rh\dom;


use App\Entity\Rh\Dom\Indemnite;
use Doctrine\Persistence\ObjectManager;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;

class IndemniteFixtures extends Fixture implements DependentFixtureInterface
{
    public function load(ObjectManager $manager)
    {
        $indemnites = [
            [
                'montant' => 50000,
                'siteId' => 'dom_site_zones_touristiques',
                'categorieId' => 'dom_categorie_chef_service',
                'rmqId' => 'dom_rmq_std',
                'sousTypeDocId' => 'sous_type_mission'
            ],
            [
                'montant' => 15000,
                'siteId' => 'dom_site_hors_tana_moins_de_24h',
                'categorieId' => 'dom_categorie_chef_service',
                'rmqId' => 'dom_rmq_std',
                'sousTypeDocId' => 'sous_type_mission'
            ],
            [
                'montant' => 45000,
                'siteId' => 'dom_site_zone_enclavees',
                'categorieId' => 'dom_categorie_chef_service',
                'rmqId' => 'dom_rmq_std',
                'sousTypeDocId' => 'sous_type_mission'
            ],
            [
                'montant' => 40000,
                'siteId' => 'dom_site_autres_villes',
                'categorieId' => 'dom_categorie_chef_service',
                'rmqId' => 'dom_rmq_std',
                'sousTypeDocId' => 'sous_type_mission'
            ],
            [
                'montant' => 45000,
                'siteId' => 'dom_site_autres_villes',
                'categorieId' => 'dom_categorie_cadre_hc',
                'rmqId' => 'dom_rmq_std',
                'sousTypeDocId' => 'sous_type_mission'
            ],
            [
                'montant' => 50000,
                'siteId' => 'dom_site_zone_enclavees',
                'categorieId' => 'dom_categorie_cadre_hc',
                'rmqId' => 'dom_rmq_std',
                'sousTypeDocId' => 'sous_type_mission'
            ],
            [
                'montant' => 60000,
                'siteId' => 'dom_site_zones_touristiques',
                'categorieId' => 'dom_categorie_cadre_hc',
                'rmqId' => 'dom_rmq_std',
                'sousTypeDocId' => 'sous_type_mission'
            ],
            [
                'montant' => 10000,
                'siteId' => 'dom_site_hors_tana_moins_de_24h',
                'categorieId' => 'dom_categorie_cadre_hc',
                'rmqId' => 'dom_rmq_std',
                'sousTypeDocId' => 'sous_type_mission'
            ],
            [
                'montant' => 8800,
                'siteId' => 'dom_site_hors_tana_moins_de_24h',
                'categorieId' => 'dom_categorie_agents_maitrise_emplyes_specialises',
                'rmqId' => 'dom_rmq_std',
                'sousTypeDocId' => 'sous_type_mission'
            ],
            [
                'montant' => 48000,
                'siteId' => 'dom_site_zones_touristiques',
                'categorieId' => 'dom_categorie_agents_maitrise_emplyes_specialises',
                'rmqId' => 'dom_rmq_std',
                'sousTypeDocId' => 'sous_type_mission'
            ],
            [
                'montant' => 45000,
                'siteId' => 'dom_site_zone_enclavees',
                'categorieId' => 'dom_categorie_agents_maitrise_emplyes_specialises',
                'rmqId' => 'dom_rmq_std',
                'sousTypeDocId' => 'sous_type_mission'
            ],
            [
                'montant' => 40000,
                'siteId' => 'dom_site_autres_villes',
                'categorieId' => 'dom_categorie_agents_maitrise_emplyes_specialises',
                'rmqId' => 'dom_rmq_std',
                'sousTypeDocId' => 'sous_type_mission'
            ],
            [
                'montant' => 35000,
                'siteId' => 'dom_site_zone_enclavees',
                'categorieId' => 'dom_categorie_ouvriers_chauffeurs',
                'rmqId' => 'dom_rmq_std',
                'sousTypeDocId' => 'sous_type_mission'
            ],
            [
                'montant' => 30000,
                'siteId' => 'dom_site_autres_villes',
                'categorieId' => 'dom_categorie_ouvriers_chauffeurs',
                'rmqId' => 'dom_rmq_std',
                'sousTypeDocId' => 'sous_type_mission'
            ],
            [
                'montant' => 40000,
                'siteId' => 'dom_site_zones_touristiques',
                'categorieId' => 'dom_categorie_ouvriers_chauffeurs',
                'rmqId' => 'dom_rmq_std',
                'sousTypeDocId' => 'sous_type_mission'
            ],
            [
                'montant' => 7000,
                'siteId' => 'dom_site_hors_tana_moins_de_24h',
                'categorieId' => 'dom_categorie_ouvriers_chauffeurs',
                'rmqId' => 'dom_rmq_std',
                'sousTypeDocId' => 'sous_type_mission'
            ],
            [
                'montant' => 0,
                'siteId' => 'dom_site_tana',
                'categorieId' => 'dom_categorie_toute_categorie',
                'rmqId' => 'dom_rmq_50',
                'sousTypeDocId' => 'sous_type_mission'
            ],
            [
                'montant' => 14400,
                'siteId' => 'dom_site_fort_dauphin',
                'categorieId' => 'dom_categorie_toute_categorie',
                'rmqId' => 'dom_rmq_50',
                'sousTypeDocId' => 'sous_type_mission'
            ],
            [
                'montant' => 14400,
                'siteId' => 'dom_site_autres_site_enclaves',
                'categorieId' => 'dom_categorie_toute_categorie',
                'rmqId' => 'dom_rmq_50',
                'sousTypeDocId' => 'sous_type_mission'
            ],
            [
                'montant' => 9600,
                'siteId' => 'dom_site_hors_tana',
                'categorieId' => 'dom_categorie_toute_categorie',
                'rmqId' => 'dom_rmq_50',
                'sousTypeDocId' => 'sous_type_mission'
            ],
            [
                'montant' => 22000,
                'siteId' => 'dom_site_hors_tana',
                'categorieId' => 'dom_categorie_chauffeur_porte_char',
                'rmqId' => 'dom_rmq_50',
                'sousTypeDocId' => 'sous_type_mission'
            ],
            [
                'montant' => 22000,
                'siteId' => 'dom_site_fort_dauphin',
                'categorieId' => 'dom_categorie_chauffeur_porte_char',
                'rmqId' => 'dom_rmq_50',
                'sousTypeDocId' => 'sous_type_mission'
            ],
            [
                'montant' => 22000,
                'siteId' => 'dom_site_autres_site_enclaves',
                'categorieId' => 'dom_categorie_chauffeur_porte_char',
                'rmqId' => 'dom_rmq_50',
                'sousTypeDocId' => 'sous_type_mission'
            ],
            [
                'montant' => 3000,
                'siteId' => 'dom_site_tana',
                'categorieId' => 'dom_categorie_chauffeur_porte_char',
                'rmqId' => 'dom_rmq_50',
                'sousTypeDocId' => 'sous_type_mission'
            ],
            [
                'montant' => 3000,
                'siteId' => 'dom_site_tana',
                'categorieId' => 'dom_categorie_aide_chauffeur',
                'rmqId' => 'dom_rmq_50',
                'sousTypeDocId' => 'sous_type_mission'
            ],
            [
                'montant' => 16000,
                'siteId' => 'dom_site_autres_site_enclaves',
                'categorieId' => 'dom_categorie_aide_chauffeur',
                'rmqId' => 'dom_rmq_50',
                'sousTypeDocId' => 'sous_type_mission'
            ],
            [
                'montant' => 16000,
                'siteId' => 'dom_site_fort_dauphin',
                'categorieId' => 'dom_categorie_aide_chauffeur',
                'rmqId' => 'dom_rmq_50',
                'sousTypeDocId' => 'sous_type_mission'
            ],
            [
                'montant' => 16000,
                'siteId' => 'dom_site_hors_tana',
                'categorieId' => 'dom_categorie_aide_chauffeur',
                'rmqId' => 'dom_rmq_50',
                'sousTypeDocId' => 'sous_type_mission'
            ],
            [
                'montant' => 50000,
                'siteId' => 'dom_site_zones_touristiques',
                'categorieId' => 'dom_categorie_chef_service',
                'rmqId' => 'dom_rmq_std',
                'sousTypeDocId' => 'sous_type_trop_percu'
            ],
            [
                'montant' => 15000,
                'siteId' => 'dom_site_hors_tana_moins_de_24h',
                'categorieId' => 'dom_categorie_chef_service',
                'rmqId' => 'dom_rmq_std',
                'sousTypeDocId' => 'sous_type_trop_percu'
            ],
            [
                'montant' => 45000,
                'siteId' => 'dom_site_zone_enclavees',
                'categorieId' => 'dom_categorie_chef_service',
                'rmqId' => 'dom_rmq_std',
                'sousTypeDocId' => 'sous_type_trop_percu'
            ],
            [
                'montant' => 40000,
                'siteId' => 'dom_site_autres_villes',
                'categorieId' => 'dom_categorie_chef_service',
                'rmqId' => 'dom_rmq_std',
                'sousTypeDocId' => 'sous_type_trop_percu'
            ],
            [
                'montant' => 45000,
                'siteId' => 'dom_site_autres_villes',
                'categorieId' => 'dom_categorie_cadre_hc',
                'rmqId' => 'dom_rmq_std',
                'sousTypeDocId' => 'sous_type_trop_percu'
            ],
            [
                'montant' => 50000,
                'siteId' => 'dom_site_zone_enclavees',
                'categorieId' => 'dom_categorie_cadre_hc',
                'rmqId' => 'dom_rmq_std',
                'sousTypeDocId' => 'sous_type_trop_percu'
            ],
            [
                'montant' => 60000,
                'siteId' => 'dom_site_zones_touristiques',
                'categorieId' => 'dom_categorie_cadre_hc',
                'rmqId' => 'dom_rmq_std',
                'sousTypeDocId' => 'sous_type_trop_percu'
            ],
            [
                'montant' => 10000,
                'siteId' => 'dom_site_hors_tana_moins_de_24h',
                'categorieId' => 'dom_categorie_cadre_hc',
                'rmqId' => 'dom_rmq_std',
                'sousTypeDocId' => 'sous_type_trop_percu'
            ],
            [
                'montant' => 8800,
                'siteId' => 'dom_site_hors_tana_moins_de_24h',
                'categorieId' => 'dom_categorie_agents_maitrise_emplyes_specialises',
                'rmqId' => 'dom_rmq_std',
                'sousTypeDocId' => 'sous_type_trop_percu'
            ],
            [
                'montant' => 48000,
                'siteId' => 'dom_site_zones_touristiques',
                'categorieId' => 'dom_categorie_agents_maitrise_emplyes_specialises',
                'rmqId' => 'dom_rmq_std',
                'sousTypeDocId' => 'sous_type_trop_percu'
            ],
            [
                'montant' => 45000,
                'siteId' => 'dom_site_zone_enclavees',
                'categorieId' => 'dom_categorie_agents_maitrise_emplyes_specialises',
                'rmqId' => 'dom_rmq_std',
                'sousTypeDocId' => 'sous_type_trop_percu'
            ],
            [
                'montant' => 40000,
                'siteId' => 'dom_site_autres_villes',
                'categorieId' => 'dom_categorie_agents_maitrise_emplyes_specialises',
                'rmqId' => 'dom_rmq_std',
                'sousTypeDocId' => 'sous_type_trop_percu'
            ],
            [
                'montant' => 35000,
                'siteId' => 'dom_site_zone_enclavees',
                'categorieId' => 'dom_categorie_ouvriers_chauffeurs',
                'rmqId' => 'dom_rmq_std',
                'sousTypeDocId' => 'sous_type_trop_percu'
            ],
            [
                'montant' => 30000,
                'siteId' => 'dom_site_autres_villes',
                'categorieId' => 'dom_categorie_ouvriers_chauffeurs',
                'rmqId' => 'dom_rmq_std',
                'sousTypeDocId' => 'sous_type_trop_percu'
            ],
            [
                'montant' => 40000,
                'siteId' => 'dom_site_zones_touristiques',
                'categorieId' => 'dom_categorie_ouvriers_chauffeurs',
                'rmqId' => 'dom_rmq_std',
                'sousTypeDocId' => 'sous_type_trop_percu'
            ],
            [
                'montant' => 7000,
                'siteId' => 'dom_site_hors_tana_moins_de_24h',
                'categorieId' => 'dom_categorie_ouvriers_chauffeurs',
                'rmqId' => 'dom_rmq_std',
                'sousTypeDocId' => 'sous_type_trop_percu'
            ],
            [
                'montant' => 0,
                'siteId' => 'dom_site_tana',
                'categorieId' => 'dom_categorie_toute_categorie',
                'rmqId' => 'dom_rmq_50',
                'sousTypeDocId' => 'sous_type_trop_percu'
            ],
            [
                'montant' => 14400,
                'siteId' => 'dom_site_fort_dauphin',
                'categorieId' => 'dom_categorie_toute_categorie',
                'rmqId' => 'dom_rmq_50',
                'sousTypeDocId' => 'sous_type_trop_percu'
            ],
            [
                'montant' => 14400,
                'siteId' => 'dom_site_autres_site_enclaves',
                'categorieId' => 'dom_categorie_toute_categorie',
                'rmqId' => 'dom_rmq_50',
                'sousTypeDocId' => 'sous_type_trop_percu'
            ],
            [
                'montant' => 9600,
                'siteId' => 'dom_site_hors_tana',
                'categorieId' => 'dom_categorie_toute_categorie',
                'rmqId' => 'dom_rmq_50',
                'sousTypeDocId' => 'sous_type_trop_percu'
            ],
            [
                'montant' => 22000,
                'siteId' => 'dom_site_hors_tana',
                'categorieId' => 'dom_categorie_chauffeur_porte_char',
                'rmqId' => 'dom_rmq_50',
                'sousTypeDocId' => 'sous_type_trop_percu'
            ],
            [
                'montant' => 22000,
                'siteId' => 'dom_site_fort_dauphin',
                'categorieId' => 'dom_categorie_chauffeur_porte_char',
                'rmqId' => 'dom_rmq_50',
                'sousTypeDocId' => 'sous_type_trop_percu'
            ],
            [
                'montant' => 22000,
                'siteId' => 'dom_site_autres_site_enclaves',
                'categorieId' => 'dom_categorie_chauffeur_porte_char',
                'rmqId' => 'dom_rmq_50',
                'sousTypeDocId' => 'sous_type_trop_percu'
            ],
            [
                'montant' => 3000,
                'siteId' => 'dom_site_tana',
                'categorieId' => 'dom_categorie_chauffeur_porte_char',
                'rmqId' => 'dom_rmq_50',
                'sousTypeDocId' => 'sous_type_trop_percu'
            ],
            [
                'montant' => 3000,
                'siteId' => 'dom_site_tana',
                'categorieId' => 'dom_categorie_aide_chauffeur',
                'rmqId' => 'dom_rmq_50',
                'sousTypeDocId' => 'sous_type_trop_percu'
            ],
            [
                'montant' => 16000,
                'siteId' => 'dom_site_autres_site_enclaves',
                'categorieId' => 'dom_categorie_aide_chauffeur',
                'rmqId' => 'dom_rmq_50',
                'sousTypeDocId' => 'sous_type_trop_percu'
            ],
            [
                'montant' => 16000,
                'siteId' => 'dom_site_fort_dauphin',
                'categorieId' => 'dom_categorie_aide_chauffeur',
                'rmqId' => 'dom_rmq_50',
                'sousTypeDocId' => 'sous_type_trop_percu'
            ],
            [
                'montant' => 16000,
                'siteId' => 'dom_site_hors_tana',
                'categorieId' => 'dom_categorie_aide_chauffeur',
                'rmqId' => 'dom_rmq_50',
                'sousTypeDocId' => 'sous_type_trop_percu'
            ],
            [
                'montant' => 50000,
                'siteId' => 'dom_site_zones_touristiques',
                'categorieId' => 'dom_categorie_chef_service',
                'rmqId' => 'dom_rmq_std',
                'sousTypeDocId' => 'sous_type_mutation'
            ],
            [
                'montant' => 15000,
                'siteId' => 'dom_site_hors_tana_moins_de_24h',
                'categorieId' => 'dom_categorie_chef_service',
                'rmqId' => 'dom_rmq_std',
                'sousTypeDocId' => 'sous_type_mutation'
            ],
            [
                'montant' => 45000,
                'siteId' => 'dom_site_zone_enclavees',
                'categorieId' => 'dom_categorie_chef_service',
                'rmqId' => 'dom_rmq_std',
                'sousTypeDocId' => 'sous_type_mutation'
            ],
            [
                'montant' => 40000,
                'siteId' => 'dom_site_autres_villes',
                'categorieId' => 'dom_categorie_chef_service',
                'rmqId' => 'dom_rmq_std',
                'sousTypeDocId' => 'sous_type_mutation'
            ],
            [
                'montant' => 45000,
                'siteId' => 'dom_site_autres_villes',
                'categorieId' => 'dom_categorie_cadre_hc',
                'rmqId' => 'dom_rmq_std',
                'sousTypeDocId' => 'sous_type_mutation'
            ],
            [
                'montant' => 50000,
                'siteId' => 'dom_site_zone_enclavees',
                'categorieId' => 'dom_categorie_cadre_hc',
                'rmqId' => 'dom_rmq_std',
                'sousTypeDocId' => 'sous_type_mutation'
            ],
            [
                'montant' => 60000,
                'siteId' => 'dom_site_zones_touristiques',
                'categorieId' => 'dom_categorie_cadre_hc',
                'rmqId' => 'dom_rmq_std',
                'sousTypeDocId' => 'sous_type_mutation'
            ],
            [
                'montant' => 10000,
                'siteId' => 'dom_site_hors_tana_moins_de_24h',
                'categorieId' => 'dom_categorie_cadre_hc',
                'rmqId' => 'dom_rmq_std',
                'sousTypeDocId' => 'sous_type_mutation'
            ],
            [
                'montant' => 8800,
                'siteId' => 'dom_site_hors_tana_moins_de_24h',
                'categorieId' => 'dom_categorie_agents_maitrise_emplyes_specialises',
                'rmqId' => 'dom_rmq_std',
                'sousTypeDocId' => 'sous_type_mutation'
            ],
            [
                'montant' => 48000,
                'siteId' => 'dom_site_zones_touristiques',
                'categorieId' => 'dom_categorie_agents_maitrise_emplyes_specialises',
                'rmqId' => 'dom_rmq_std',
                'sousTypeDocId' => 'sous_type_mutation'
            ],
            [
                'montant' => 45000,
                'siteId' => 'dom_site_zone_enclavees',
                'categorieId' => 'dom_categorie_agents_maitrise_emplyes_specialises',
                'rmqId' => 'dom_rmq_std',
                'sousTypeDocId' => 'sous_type_mutation'
            ],
            [
                'montant' => 40000,
                'siteId' => 'dom_site_autres_villes',
                'categorieId' => 'dom_categorie_agents_maitrise_emplyes_specialises',
                'rmqId' => 'dom_rmq_std',
                'sousTypeDocId' => 'sous_type_mutation'
            ],
            [
                'montant' => 35000,
                'siteId' => 'dom_site_zone_enclavees',
                'categorieId' => 'dom_categorie_ouvriers_chauffeurs',
                'rmqId' => 'dom_rmq_std',
                'sousTypeDocId' => 'sous_type_mutation'
            ],
            [
                'montant' => 30000,
                'siteId' => 'dom_site_autres_villes',
                'categorieId' => 'dom_categorie_ouvriers_chauffeurs',
                'rmqId' => 'dom_rmq_std',
                'sousTypeDocId' => 'sous_type_mutation'
            ],
            [
                'montant' => 40000,
                'siteId' => 'dom_site_zones_touristiques',
                'categorieId' => 'dom_categorie_ouvriers_chauffeurs',
                'rmqId' => 'dom_rmq_std',
                'sousTypeDocId' => 'sous_type_mutation'
            ],
            [
                'montant' => 7000,
                'siteId' => 'dom_site_hors_tana_moins_de_24h',
                'categorieId' => 'dom_categorie_ouvriers_chauffeurs',
                'rmqId' => 'dom_rmq_std',
                'sousTypeDocId' => 'sous_type_mutation'
            ],
        ];


        foreach ($indemnites as $indemniteData) {
            $indemnite = new Indemnite();
            $indemnite->setMontant($indemniteData['montant'])

                ->setSiteId($this->getReference($indemniteData['siteId']))
                ->setCategorieId($this->getReference($indemniteData['categorieId']))
                ->setRmqId($this->getReference($indemniteData['rmqId']))
                ->setSousTypeDocumentId($this->getReference($indemniteData['sousTypeDocId']))
            ;

            $manager->persist($indemnite);
        }

        $manager->flush();
    }

    public function getDependencies(): array
    {
        return [
            SiteFixtures::class,
            CategorieFixtures::class,
            RmqFixtures::class,
            SousTypeDocumentFixtures::class
        ];
    }
}
