<?php

namespace App\DataFixtures\Admin;

use App\Entity\Admin\ApplicationGroupe\Application;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class ApplicationFixtures extends Fixture
{
    public function load(ObjectManager $manager): void
    {
        $application = new Application();
        $application->setName('DEMANDE D\'ORDRE DE MISSION');
        $application->setCode('DOM');
        $manager->persist($application);
    }
}
