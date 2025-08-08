<?php

namespace App\Tests\Controller\Api;

use ApiPlatform\Core\Bridge\Symfony\Bundle\Test\ApiTestCase;
use App\Entity\Admin\AgenceService\Agence;
use App\Entity\Admin\AgenceService\Service;

class AgenceApiTest extends ApiTestCase
{
    public function testGetAgenceServices(): void
    {
        // (Optionnel) Créez des données de test si votre base de données de test est vide
        $service = new Service();
        $service->setCode('SERV1')->setNom('Test Service');

        $agence = new Agence();
        $agence->setCode('AG1')->setNom('Test Agence');
        $agence->addService($service);

        $em = self::getContainer()->get('doctrine')->getManager();
        $em->persist($service);
        $em->persist($agence);
        $em->flush();

        // Récupère l'ID de l'agence créée
        $agenceId = $agence->getId();

        // Faites une requête à votre nouvelle route
        static::createClient()->request('GET', '/api/agences/' . $agenceId . '/services');

        // Vérifiez que la réponse a un statut 200 (OK)
        $this->assertResponseIsSuccessful();

        // Vérifiez que la réponse est bien en JSON
        $this->assertResponseHeaderSame('content-type', 'application/ld+json; charset=utf-8');

        // Vérifiez que le JSON contient les données attendues
        $this->assertJsonContains('{
            "@context": "/api/contexts/Service",
            "@id": "/api/agences/' . $agenceId . '/services",
            "@type": "hydra:Collection",
            "hydra:member": [
                {
                    "@type": "Service",
                    "code": "SERV1",
                    "nom": "Test Service"
                }
            ]
        }');
    }
}
