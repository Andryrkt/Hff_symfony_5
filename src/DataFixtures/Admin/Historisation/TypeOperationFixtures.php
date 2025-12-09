<?php

namespace App\DataFixtures\Admin\Historisation;

use App\Entity\Admin\Historisation\TypeOperation;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class TypeOperationFixtures extends Fixture
{
    public function load(ObjectManager $manager): void
    {
        $typeOperations = [
            ['type' => 'SOUMISSION', 'reference' => 'type_soumission'],
            ['type' => 'VALIDATION', 'reference' => 'type_validation'],
            ['type' => 'MODIFICATION', 'reference' => 'type_modification'],
            ['type' => 'SUPPRESSION', 'reference' => 'type_suppression'],
            ['type' => 'CREATION', 'reference' => 'type_creation'],
            ['type' => 'CLOTURE', 'reference' => 'type_cloture'],
            ['type' => 'FILE_MERGE', 'reference' => 'type_file_merge'],
            ['type' => 'DB_SAVE', 'reference' => 'type_db_save'],
            ['type' => 'DW_COPY', 'reference' => 'type_dw_copy'],
            ['type' => 'FILE_UPLOAD', 'reference' => 'type_file_upload'],

        ];

        foreach ($typeOperations as $typeOperationData) {
            $typeOperation = new TypeOperation();
            $typeOperation->setTypeOperation($typeOperationData['type']);
            $manager->persist($typeOperation);
            $this->addReference($typeOperationData['reference'], $typeOperation);
        }

        $manager->flush();
    }
}
