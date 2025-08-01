<?php

namespace App\DataFixtures\Admin;

use App\Entity\Admin\ApplicationGroupe\Application;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class ApplicationFixtures extends Fixture
{
    public function load(ObjectManager $manager): void
    {
        $dom = new Application();
        $dom->setName('DEMANDE D\'ORDRE DE MISSION');
        $dom->setCode('DOM');
        $this->addReference('app_dom', $dom);
        $manager->persist($dom);
        $manager->flush();
    }
}
