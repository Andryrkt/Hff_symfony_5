<?php

namespace App\DataFixtures\Admin;

use App\Entity\Admin\ApplicationGroupe\Group;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class GroupFixtures extends Fixture
{
    public function load(ObjectManager $manager): void
    {
        $groups = [
            ['name' => 'RH', 'description' => 'Groupe Ressources Humaines'],
            ['name' => 'MAGASIN', 'description' => 'Groupe Magasin'],
            ['name' => 'ATELIER', 'description' => 'Groupe Atelier'],
            ['name' => 'APPRO', 'description' => 'Groupe Approvisionnement'],
            ['name' => 'RENTAL', 'description' => 'Groupe Location'],
            ['name' => 'ENERGIE', 'description' => 'Groupe Energie'],
            // Vous pouvez ajouter d'autres groupes ici selon le même format
        ];

        foreach ($groups as $groupData) {
            $group = new Group();
            $group->setName($groupData['name'])
                ->setDescription($groupData['description']);

            $manager->persist($group);

            // Génération automatique de la référence
            $referenceKey = 'group_' . strtolower($groupData['name']);
            $this->addReference($referenceKey, $group);
        }

        $manager->flush();
    }
}
