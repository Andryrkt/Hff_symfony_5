<?php

namespace App\Tests\Functional;

use App\DataFixtures\Test\TestUserFixtures;
use App\Tests\BaseTestCase;
use Symfony\Component\BrowserKit\Cookie;
use Symfony\Component\Security\Core\Authentication\Token\UsernamePasswordToken;

class UserAccessTest extends BaseTestCase
{
    private $referenceRepository;

    protected function setUp(): void
    {
        parent::setUp();

        // DEBUG: Affichez l'erreur réelle
        try {
            $this->referenceRepository = $this->loadTestFixtures([
                TestUserFixtures::class
            ])->getReferenceRepository();
        } catch (\Exception $e) {
            // Affiche l'erreur complète
            echo "\n=== ERREUR FIXTURE ===\n";
            echo "Message: " . $e->getMessage() . "\n";
            echo "File: " . $e->getFile() . ":" . $e->getLine() . "\n";
            echo "Trace: " . $e->getTraceAsString() . "\n";
            echo "=== FIN ERREUR ===\n";

            throw $e; // Relance l'exception pour voir le test échouer
        }
    }

    public function testUserCanAccessHomepage(): void
    {
        $user = $this->referenceRepository->getReference('user_u1');

        $this->client->request('GET', '/');
        $this->assertResponseIsSuccessful();
    }

    public function testUserCannotAccessAdminPage(): void
    {
        $user = $this->referenceRepository->getReference('user_u1');

        $this->client->request('GET', '/admin');
        $this->assertTrue(
            $this->client->getResponse()->isForbidden() ||
                $this->client->getResponse()->isRedirect()
        );
    }


}
