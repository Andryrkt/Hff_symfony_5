<?php

namespace App\Tests\Functional\Controller\Rh\Dom\Creation;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

/**
 * Test de diagnostic simple pour déboguer les problèmes de DomFirstController
 */
class DomFirstControllerDebugTest extends WebTestCase
{
    /**
     * Test 1: Vérifier que la page de login est accessible
     */
    public function testLoginPageIsAccessible(): void
    {
        $client = static::createClient();
        $client->request('GET', '/login');

        $response = $client->getResponse();

        echo "\n=== TEST LOGIN PAGE ===\n";
        echo "Status Code: " . $response->getStatusCode() . "\n";
        echo "Is Successful: " . ($response->isSuccessful() ? 'YES' : 'NO') . "\n";

        $this->assertResponseIsSuccessful('La page de login devrait être accessible');
    }

    /**
     * Test 2: Vérifier la redirection vers login pour utilisateur anonyme
     */
    public function testAnonymousUserRedirectsToLogin(): void
    {
        $client = static::createClient();
        $client->request('GET', '/rh/ordre-de-mission/dom-first-form');

        $response = $client->getResponse();

        echo "\n=== TEST ANONYMOUS ACCESS ===\n";
        echo "Status Code: " . $response->getStatusCode() . "\n";
        echo "Is Redirect: " . ($response->isRedirect() ? 'YES' : 'NO') . "\n";

        if ($response->isRedirect()) {
            echo "Redirect Location: " . $response->headers->get('Location') . "\n";
        } else {
            echo "Response Content (first 500 chars):\n";
            echo substr($response->getContent(), 0, 500) . "\n";

            // Afficher l'exception si disponible
            if ($response->headers->has('X-Debug-Exception')) {
                echo "\nException Class: " . $response->headers->get('X-Debug-Exception') . "\n";
                echo "Exception File: " . $response->headers->get('X-Debug-Exception-File') . "\n";
            }
        }

        // On vérifie juste que ce n'est pas une erreur 500
        $this->assertNotEquals(
            500,
            $response->getStatusCode(),
            'Ne devrait pas retourner une erreur 500. Vérifiez la configuration du firewall/authenticator.'
        );
    }

    /**
     * Test 3: Vérifier que la route existe
     */
    public function testRouteExists(): void
    {
        $client = static::createClient();
        $router = $client->getContainer()->get('router');

        try {
            $route = $router->getRouteCollection()->get('app_rh_dom_first_form');

            echo "\n=== TEST ROUTE ===\n";
            if ($route) {
                echo "Route trouvée: app_rh_dom_first_form\n";
                echo "Path: " . $route->getPath() . "\n";
                echo "Methods: " . implode(', ', $route->getMethods() ?: ['ANY']) . "\n";
            } else {
                echo "Route 'app_rh_dom_first_form' non trouvée\n";
                echo "Essai avec le path direct...\n";

                $match = $router->match('/rh/ordre-de-mission/dom-first-form');
                echo "Match trouvé: " . print_r($match, true) . "\n";
            }

            $this->assertTrue(true);
        } catch (\Exception $e) {
            echo "Erreur: " . $e->getMessage() . "\n";
            $this->fail('Erreur lors de la vérification de la route: ' . $e->getMessage());
        }
    }

    /**
     * Test 4: Vérifier la connexion à la base de données
     */
    public function testDatabaseConnection(): void
    {
        $client = static::createClient();

        try {
            $em = $client->getContainer()->get('doctrine')->getManager();
            $connection = $em->getConnection();

            // Test simple de connexion
            $result = $connection->executeQuery('SELECT 1')->fetchOne();

            echo "\n=== TEST DATABASE ===\n";
            echo "Database URL: " . $_ENV['DATABASE_URL'] . "\n";
            echo "Connection: OK\n";
            echo "Test query result: " . $result . "\n";

            $this->assertEquals(1, $result);
        } catch (\Exception $e) {
            echo "\n=== DATABASE ERROR ===\n";
            echo "Error: " . $e->getMessage() . "\n";
            $this->fail('Erreur de connexion à la base de données: ' . $e->getMessage());
        }
    }
}
