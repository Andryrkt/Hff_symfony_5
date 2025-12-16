<?php

namespace App\Tests\Security;

use PHPUnit\Framework\TestCase;
use Symfony\Component\Ldap\Entry;
use App\Security\LdapUserProvider;
use App\Security\LdapAuthenticator;
use Symfony\Component\Ldap\LdapInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\RouterInterface;
use Symfony\Component\Ldap\Adapter\ExtLdap\Query;
use Symfony\Component\Security\Core\Exception\AuthenticationException;


class LdapAuthenticatorTest extends TestCase
{
    private const TEST_DN = 'cn=testuser,dc=example,dc=com';
    private const ADMIN_DN = 'CN=Lanto ANDRIANADISON,OU=Informatique,OU=HFF Tana,OU=HFF Users,DC=fraise,DC=hff,DC=mg';
    private const ADMIN_PASSWORD = 'Hasina#2025-3';

    private $ldap;
    private $router;
    private $userProvider;
    private $authenticator;

    protected function setUp(): void
    {
        $this->ldap = $this->createMock(LdapInterface::class);
        $this->router = $this->createMock(RouterInterface::class);
        $this->userProvider = $this->createMock(LdapUserProvider::class);

        $this->authenticator = new LdapAuthenticator(
            $this->ldap,
            $this->router,
            $this->userProvider,
            self::ADMIN_DN,
            self::ADMIN_PASSWORD
        );
    }

    /**
     * test pour une vrai URL (/login)
     *
     * @return void
     */
    public function testSupportsReturnsTrueForLoginPostRequest(): void
    {
        $request = new Request([], [], [], [], [], ['REQUEST_METHOD' => 'POST']);
        $request->server->set('REQUEST_URI', '/login');

        $this->assertTrue($this->authenticator->supports($request));
    }

    /**
     * teste si une faux URL (/other) est ajouter
     *
     * @return void
     */
    public function testSupportsReturnsFalseForNonLoginRequest(): void
    {
        $request = new Request([], [], [], [], [], ['REQUEST_METHOD' => 'POST']);
        $request->server->set('REQUEST_URI', '/other');

        $this->assertFalse($this->authenticator->supports($request));
    }

    /**
     * test si une requet GET est evoyer pendant la soumission
     *
     * @return void
     */
    public function testSupportsReturnsFalseForGetRequest(): void
    {
        $request = new Request([], [], [], [], [], ['REQUEST_METHOD' => 'GET']);
        $request->server->set('REQUEST_URI', '/login');

        $this->assertFalse($this->authenticator->supports($request));
    }

    /**
     * test si le nom d'utilisateur et le mot de passe sont vide
     *
     * @return void
     */
    public function testAuthenticateWithEmptyCredentialsThrowsException(): void
    {
        $request = new Request();
        $request->request->set('_username', '');
        $request->request->set('_password', '');

        $this->expectException(AuthenticationException::class);
        $this->expectExceptionMessage('Nom d\'utilisateur et mot de passe requis.');

        $this->authenticator->authenticate($request);
    }

    // public function testAuthenticateWithValidCredentials(): void
    // {
    //     $request = new Request();
    //     $request->request->set('_username', 'testuser');
    //     $request->request->set('_password', 'password');

    //     $query = $this->createMock(Query::class);
    //     $entry = $this->createMock(Entry::class);
    //     $entry->method('getDn')->willReturn('cn=testuser,dc=example,dc=com');

    //     $this->ldap->expects($this->once())
    //         ->method('bind')
    //         ->with('cn=admin,dc=example,dc=com', 'password');

    //     $this->ldap->expects($this->once())
    //         ->method('query')
    //         ->with('dc=example,dc=com', '(sAMAccountName=testuser)')
    //         ->willReturn($query);

    //     $query->expects($this->once())
    //         ->method('execute')
    //         ->willReturn([$entry]);

    //     $this->ldap->expects($this->once())
    //         ->method('bind')
    //         ->with('cn=testuser,dc=example,dc=com', 'password');

    //     $this->userProvider->expects($this->once())
    //         ->method('loadUserByIdentifier')
    //         ->with('testuser');

    //     $passport = $this->authenticator->authenticate($request);
    //     $this->assertNotNull($passport);
    // }
}
