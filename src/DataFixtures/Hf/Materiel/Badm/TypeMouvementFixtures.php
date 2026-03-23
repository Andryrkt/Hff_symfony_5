<?php

namespace App\DataFixtures\Hf\Materiel\Badm;

use Doctrine\Persistence\ObjectManager;
use Doctrine\Bundle\FixturesBundle\Fixture;
use App\Entity\Hf\Materiel\Badm\TypeMouvement;

class TypeMouvementFixtures extends Fixture
{
    public function load(ObjectManager $manager)
    {
        $typeMouvements = [
            ['code' => 'ENP', 'description' => 'ENTREE EN PARC'],
            ['code' => 'CAS', 'description' => 'CHANGEMENT AGENCE/SERVICE'],
            ['code' => 'CCA', 'description' => 'CHANGEMENT DE CASIER'],
            ['code' => 'CEA', 'description' => 'CESSION D\'ACTIF'],
            ['code' => 'MRE', 'description' => 'MISE AU REBUT'],
        ];

        foreach ($typeMouvements as $typeMouvementData) {
            $typeMouvement = new TypeMouvement();
            $typeMouvement->setCodeMouvement($typeMouvementData['code']);
            $typeMouvement->setDescription($typeMouvementData['description']);
            $manager->persist($typeMouvement);
            $this->addReference('type_mvt_' . $typeMouvementData['description'], $typeMouvement);
        }
        $manager->flush();
    }
}