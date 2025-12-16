<?php

namespace App\Tests\Functional\Security;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class LoginTest extends WebTestCase
{
    /**
     * Test qu'un utilisateur peut se connecter avec des identifiants LDAP valides
     */
    public function testUserCanLoginWithValidLdapCredentials(): void
    {
        $client = static::createClient();

        // 1. Accéder à la page de login
        $crawler = $client->request('GET', '/login');

        $this->assertResponseIsSuccessful();
        $this->assertSelectorExists('form');

        // 2. Remplir le formulaire de connexion
        $form = $crawler->selectButton('Se connecter')->form([
            '_username' => 'lanto',
            '_password' => 'Hasina#2025-4',
        ]);

        // 3. Soumettre le formulaire
        $client->submit($form);

        // 4. Vérifier la redirection vers la page d'accueil
        $this->assertResponseRedirects('/');

        // 5. Suivre la redirection
        $client->followRedirect();

        // 6. Vérifier qu'on est bien connecté (page d'accueil accessible)
        $this->assertResponseIsSuccessful();
    }

    /**
     * Test qu'un utilisateur ne peut pas se connecter avec un mot de passe invalide
     */
    public function testUserCannotLoginWithInvalidPassword(): void
    {
        $client = static::createClient();

        $crawler = $client->request('GET', '/login');

        // Soumettre avec un mauvais mot de passe
        $form = $crawler->selectButton('Se connecter')->form([
            '_username' => 'lanto',
            '_password' => 'WrongPassword123',
        ]);

        $client->submit($form);

        // Devrait rester sur /login avec une erreur
        $this->assertResponseRedirects('/login');

        $crawler = $client->followRedirect();

        // Vérifier qu'un message d'erreur est affiché
        $this->assertSelectorExists('.alert-danger');
    }
}
