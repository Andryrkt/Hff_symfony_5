<?php

namespace App\Tests\Security;

use App\Security\LdapAuthenticator;
use App\Security\LdapUserProvider;
use PHPUnit\Framework\TestCase;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Ldap\LdapInterface;
use Symfony\Component\Ldap\Query;
use Symfony\Component\Ldap\Entry;
use Symfony\Component\Routing\RouterInterface;
use Symfony\Component\Security\Core\Exception\AuthenticationException;
use Psr\Log\LoggerInterface;

class LdapAuthenticatorTest extends TestCase
{
    private $ldap;
    private $router;
    private $userProvider;
    private $logger;
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
            'CN=Lanto ANDRIANADISON,OU=Informatique,OU=HFF Tana,OU=HFF Users,DC=fraise,DC=hff,DC=mg',
            'Hasina#2025-2'
        );
    }

    public function testSupportsReturnsTrueForLoginPostRequest(): void
    {
        $request = new Request([], [], [], [], [], ['REQUEST_METHOD' => 'POST']);
        $request->server->set('REQUEST_URI', '/login');

        $this->assertTrue($this->authenticator->supports($request));
    }

    public function testSupportsReturnsFalseForNonLoginRequest(): void
    {
        $request = new Request([], [], [], [], [], ['REQUEST_METHOD' => 'POST']);
        $request->server->set('REQUEST_URI', '/other');

        $this->assertFalse($this->authenticator->supports($request));
    }

    public function testSupportsReturnsFalseForGetRequest(): void
    {
        $request = new Request([], [], [], [], [], ['REQUEST_METHOD' => 'GET']);
        $request->server->set('REQUEST_URI', '/login');

        $this->assertFalse($this->authenticator->supports($request));
    }

    // public function testAuthenticateWithEmptyCredentialsThrowsException(): void
    // {
    //     $request = new Request();
    //     $request->request->set('_username', '');
    //     $request->request->set('_password', '');

    //     $this->expectException(AuthenticationException::class);
    //     $this->expectExceptionMessage('Nom d\'utilisateur et mot de passe requis.');

    //     $this->authenticator->authenticate($request);
    // }

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