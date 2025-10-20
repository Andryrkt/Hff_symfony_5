<?php

namespace App\DataFixtures\dom;

use App\Entity\Dom\Site;
use Doctrine\Persistence\ObjectManager;
use Doctrine\Bundle\FixturesBundle\Fixture;

class SiteFixtures extends Fixture
{
    public function load(ObjectManager $manager)
    {
        $sites = [
            ['nomZone' => 'AUTRES VILLES', 'reference' => 'dom_site_autres_villes'],
            ['nomZone' => 'HORS TANA MOINS DE 24H', 'reference' => 'dom_site_hors_tana_moins_de_24h'],
            ['nomZone' => 'ZONES ENCLAVEES', 'reference' => 'dom_site_zone_enclavees'],
            ['nomZone' => 'ZONES TOURISTIQUES', 'reference' => 'dom_site_zones_touristiques'],
            ['nomZone' => 'FORT-DAUPHIN', 'reference' => 'dom_site_fort_dauphin'],
            ['nomZone' => 'AUTRES SITE ENCLAVES', 'reference' => 'dom_site_autres_site_enclaves'],
            ['nomZone' => 'HORS TANA', 'reference' => 'dom_site_hors_tana'],
            ['nomZone' => 'TANA', 'reference' => 'dom_site_tana']
        ];

        foreach ($sites as $siteData) {
            $site = new Site();
            $site->setNomZone($siteData['nomZone']);

            $manager->persist($site);
            $this->addReference($siteData['reference'], $site);
        }

        $manager->flush();
    }
}
