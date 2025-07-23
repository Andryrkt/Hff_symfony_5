<?php

namespace App\DataFixtures\Admin;

use App\Entity\Admin\AgenceService\Service;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class ServiceFixtures extends Fixture
{
    public function load(ObjectManager $manager): void
    {
        $services = [
            ['code' => 'NEG', 'nom' => 'MAGASIN'],
            ['code' => 'COM', 'nom' => 'COMMERCIAL'],
            ['code' => 'ATE', 'nom' => 'ATELIER'],
            ['code' => 'CSP', 'nom' => 'CUSTOMER SUPPORT'],
            ['code' => 'GAR', 'nom' => 'GARANTIE'],
            ['code' => 'FOR', 'nom' => 'FORMATION'],
            ['code' => 'ASS', 'nom' => 'ASSURANCE'],
            ['code' => 'MAN', 'nom' => 'ENERGIE MAN'],
            ['code' => 'LCD', 'nom' => 'LOCATION'],
            ['code' => 'DIR', 'nom' => 'DIRECTION GENERALE'],
            ['code' => 'FIN', 'nom' => 'FINANCE'],
            ['code' => 'PER', 'nom' => 'PERSONNEL ET SECURITE'],
            ['code' => 'INF', 'nom' => 'INFORMATIQUE'],
            ['code' => 'IMM', 'nom' => 'IMMOBILIER'],
            ['code' => 'TRA', 'nom' => 'TRANSIT'],
            ['code' => 'APP', 'nom' => 'APPRO'],
            ['code' => 'UMP', 'nom' => 'UNITE METHODE ET PROCEDURES'],
            ['code' => 'ENG', 'nom' => 'ENGINEERIE ET REALISATIONS'],
            ['code' => 'VAN', 'nom' => 'VANILLE'],
            ['code' => 'GIR', 'nom' => 'GIROFLE'],
            ['code' => 'THO', 'nom' => 'THOMSON'],
            ['code' => 'TSI', 'nom' => 'TSIAZOMPANIRY'],
            ['code' => 'LTV', 'nom' => 'LOCATION TAMATAVE'],
            ['code' => 'LFD', 'nom' => 'LOCATION FORT DAUPHINE'],
            ['code' => 'LBV', 'nom' => 'LOCATION MORAMANGA'],
            ['code' => 'MAH', 'nom' => 'MAHAJANGA'],
            ['code' => 'NOS', 'nom' => 'NOSY BE'],
            ['code' => 'TUL', 'nom' => 'TOLIARA'],
            ['code' => 'AMB', 'nom' => 'AMBOHIMANAMBOLA'],
            ['code' => 'FLE', 'nom' => 'FLEXIBLE'],
            ['code' => 'TSD', 'nom' => 'TSIROANOMANDIDY'],
            ['code' => 'VAT', 'nom' => 'VATOMANDRY'],
            ['code' => 'BLK', 'nom' => 'BELOBABA'],
            ['code' => 'MAS', 'nom' => 'MOBILE ASSETS'],
            ['code' => 'MAP', 'nom' => 'MARCHE PUBLIC'],
            ['code' => 'ADM', 'nom' => 'ADMINISTRATION'],
            ['code' => 'LEV', 'nom' => 'LEVAGE DM'],
            ['code' => 'LR6', 'nom' => 'LOCATION RN6'],
            ['code' => 'LST', 'nom' => 'LOCATION STAR'],
            ['code' => 'LCJ', 'nom' => 'LOCATION CENTRALE JIRAMA'],
            ['code' => 'SLR', 'nom' => 'SOLAIRE'],
            ['code' => 'LGR', 'nom' => 'LOCATION GROUPES'],
            ['code' => 'LSC', 'nom' => 'LOCATION SAMCRETTE'],
            ['code' => 'C1', 'nom' => 'TRAVEL AIRWAYS'],
        ];

        foreach ($services as $serviceData) {
            $service = new Service();
            $service->setCode($serviceData['code'])
                ->setNom($serviceData['nom']);

            $manager->persist($service);

            // Génération automatique de la référence
            $referenceKey = 'service_' . strtolower($serviceData['code']);
            $this->addReference($referenceKey, $service);
        }

        $manager->flush();
    }
}
