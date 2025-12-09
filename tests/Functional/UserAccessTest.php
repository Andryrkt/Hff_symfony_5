<?php

namespace App\Tests\Functional;

use App\Tests\BaseTestCase;
use App\DataFixtures\Admin\PersonnelUser\UserFixtures;

class UserAccessTest extends BaseTestCase
{
    private $referenceRepository;

    protected function setUp(): void
    {
        parent::setUp();

        // DEBUG: Affichez l'erreur réelle
        try {
            $this->referenceRepository = $this->loadTestFixtures([
                UserFixtures::class
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

        // Authenticate the user
        $this->client->loginUser($user);

        $this->client->request('GET', '/');
        $this->assertResponseIsSuccessful();
    }

    // Note: testUserCannotAccessAdminPage removed because /admin route doesn't exist
    // If you need to test access control, create a route with ROLE_ADMIN requirement first
}
