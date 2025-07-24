<?php

namespace App\DataFixtures\dom;

use App\Entity\Dom\DomRmq;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class RmqFixtures extends Fixture
{
    public function load(ObjectManager $manager)
    {
        $rmqs = [
            ['description' => 'STD', 'reference' => 'dom_rmq_std'],
            ['description' => '50', 'reference' => 'dom_rmq_50']
        ];

        foreach ($rmqs as $rmqData) {
            $rmq = new DomRmq();
            $rmq->setDescription($rmqData['description']);

            $manager->persist($rmq);
            $this->addReference($rmqData['reference'], $rmq);
        }

        $manager->flush();
    }
}
