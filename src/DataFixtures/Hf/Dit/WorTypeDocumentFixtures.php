<?php

namespace App\DataFixtures\Hf\Dit;

use Doctrine\Persistence\ObjectManager;
use Doctrine\Bundle\FixturesBundle\Fixture;
use App\Entity\Hf\Atelier\Dit\WorTypeDocument;

class WorTypeDocumentFixtures extends Fixture
{
    public function load(ObjectManager $manager)
    {
        $worTypeDocuments = [
            ['code' => 'PAN', 'description' => 'PANNE'],
            ['code' => 'MAH', 'description' => 'MAINTENANCE HABITUELLE'],
            ['code' => 'GRM', 'description' => 'GROSSE MAINTENANCE'],
            ['code' => 'FOR', 'description' => 'FORMATION'],
            ['code' => 'MAP', 'description' => 'Maintenance préventive'],
            ['code' => 'MAC', 'description' => 'Maintenance curative'],
            ['code' => 'AUT', 'description' => 'Autres'],
        ];

        foreach ($worTypeDocuments as $worTypeDocumentData) {
            $worTypeDocument = new WorTypeDocument();
            $worTypeDocument->setCodeDocument($worTypeDocumentData['code']);
            $worTypeDocument->setDescription($worTypeDocumentData['description']);
            $manager->persist($worTypeDocument);
            $this->addReference('wor_type_document_' . $worTypeDocumentData['code'], $worTypeDocument);
        }
        $manager->flush();
    }
}
