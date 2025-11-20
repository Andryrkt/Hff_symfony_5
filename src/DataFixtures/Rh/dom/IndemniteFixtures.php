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
                'site' => 'dom_site_zones_touristiques',
                'categorie' => 'dom_categorie_chef_service',
                'rmq' => 'dom_rmq_std',
                'sousTypeDocument' => 'sous_type_mission'
            ],
            [
                'montant' => 15000,
                'site' => 'dom_site_hors_tana_moins_de_24h',
                'categorie' => 'dom_categorie_chef_service',
                'rmq' => 'dom_rmq_std',
                'sousTypeDocument' => 'sous_type_mission'
            ],
            [
                'montant' => 45000,
                'site' => 'dom_site_zone_enclavees',
                'categorie' => 'dom_categorie_chef_service',
                'rmq' => 'dom_rmq_std',
                'sousTypeDocument' => 'sous_type_mission'
            ],
            [
                'montant' => 40000,
                'site' => 'dom_site_autres_villes',
                'categorie' => 'dom_categorie_chef_service',
                'rmq' => 'dom_rmq_std',
                'sousTypeDocument' => 'sous_type_mission'
            ],
            [
                'montant' => 45000,
                'site' => 'dom_site_autres_villes',
                'categorie' => 'dom_categorie_cadre_hc',
                'rmq' => 'dom_rmq_std',
                'sousTypeDocument' => 'sous_type_mission'
            ],
            [
                'montant' => 50000,
                'site' => 'dom_site_zone_enclavees',
                'categorie' => 'dom_categorie_cadre_hc',
                'rmq' => 'dom_rmq_std',
                'sousTypeDocument' => 'sous_type_mission'
            ],
            [
                'montant' => 60000,
                'site' => 'dom_site_zones_touristiques',
                'categorie' => 'dom_categorie_cadre_hc',
                'rmq' => 'dom_rmq_std',
                'sousTypeDocument' => 'sous_type_mission'
            ],
            [
                'montant' => 10000,
                'site' => 'dom_site_hors_tana_moins_de_24h',
                'categorie' => 'dom_categorie_cadre_hc',
                'rmq' => 'dom_rmq_std',
                'sousTypeDocument' => 'sous_type_mission'
            ],
            [
                'montant' => 8800,
                'site' => 'dom_site_hors_tana_moins_de_24h',
                'categorie' => 'dom_categorie_agents_maitrise_emplyes_specialises',
                'rmq' => 'dom_rmq_std',
                'sousTypeDocument' => 'sous_type_mission'
            ],
            [
                'montant' => 48000,
                'site' => 'dom_site_zones_touristiques',
                'categorie' => 'dom_categorie_agents_maitrise_emplyes_specialises',
                'rmq' => 'dom_rmq_std',
                'sousTypeDocument' => 'sous_type_mission'
            ],
            [
                'montant' => 45000,
                'site' => 'dom_site_zone_enclavees',
                'categorie' => 'dom_categorie_agents_maitrise_emplyes_specialises',
                'rmq' => 'dom_rmq_std',
                'sousTypeDocument' => 'sous_type_mission'
            ],
            [
                'montant' => 40000,
                'site' => 'dom_site_autres_villes',
                'categorie' => 'dom_categorie_agents_maitrise_emplyes_specialises',
                'rmq' => 'dom_rmq_std',
                'sousTypeDocument' => 'sous_type_mission'
            ],
            [
                'montant' => 35000,
                'site' => 'dom_site_zone_enclavees',
                'categorie' => 'dom_categorie_ouvriers_chauffeurs',
                'rmq' => 'dom_rmq_std',
                'sousTypeDocument' => 'sous_type_mission'
            ],
            [
                'montant' => 30000,
                'site' => 'dom_site_autres_villes',
                'categorie' => 'dom_categorie_ouvriers_chauffeurs',
                'rmq' => 'dom_rmq_std',
                'sousTypeDocument' => 'sous_type_mission'
            ],
            [
                'montant' => 40000,
                'site' => 'dom_site_zones_touristiques',
                'categorie' => 'dom_categorie_ouvriers_chauffeurs',
                'rmq' => 'dom_rmq_std',
                'sousTypeDocument' => 'sous_type_mission'
            ],
            [
                'montant' => 7000,
                'site' => 'dom_site_hors_tana_moins_de_24h',
                'categorie' => 'dom_categorie_ouvriers_chauffeurs',
                'rmq' => 'dom_rmq_std',
                'sousTypeDocument' => 'sous_type_mission'
            ],
            [
                'montant' => 0,
                'site' => 'dom_site_tana',
                'categorie' => 'dom_categorie_toute_categorie',
                'rmq' => 'dom_rmq_50',
                'sousTypeDocument' => 'sous_type_mission'
            ],
            [
                'montant' => 14400,
                'site' => 'dom_site_fort_dauphin',
                'categorie' => 'dom_categorie_toute_categorie',
                'rmq' => 'dom_rmq_50',
                'sousTypeDocument' => 'sous_type_mission'
            ],
            [
                'montant' => 14400,
                'site' => 'dom_site_autres_site_enclaves',
                'categorie' => 'dom_categorie_toute_categorie',
                'rmq' => 'dom_rmq_50',
                'sousTypeDocument' => 'sous_type_mission'
            ],
            [
                'montant' => 9600,
                'site' => 'dom_site_hors_tana',
                'categorie' => 'dom_categorie_toute_categorie',
                'rmq' => 'dom_rmq_50',
                'sousTypeDocument' => 'sous_type_mission'
            ],
            [
                'montant' => 22000,
                'site' => 'dom_site_hors_tana',
                'categorie' => 'dom_categorie_chauffeur_porte_char',
                'rmq' => 'dom_rmq_50',
                'sousTypeDocument' => 'sous_type_mission'
            ],
            [
                'montant' => 22000,
                'site' => 'dom_site_fort_dauphin',
                'categorie' => 'dom_categorie_chauffeur_porte_char',
                'rmq' => 'dom_rmq_50',
                'sousTypeDocument' => 'sous_type_mission'
            ],
            [
                'montant' => 22000,
                'site' => 'dom_site_autres_site_enclaves',
                'categorie' => 'dom_categorie_chauffeur_porte_char',
                'rmq' => 'dom_rmq_50',
                'sousTypeDocument' => 'sous_type_mission'
            ],
            [
                'montant' => 3000,
                'site' => 'dom_site_tana',
                'categorie' => 'dom_categorie_chauffeur_porte_char',
                'rmq' => 'dom_rmq_50',
                'sousTypeDocument' => 'sous_type_mission'
            ],
            [
                'montant' => 3000,
                'site' => 'dom_site_tana',
                'categorie' => 'dom_categorie_aide_chauffeur',
                'rmq' => 'dom_rmq_50',
                'sousTypeDocument' => 'sous_type_mission'
            ],
            [
                'montant' => 16000,
                'site' => 'dom_site_autres_site_enclaves',
                'categorie' => 'dom_categorie_aide_chauffeur',
                'rmq' => 'dom_rmq_50',
                'sousTypeDocument' => 'sous_type_mission'
            ],
            [
                'montant' => 16000,
                'site' => 'dom_site_fort_dauphin',
                'categorie' => 'dom_categorie_aide_chauffeur',
                'rmq' => 'dom_rmq_50',
                'sousTypeDocument' => 'sous_type_mission'
            ],
            [
                'montant' => 16000,
                'site' => 'dom_site_hors_tana',
                'categorie' => 'dom_categorie_aide_chauffeur',
                'rmq' => 'dom_rmq_50',
                'sousTypeDocument' => 'sous_type_mission'
            ],
            [
                'montant' => 50000,
                'site' => 'dom_site_zones_touristiques',
                'categorie' => 'dom_categorie_chef_service',
                'rmq' => 'dom_rmq_std',
                'sousTypeDocument' => 'sous_type_trop_percu'
            ],
            [
                'montant' => 15000,
                'site' => 'dom_site_hors_tana_moins_de_24h',
                'categorie' => 'dom_categorie_chef_service',
                'rmq' => 'dom_rmq_std',
                'sousTypeDocument' => 'sous_type_trop_percu'
            ],
            [
                'montant' => 45000,
                'site' => 'dom_site_zone_enclavees',
                'categorie' => 'dom_categorie_chef_service',
                'rmq' => 'dom_rmq_std',
                'sousTypeDocument' => 'sous_type_trop_percu'
            ],
            [
                'montant' => 40000,
                'site' => 'dom_site_autres_villes',
                'categorie' => 'dom_categorie_chef_service',
                'rmq' => 'dom_rmq_std',
                'sousTypeDocument' => 'sous_type_trop_percu'
            ],
            [
                'montant' => 45000,
                'site' => 'dom_site_autres_villes',
                'categorie' => 'dom_categorie_cadre_hc',
                'rmq' => 'dom_rmq_std',
                'sousTypeDocument' => 'sous_type_trop_percu'
            ],
            [
                'montant' => 50000,
                'site' => 'dom_site_zone_enclavees',
                'categorie' => 'dom_categorie_cadre_hc',
                'rmq' => 'dom_rmq_std',
                'sousTypeDocument' => 'sous_type_trop_percu'
            ],
            [
                'montant' => 60000,
                'site' => 'dom_site_zones_touristiques',
                'categorie' => 'dom_categorie_cadre_hc',
                'rmq' => 'dom_rmq_std',
                'sousTypeDocument' => 'sous_type_trop_percu'
            ],
            [
                'montant' => 10000,
                'site' => 'dom_site_hors_tana_moins_de_24h',
                'categorie' => 'dom_categorie_cadre_hc',
                'rmq' => 'dom_rmq_std',
                'sousTypeDocument' => 'sous_type_trop_percu'
            ],
            [
                'montant' => 8800,
                'site' => 'dom_site_hors_tana_moins_de_24h',
                'categorie' => 'dom_categorie_agents_maitrise_emplyes_specialises',
                'rmq' => 'dom_rmq_std',
                'sousTypeDocument' => 'sous_type_trop_percu'
            ],
            [
                'montant' => 48000,
                'site' => 'dom_site_zones_touristiques',
                'categorie' => 'dom_categorie_agents_maitrise_emplyes_specialises',
                'rmq' => 'dom_rmq_std',
                'sousTypeDocument' => 'sous_type_trop_percu'
            ],
            [
                'montant' => 45000,
                'site' => 'dom_site_zone_enclavees',
                'categorie' => 'dom_categorie_agents_maitrise_emplyes_specialises',
                'rmq' => 'dom_rmq_std',
                'sousTypeDocument' => 'sous_type_trop_percu'
            ],
            [
                'montant' => 40000,
                'site' => 'dom_site_autres_villes',
                'categorie' => 'dom_categorie_agents_maitrise_emplyes_specialises',
                'rmq' => 'dom_rmq_std',
                'sousTypeDocument' => 'sous_type_trop_percu'
            ],
            [
                'montant' => 35000,
                'site' => 'dom_site_zone_enclavees',
                'categorie' => 'dom_categorie_ouvriers_chauffeurs',
                'rmq' => 'dom_rmq_std',
                'sousTypeDocument' => 'sous_type_trop_percu'
            ],
            [
                'montant' => 30000,
                'site' => 'dom_site_autres_villes',
                'categorie' => 'dom_categorie_ouvriers_chauffeurs',
                'rmq' => 'dom_rmq_std',
                'sousTypeDocument' => 'sous_type_trop_percu'
            ],
            [
                'montant' => 40000,
                'site' => 'dom_site_zones_touristiques',
                'categorie' => 'dom_categorie_ouvriers_chauffeurs',
                'rmq' => 'dom_rmq_std',
                'sousTypeDocument' => 'sous_type_trop_percu'
            ],
            [
                'montant' => 7000,
                'site' => 'dom_site_hors_tana_moins_de_24h',
                'categorie' => 'dom_categorie_ouvriers_chauffeurs',
                'rmq' => 'dom_rmq_std',
                'sousTypeDocument' => 'sous_type_trop_percu'
            ],
            [
                'montant' => 0,
                'site' => 'dom_site_tana',
                'categorie' => 'dom_categorie_toute_categorie',
                'rmq' => 'dom_rmq_50',
                'sousTypeDocument' => 'sous_type_trop_percu'
            ],
            [
                'montant' => 14400,
                'site' => 'dom_site_fort_dauphin',
                'categorie' => 'dom_categorie_toute_categorie',
                'rmq' => 'dom_rmq_50',
                'sousTypeDocument' => 'sous_type_trop_percu'
            ],
            [
                'montant' => 14400,
                'site' => 'dom_site_autres_site_enclaves',
                'categorie' => 'dom_categorie_toute_categorie',
                'rmq' => 'dom_rmq_50',
                'sousTypeDocument' => 'sous_type_trop_percu'
            ],
            [
                'montant' => 9600,
                'site' => 'dom_site_hors_tana',
                'categorie' => 'dom_categorie_toute_categorie',
                'rmq' => 'dom_rmq_50',
                'sousTypeDocument' => 'sous_type_trop_percu'
            ],
            [
                'montant' => 22000,
                'site' => 'dom_site_hors_tana',
                'categorie' => 'dom_categorie_chauffeur_porte_char',
                'rmq' => 'dom_rmq_50',
                'sousTypeDocument' => 'sous_type_trop_percu'
            ],
            [
                'montant' => 22000,
                'site' => 'dom_site_fort_dauphin',
                'categorie' => 'dom_categorie_chauffeur_porte_char',
                'rmq' => 'dom_rmq_50',
                'sousTypeDocument' => 'sous_type_trop_percu'
            ],
            [
                'montant' => 22000,
                'site' => 'dom_site_autres_site_enclaves',
                'categorie' => 'dom_categorie_chauffeur_porte_char',
                'rmq' => 'dom_rmq_50',
                'sousTypeDocument' => 'sous_type_trop_percu'
            ],
            [
                'montant' => 3000,
                'site' => 'dom_site_tana',
                'categorie' => 'dom_categorie_chauffeur_porte_char',
                'rmq' => 'dom_rmq_50',
                'sousTypeDocument' => 'sous_type_trop_percu'
            ],
            [
                'montant' => 3000,
                'site' => 'dom_site_tana',
                'categorie' => 'dom_categorie_aide_chauffeur',
                'rmq' => 'dom_rmq_50',
                'sousTypeDocument' => 'sous_type_trop_percu'
            ],
            [
                'montant' => 16000,
                'site' => 'dom_site_autres_site_enclaves',
                'categorie' => 'dom_categorie_aide_chauffeur',
                'rmq' => 'dom_rmq_50',
                'sousTypeDocument' => 'sous_type_trop_percu'
            ],
            [
                'montant' => 16000,
                'site' => 'dom_site_fort_dauphin',
                'categorie' => 'dom_categorie_aide_chauffeur',
                'rmq' => 'dom_rmq_50',
                'sousTypeDocument' => 'sous_type_trop_percu'
            ],
            [
                'montant' => 16000,
                'site' => 'dom_site_hors_tana',
                'categorie' => 'dom_categorie_aide_chauffeur',
                'rmq' => 'dom_rmq_50',
                'sousTypeDocument' => 'sous_type_trop_percu'
            ],
            [
                'montant' => 50000,
                'site' => 'dom_site_zones_touristiques',
                'categorie' => 'dom_categorie_chef_service',
                'rmq' => 'dom_rmq_std',
                'sousTypeDocument' => 'sous_type_mutation'
            ],
            [
                'montant' => 15000,
                'site' => 'dom_site_hors_tana_moins_de_24h',
                'categorie' => 'dom_categorie_chef_service',
                'rmq' => 'dom_rmq_std',
                'sousTypeDocument' => 'sous_type_mutation'
            ],
            [
                'montant' => 45000,
                'site' => 'dom_site_zone_enclavees',
                'categorie' => 'dom_categorie_chef_service',
                'rmq' => 'dom_rmq_std',
                'sousTypeDocument' => 'sous_type_mutation'
            ],
            [
                'montant' => 40000,
                'site' => 'dom_site_autres_villes',
                'categorie' => 'dom_categorie_chef_service',
                'rmq' => 'dom_rmq_std',
                'sousTypeDocument' => 'sous_type_mutation'
            ],
            [
                'montant' => 45000,
                'site' => 'dom_site_autres_villes',
                'categorie' => 'dom_categorie_cadre_hc',
                'rmq' => 'dom_rmq_std',
                'sousTypeDocument' => 'sous_type_mutation'
            ],
            [
                'montant' => 50000,
                'site' => 'dom_site_zone_enclavees',
                'categorie' => 'dom_categorie_cadre_hc',
                'rmq' => 'dom_rmq_std',
                'sousTypeDocument' => 'sous_type_mutation'
            ],
            [
                'montant' => 60000,
                'site' => 'dom_site_zones_touristiques',
                'categorie' => 'dom_categorie_cadre_hc',
                'rmq' => 'dom_rmq_std',
                'sousTypeDocument' => 'sous_type_mutation'
            ],
            [
                'montant' => 10000,
                'site' => 'dom_site_hors_tana_moins_de_24h',
                'categorie' => 'dom_categorie_cadre_hc',
                'rmq' => 'dom_rmq_std',
                'sousTypeDocument' => 'sous_type_mutation'
            ],
            [
                'montant' => 8800,
                'site' => 'dom_site_hors_tana_moins_de_24h',
                'categorie' => 'dom_categorie_agents_maitrise_emplyes_specialises',
                'rmq' => 'dom_rmq_std',
                'sousTypeDocument' => 'sous_type_mutation'
            ],
            [
                'montant' => 48000,
                'site' => 'dom_site_zones_touristiques',
                'categorie' => 'dom_categorie_agents_maitrise_emplyes_specialises',
                'rmq' => 'dom_rmq_std',
                'sousTypeDocument' => 'sous_type_mutation'
            ],
            [
                'montant' => 45000,
                'site' => 'dom_site_zone_enclavees',
                'categorie' => 'dom_categorie_agents_maitrise_emplyes_specialises',
                'rmq' => 'dom_rmq_std',
                'sousTypeDocument' => 'sous_type_mutation'
            ],
            [
                'montant' => 40000,
                'site' => 'dom_site_autres_villes',
                'categorie' => 'dom_categorie_agents_maitrise_emplyes_specialises',
                'rmq' => 'dom_rmq_std',
                'sousTypeDocument' => 'sous_type_mutation'
            ],
            [
                'montant' => 35000,
                'site' => 'dom_site_zone_enclavees',
                'categorie' => 'dom_categorie_ouvriers_chauffeurs',
                'rmq' => 'dom_rmq_std',
                'sousTypeDocument' => 'sous_type_mutation'
            ],
            [
                'montant' => 30000,
                'site' => 'dom_site_autres_villes',
                'categorie' => 'dom_categorie_ouvriers_chauffeurs',
                'rmq' => 'dom_rmq_std',
                'sousTypeDocument' => 'sous_type_mutation'
            ],
            [
                'montant' => 40000,
                'site' => 'dom_site_zones_touristiques',
                'categorie' => 'dom_categorie_ouvriers_chauffeurs',
                'rmq' => 'dom_rmq_std',
                'sousTypeDocument' => 'sous_type_mutation'
            ],
            [
                'montant' => 7000,
                'site' => 'dom_site_hors_tana_moins_de_24h',
                'categorie' => 'dom_categorie_ouvriers_chauffeurs',
                'rmq' => 'dom_rmq_std',
                'sousTypeDocument' => 'sous_type_mutation'
            ],
        ];


        foreach ($indemnites as $indemniteData) {
            $indemnite = new Indemnite();
            $indemnite->setMontant($indemniteData['montant'])

                ->setSite($this->getReference($indemniteData['site']))
                ->setCategorie($this->getReference($indemniteData['categorie']))
                ->setRmq($this->getReference($indemniteData['rmq']))
                ->setSousTypeDocument($this->getReference($indemniteData['sousTypeDocument']))
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
