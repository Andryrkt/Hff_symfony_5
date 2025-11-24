<?php

namespace App\DataFixtures\Admin\Historisation;

use App\Entity\Admin\Historisation\TypeDocument;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class TypeDocumentFixtures extends Fixture
{
    public function load(ObjectManager $manager)
    {
        $typeDocuments = [
            ['type' => 'DIT', 'libelle' => 'DEMANDE INTERVENTION', 'reference' => 'type_dit'],
            ['type' => 'OR', 'libelle' => 'ORDRE DE REPARATION', 'reference' => 'type_or'],
            ['type' => 'FAC', 'libelle' => 'FACTURE', 'reference' => 'type_fac'],
            ['type' => 'RI', 'libelle' => 'RAPPORT INTERVENTION', 'reference' => 'type_ri'],
            ['type' => 'TIK', 'libelle' => 'DEMANDE DE SUPPORT INFORMATIQUE', 'reference' => 'type_tik'],
            ['type' => 'DA', 'libelle' => 'DEMANDE APPROVISIONNEMENT', 'reference' => 'type_da'],
            ['type' => 'DOM', 'libelle' => 'DEMANDE ORDRE DE MISSION', 'reference' => 'type_dom'],
            ['type' => 'BADM', 'libelle' => 'MOUVEMENT MATERIEL BADM', 'reference' => 'type_badm'],
            ['type' => 'CAS', 'libelle' => 'CASIER', 'reference' => 'type_cas'],
            ['type' => 'CDE', 'libelle' => 'COMMANDE', 'reference' => 'type_cde'],
            ['type' => 'DEV', 'libelle' => 'DEVIS', 'reference' => 'type_dev'],
            ['type' => 'BC', 'libelle' => 'BON DE COMMANDE', 'reference' => 'type_bc'],
            ['type' => 'AC', 'libelle' => 'ACCUSE DE RECEPTION', 'reference' => 'type_ac'],
            ['type' => 'CDEFRN', 'libelle' => 'COMMANDE FOURNISSEUR', 'reference' => 'type_cdefrn'],
            ['type' => 'SW', 'libelle' => 'SWIFT', 'reference' => 'type_sw'],
            ['type' => 'MUT', 'libelle' => 'DEMANDE DE MUTATION', 'reference' => 'type_mut']
        ];

        foreach ($typeDocuments as $typeDocumentData) {
            $typeDocument = new TypeDocument();
            $typeDocument->setTypeDocument($typeDocumentData['type'])
                ->setLibelleDocument($typeDocumentData['libelle'])
            ;
            $manager->persist($typeDocument);
            $this->addReference($typeDocumentData['reference'], $typeDocument);
        }

        $manager->flush();
    }
}
