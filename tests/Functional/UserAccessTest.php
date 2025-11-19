<?php

namespace App\Tests\Functional;

use App\DataFixtures\Test\TestUserFixtures;
use Liip\TestFixturesBundle\Test\FixturesTrait;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\BrowserKit\Cookie;
use Symfony\Component\Security\Core\Authentication\Token\UsernamePasswordToken;

class UserAccessTest extends WebTestCase
{
    use FixturesTrait;

    protected function setUp(): void
    {
        // Charge les fixtures avant chaque test
        $this->loadFixtures([TestUserFixtures::class]);
    }

    public function testUserCanAccessHomepage(): void
    {
        $client = static::createClient();

        // Récupère l'utilisateur créé par la fixture
        // Assurez-vous que votre fixture ajoute bien une référence pour l'utilisateur 'test_u1'
        $user = $this->getReference('user_u1');

        // Simule la connexion de l'utilisateur
        $this->logIn($client, $user);

        // Fait une requête à la page d'accueil
        $client->request('GET', '/');

        // Vérifie que la page est accessible (code 200)
        $this->assertResponseIsSuccessful();

        // Vérifie que le nom d'utilisateur est affiché sur la page (exemple)
        // Vous devrez adapter cette assertion en fonction de votre template Twig
        $this->assertSelectorTextContains('body', $user->getUsername());
    }

    /**
     * Simule la connexion d'un utilisateur.
     */
    private function logIn($client, $user): void
    {
        $session = $client->getContainer()->get('session');

        // Pour Symfony 5.4, le token est généralement UsernamePasswordToken
        $firewallName = 'main'; // Remplacez par le nom de votre firewall si différent
        $firewallContext = 'main'; // Le contexte du firewall est souvent le même que le nom

        $token = new UsernamePasswordToken($user, $firewallName, $user->getRoles());
        $session->set('_security_'.$firewallContext, serialize($token));
        $session->save();

        $cookie = new Cookie($session->getName(), $session->getId());
        $client->getCookieJar()->set($cookie);
    }
}
