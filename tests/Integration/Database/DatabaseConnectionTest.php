<?php

namespace App\Tests\Integration\Database;

use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Doctrine\DBAL\DriverManager;

class DatabaseConnectionTest extends KernelTestCase
{
    protected function setUp(): void
    {
        self::bootKernel();
    }

    public function testDatabaseConnection(): void
    {
        // On récupère l'URL de connexion spécifique pour les tests depuis .env.test
        $connectionUrl = $_ENV['TEST_DATABASE_URL'] ?? null;

        if (!$connectionUrl) {
            $this->markTestSkipped('La variable TEST_DATABASE_URL n\'est pas définie dans .env.test');
        }

        try {
            // Création de la connexion via DBAL sans passer par le service Doctrine de Symfony
            // pour tester spécifiquement cette URL
            $connection = DriverManager::getConnection([
                'url' => $connectionUrl,
            ]);

            $connection->connect();

            $this->assertTrue($connection->isConnected(), 'La connexion à la base de données a échoué.');

            // Test d'une requête simple
            $sql = 'SELECT 1';
            $stmt = $connection->prepare($sql);
            $result = $stmt->executeQuery();

            $this->assertNotNull($result->fetchOne(), 'La requête de test n\'a rien retourné.');
        } catch (\Exception $e) {
            $this->fail('Erreur de connexion BDD : ' . $e->getMessage());
        }
    }

    public function testCanQueryDatabase(): void
    {
        // Test inspiré de votre script test_db.php
        $connectionUrl = $_ENV['TEST_DATABASE_URL'] ?? null;

        if (!$connectionUrl) {
            $this->markTestSkipped('TEST_DATABASE_URL missing');
        }

        try {
            $connection = DriverManager::getConnection(['url' => $connectionUrl]);

            // Test simple: vérifier qu'on peut interroger une table
            // On compte juste le nombre d'enregistrements dans dom_sous_type_document
            $sql = 'SELECT COUNT(*) as total FROM dom_sous_type_document';
            $stmt = $connection->prepare($sql);
            $result = $stmt->executeQuery();
            $row = $result->fetchAssociative();

            $this->assertIsArray($row);
            $this->assertArrayHasKey('total', $row);
            $this->assertGreaterThanOrEqual(0, $row['total'], 'La table dom_sous_type_document devrait être accessible.');
        } catch (\Exception $e) {
            $this->fail('Erreur SQL : ' . $e->getMessage());
        }
    }
}
