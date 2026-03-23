<?php

namespace App\Tests\Integration\Security;

use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Component\Ldap\Ldap;

class LdapConnectionTest extends KernelTestCase
{
    protected function setUp(): void
    {
        // On s'assure que le kernel démarre pour charger les variables d'environnement
        self::bootKernel();
    }

    public function testLdapConnectionAndBind(): void
    {
        if (!extension_loaded('ldap')) {
            $this->markTestSkipped('L\'extension LDAP n\'est pas activée.');
        }

        try {
            $ldap = Ldap::create('ext_ldap', [
                'host' => $_ENV['LDAP_HOST'],
                'port' => $_ENV['LDAP_PORT'],
                'encryption' => $_ENV['LDAP_ENCRYPTION'],
                'options' => [
                    'protocol_version' => 3,
                    'referrals' => false,
                ],
            ]);

            // 3. Tenter le BIND (Authentification)
            $ldap->bind($_ENV['LDAP_BIND_DN'], $_ENV['LDAP_BIND_PASSWORD']);

            $this->assertTrue(true, 'La connexion et le bind LDAP ont réussi.');
        } catch (\Exception $e) {
            $this->fail('Erreur de connexion LDAP : ' . $e->getMessage());
        }
    }

    public function testLdapSearch(): void
    {
        if (!extension_loaded('ldap')) {
            $this->markTestSkipped('L\'extension LDAP n\'est pas activée.');
        }

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
            $ldap->bind($_ENV['LDAP_BIND_DN'], $_ENV['LDAP_BIND_PASSWORD']);

            $filter = '(sAMAccountName=lanto)';

            $query = $ldap->query($_ENV['LDAP_BASE_DN'], $filter);
            $results = $query->execute();

            $entries = $results->toArray();

            $this->assertGreaterThanOrEqual(0, count($entries), 'La requete LDAP devrait s\'exécuter sans erreur.');
        } catch (\Exception $e) {
            $this->fail('Erreur lors de la recherche LDAP : ' . $e->getMessage());
        }
    }
}
