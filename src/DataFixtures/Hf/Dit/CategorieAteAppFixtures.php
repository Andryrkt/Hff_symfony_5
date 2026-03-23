<?php

namespace App\DataFixtures\Hf\Dit;

use Doctrine\Persistence\ObjectManager;
use Doctrine\Bundle\FixturesBundle\Fixture;
use App\Entity\Hf\Atelier\Dit\CategorieAteApp;

class CategorieAteAppFixtures extends Fixture
{
    public function load(ObjectManager $manager)
    {
        $categorieAteApps = [
            ['libelle' => 'LANCEMENT SAV'],
            ['libelle' => 'AUTRES'],
            ['libelle' => 'FOURNITURES PIECES'],
            ['libelle' => 'GARANTIE'],
            ['libelle' => 'RECEPTION'],
            ['libelle' => 'ENTRETIEN'],
            ['libelle' => 'REPARATION'],
            ['libelle' => 'DIAGNOSTIC'],
            ['libelle' => 'FORMATION']
        ];

        foreach ($categorieAteApps as $categorieAteAppData) {
            $categorieAteApp = new CategorieAteApp();
            $categorieAteApp->setLibelleCategorieAteApp($categorieAteAppData['libelle']);
            $manager->persist($categorieAteApp);
            $this->addReference('categorie_ate_app_' . $categorieAteAppData['libelle'], $categorieAteApp);
        }
        $manager->flush();
    }
}
