<?php

namespace App\Tests\Integration\Security;

use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Component\Ldap\Ldap;

class LdapAuthenticationTest extends KernelTestCase
{
    /**
     * Test qu'un utilisateur peut s'authentifier avec LDAP
     * Ce test vérifie uniquement l'authentification LDAP, pas la création de session
     */
    public function testUserCanAuthenticateWithLdap(): void
    {
        if (!extension_loaded('ldap')) {
            $this->markTestSkipped('L\'extension LDAP n\'est pas activée.');
        }

        self::bootKernel();

        $ldap = Ldap::create('ext_ldap', [
            'host' => $_ENV['LDAP_HOST'],
            'port' => $_ENV['LDAP_PORT'],
            'encryption' => $_ENV['LDAP_ENCRYPTION'],
            'options' => [
                'protocol_version' => 3,
                'referrals' => false,
            ],
        ]);

        try {
            // 1. Bind avec le compte technique pour rechercher l'utilisateur
            $ldap->bind($_ENV['LDAP_BIND_DN'], $_ENV['LDAP_BIND_PASSWORD']);

            // 2. Rechercher l'utilisateur 'lanto'
            $query = $ldap->query($_ENV['LDAP_BASE_DN'], '(sAMAccountName=lanto)');
            $results = $query->execute();

            $this->assertGreaterThan(0, count($results), 'L\'utilisateur lanto devrait être trouvé dans LDAP');

            $userDn = $results[0]->getDn();

            // 3. Tenter de s'authentifier avec le mot de passe de l'utilisateur
            $ldap->bind($userDn, 'Hasina#2025-4');

            // Si on arrive ici, l'authentification a réussi
            $this->assertTrue(true, 'L\'utilisateur lanto peut s\'authentifier avec le mot de passe fourni');
        } catch (\Exception $e) {
            $this->fail('Erreur d\'authentification LDAP : ' . $e->getMessage());
        }
    }

    /**
     * Test qu'un mauvais mot de passe est rejeté
     */
    public function testInvalidPasswordIsRejected(): void
    {
        if (!extension_loaded('ldap')) {
            $this->markTestSkipped('L\'extension LDAP n\'est pas activée.');
        }

        self::bootKernel();

        $ldap = Ldap::create('ext_ldap', [
            'host' => $_ENV['LDAP_HOST'],
            'port' => $_ENV['LDAP_PORT'],
            'encryption' => $_ENV['LDAP_ENCRYPTION'],
            'options' => [
                'protocol_version' => 3,
                'referrals' => false,
            ],
        ]);

        $ldap->bind($_ENV['LDAP_BIND_DN'], $_ENV['LDAP_BIND_PASSWORD']);

        $query = $ldap->query($_ENV['LDAP_BASE_DN'], '(sAMAccountName=lanto)');
        $results = $query->execute();

        if (count($results) === 0) {
            $this->markTestSkipped('Utilisateur lanto non trouvé');
        }

        $userDn = $results[0]->getDn();

        // Tenter avec un mauvais mot de passe - cela devrait lever une exception
        $exceptionThrown = false;
        try {
            $ldap->bind($userDn, 'WrongPassword123');
        } catch (\Exception $e) {
            $exceptionThrown = true;
            $this->assertStringContainsString('Invalid credentials', $e->getMessage());
        }

        $this->assertTrue($exceptionThrown, 'Une exception devrait être levée pour un mauvais mot de passe');
    }
}
