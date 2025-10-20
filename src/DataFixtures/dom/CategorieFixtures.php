<?php

namespace App\DataFixtures\dom;

use App\Entity\Dom\Categorie;
use Doctrine\Persistence\ObjectManager;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;

class CategorieFixtures extends Fixture implements DependentFixtureInterface
{
    public function load(ObjectManager $manager)
    {
        $categories = [
            [
                'description' => "Agents de maitrise, employes specialises",
                'sousType'   => 'sous_type_mission',
                'reference' => 'dom_categorie_agents_maitrise_emplyes_specialises'
            ],
            [
                'description' => "Cadre HC",
                'sousType'   => 'sous_type_mission',
                'reference' => 'dom_categorie_cadre_hc'
            ],
            [
                'description' => "Chef de service",
                'sousType'   => 'sous_type_mission',
                'reference' => 'dom_categorie_chef_service'
            ],
            [
                'description' => "Ouvriers et chauffeurs",
                'sousType'   => 'sous_type_mission',
                'reference' => 'dom_categorie_ouvriers_chauffeurs'
            ],
            [
                'description' => "Toute Categorie",
                'sousType'   => 'sous_type_mutation',
                'reference' => 'dom_categorie_toute_categorie'
            ],
            [
                'description' => "Chauffeurs porte char",
                'sousType'   => null,
                'reference' => 'dom_categorie_chauffeur_porte_char'
            ],
            [
                'description' => "Aide chauffeur",
                'sousType'   => null,
                'reference' => 'dom_categorie_aide_chauffeur'
            ],
        ];

        foreach ($categories as $categorieData) {
            $categorie = new Categorie();
            $categorie->setDescription($categorieData['description']);

            // Gestion du sous-type
            if ($categorieData['sousType'] !== null) {
                $sousType = $this->getReference($categorieData['sousType']);
                $categorie->setSousTypeDocumentId($sousType);
            }

            $manager->persist($categorie);
            $this->addReference($categorieData['reference'], $categorie);
        }

        $manager->flush();
    }

    public function getDependencies(): array
    {
        return [
            SousTypeDocumentFixtures::class,
        ];
    }
}
