<?php

namespace App\DataFixtures\Admin;

use App\Entity\Admin\ApplicationGroupe\Vignette;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class VignetteFixtures extends Fixture
{
    public function load(ObjectManager $manager)
    {

        $vignette = new Vignette();
        $vignette->setNom('Documentation');
        $vignette->setDescription('');
    }
}