<?php

namespace App\DataFixtures\Admin\ApplicationGroupe;

use App\Entity\Admin\ApplicationGroupe\Vignette;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Bundle\FixturesBundle\FixtureGroupInterface;
use Doctrine\Persistence\ObjectManager;

class VignetteFixtures extends Fixture implements FixtureGroupInterface
{
    public static function getGroups(): array
    {
        return ['prod'];
    }
    public function load(ObjectManager $manager)
    {
        $vignettes = [
            ['nom' => 'Documentation', 'description' => 'module de gestion de documentation', 'reference' => 'vignette_documentation'],
            ['nom' => 'Reporting', 'description' => 'module de gestion de reporting', 'reference' => 'vignette_reporting'],
            ['nom' => 'Compta', 'description' => 'module de gestion de comptabilité', 'reference' => 'vignette_compta'],
            ['nom' => 'RH', 'description' => 'Module de gestion des ressources humaines : congés, mutations, ordres de mission, temporaires.', 'reference' => 'vignette_rh'],
            ['nom' => 'Matériel', 'description' => 'Module de gestion des matériels', 'reference' => 'vignette_materiel'],
            ['nom' => 'Atelier', 'description' => 'Module de gestion des interventions et plannings de l’atelier.', 'reference' => 'vignette_atelier'],
            ['nom' => 'Magasin', 'description' => 'Module de gestion magasin', 'reference' => 'vignette_magasin'],
            ['nom' => 'Appro', 'description' => 'Module de gestion des demandes d’achat et commandes fournisseurs.', 'reference' => 'vignette_appro'],
            ['nom' => 'IT', 'description' => 'Module de gestion des tickets', 'reference' => 'vignette_it'],
            ['nom' => 'POL', 'description' => null, 'reference' => 'vignette_pol'],
            ['nom' => 'Energie', 'description' => null, 'reference' => 'vignette_energie'],
            ['nom' => 'HSE', 'description' => null, 'reference' => 'vignette_hse']
        ];

        foreach ($vignettes as $vignetteData) {
            $vignette = new Vignette();
            $vignette->setNom($vignetteData['nom']);

            $manager->persist($vignette);
            $this->addReference($vignetteData['reference'], $vignette);
        }

        $manager->flush();
    }
}
