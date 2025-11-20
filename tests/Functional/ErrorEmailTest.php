<?php

namespace App\Tests\Functional;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\Mime\Email;
use Symfony\Component\VarDumper\VarDumper;

class ErrorEmailTest extends WebTestCase
{
    public function testErrorEmailIsSent(): void
    {
        // Crée un client HTTP pour simuler des requêtes
        $client = static::createClient();

        // Active le profileur pour accéder aux services du Mailer
        $client->enableProfiler();

        // Fait une requête à la route qui provoque une erreur
        $client->request('GET', '/test/error');

        // Vérifie que la page d'erreur est affichée (statut 500)
        $this->assertResponseStatusCodeSame(500);

        // Récupère le profileur
        $profile = $client->getProfile();
        $this->assertNotNull($profile, 'Profiler should be available.');

        // Récupère l'enregistreur de données Mailer
        $mailerDataCollector = $profile->getCollector('mailer');
        $this->assertNotNull($mailerDataCollector, 'Mailer data collector should be available.');

        // Vérifie qu'au moins un e-mail a été envoyé
        $collectedMessages = $mailerDataCollector->getEvents()->getMessages();
        $this->assertGreaterThan(0, count($collectedMessages), 'Au moins un e-mail devrait avoir été envoyé.');

        // On s'attend à ce que le premier message soit notre e-mail d'erreur
        /** @var \Symfony\Component\Mime\Email $email */
        $email = $collectedMessages[0]->getMessage();

        // Vérifie le destinataire
        $expectedAdminEmail = $_ENV['ADMIN_EMAIL'] ?? 'votre.email@example.com';
        $this->assertContains($expectedAdminEmail, $email->getTo()[0]->getAddress(), 'L\'e-mail devrait être envoyé à l\'adresse ADMIN_EMAIL.');

        // Vérifie le sujet de l'e-mail
        $this->assertStringContainsString('HFF - Erreur Application', $email->getSubject(), 'Le sujet de l\'e-mail devrait contenir "HFF - Erreur Application".');

        // Vérifie que le corps de l'e-mail contient le message d'erreur
        $this->assertStringContainsString('Ceci est une erreur de test simulée', $email->getHtmlBody(), 'Le corps de l'e-mail devrait contenir le message d'erreur simulée.');

        // Vérifie le destinataire
        $expectedAdminEmail = $_ENV['ADMIN_EMAIL'] ?? 'votre.email@example.com';
        $this->assertContains($expectedAdminEmail, $email->getTo(), 'L\'e-mail devrait être envoyé à l\'adresse ADMIN_EMAIL.');

        // Vérifie le sujet de l'e-mail
        $this->assertStringContainsString('HFF - Erreur Application', $email->getSubject(), 'Le sujet de l\'e-mail devrait contenir "HFF - Erreur Application".');

        // Vérifie que le corps de l'e-mail contient le message d'erreur
        $this->assertStringContainsString('Ceci est une erreur de test simulée', $email->getHtmlBody(), 'Le corps de l\'e-mail devrait contenir le message d\'erreur simulée.');
    }
}
