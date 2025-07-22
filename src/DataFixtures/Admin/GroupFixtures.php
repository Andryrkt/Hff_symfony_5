<?php

namespace App\DataFixtures\Admin;

use App\Entity\Admin\ApplicationGroupe\Group;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class GroupFixtures extends Fixture
{
    public function load(ObjectManager $manager)
    {
        // Groupe RH
        $groupRh = new Group();
        $groupRh->setName('RH')->setDescription('Groupe Ressources Humaines');
        $manager->persist($groupRh);

        // Groupe Magasin
        $groupMagasin = new Group();
        $groupMagasin->setName('MAGASIN')->setDescription('Groupe Magasin');
        $manager->persist($groupMagasin);

        // Groupe Atelier
        $groupAtelier = new Group();
        $groupAtelier->setName('ATELIER')->setDescription('Groupe Atelier');
        $manager->persist($groupAtelier);

        // Groupe Appro
        $groupAppro = new Group();
        $groupAppro->setName('APPRO')->setDescription('Groupe Appro');
        $manager->persist($groupAppro);

        // Groupe Rentale
        $groupRentale = new Group();
        $groupRentale->setName('RENTAL')->setDescription('Groupe Rentale');
        $manager->persist($groupRentale);

        // Groupe Energie
        $groupEnergie = new Group();
        $groupEnergie->setName('ENERGIE')->setDescription('Groupe Energie');
        $manager->persist($groupEnergie);

        // Ajout de références pour d'autres fixtures
        $this->addReference('group_rh', $groupRh);
        $this->addReference('group_magasin', $groupMagasin);
        $this->addReference('group_atelier', $groupAtelier);
        $this->addReference('group_appro', $groupAppro);
        $this->addReference('group_rentale', $groupRentale);
        $this->addReference('group_energie', $groupEnergie);

        $manager->flush();
    }
}
