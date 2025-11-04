<?php

namespace App\DataFixtures\Rh\dom;


use App\Entity\Rh\Dom\Rmq;
use Doctrine\Persistence\ObjectManager;
use Doctrine\Bundle\FixturesBundle\Fixture;

class RmqFixtures extends Fixture
{
    public function load(ObjectManager $manager)
    {
        $rmqs = [
            ['description' => 'STD', 'reference' => 'dom_rmq_std'],
            ['description' => '50', 'reference' => 'dom_rmq_50']
        ];

        foreach ($rmqs as $rmqData) {
            $rmq = new Rmq();
            $rmq->setDescription($rmqData['description']);

            $manager->persist($rmq);
            $this->addReference($rmqData['reference'], $rmq);
        }

        $manager->flush();
    }
}
