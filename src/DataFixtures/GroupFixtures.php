<?php

namespace App\DataFixtures;

use App\Entity\Admin\ApplicationGroupe\Group;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class GroupFixtures extends Fixture
{
    public function load(ObjectManager $manager)
    {
        $groupRh = new Group();
        $groupRh->setName('RH')->setDescription('Groupe Ressources Humaines');
        $manager->persist($groupRh);

        $groupGestion = new Group();
        $groupGestion->setName('GESTION')->setDescription('Groupe Gestion');
        $manager->persist($groupGestion);

        // Ajout de références pour d'autres fixtures
        $this->addReference('group_rh', $groupRh);
        $this->addReference('group_gestion', $groupGestion);

        $manager->flush();
    }
} 