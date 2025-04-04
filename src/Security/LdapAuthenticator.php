<?php

namespace App\Security;

use Symfony\Component\Ldap\LdapInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\RouterInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\Security\Http\Authenticator\Passport\Passport;
use Symfony\Component\Security\Core\Exception\AuthenticationException;
use Symfony\Component\Security\Http\Authenticator\AbstractAuthenticator;
use Symfony\Component\Security\Http\Authenticator\Passport\Badge\UserBadge;
use Symfony\Component\Security\Http\Authenticator\Passport\SelfValidatingPassport;
use Symfony\Component\Security\Http\Authenticator\Passport\Credentials\PasswordCredentials;

class LdapAuthenticator extends AbstractAuthenticator
{
    private $ldap;
    private $router;
    private $userProvider;
    private $searchDn;
    private $searchPassword;

    public function __construct(
        LdapInterface $ldap, 
        RouterInterface $router,
        LdapUserProvider $userProvider, 
        string $searchDn,
        string $searchPassword
    )
    {
        $this->ldap = $ldap;
        $this->router = $router;
        $this->userProvider = $userProvider;
        $this->searchDn = $searchDn;
        $this->searchPassword = $searchPassword;
    }

    public function supports(Request $request): ?bool
    {
        return $request->getPathInfo() === '/login' && $request->isMethod('POST');
    }

    public function authenticate(Request $request): Passport
    {
        $username = $request->request->get('_username', '');
        $password = $request->request->get('_password', '');
        // 🔐 Bind avec le compte technique AVANT la requête
        $this->ldap->bind($this->searchDn, $this->searchPassword);
        
        $query = $this->ldap->query('DC=fraise,DC=hff,DC=mg', sprintf('(sAMAccountName=%s)', $username));
        
        $results = $query->execute();
        
        if (count($results) === 0) {
            throw new AuthenticationException('Utilisateur LDAP non trouvé.');
        }
        
        $dn = $results[0]->getDn();
        
        
        try {
            $this->ldap->bind($dn, $password);
        } catch (\Exception $e) {
            throw new AuthenticationException('Mot de passe incorrect.');
        }

        // ✅ Charger l'utilisateur via UserProvider
        return new SelfValidatingPassport(
            new UserBadge($username, function ($userIdentifier) {
                return $this->userProvider->loadUserByIdentifier($userIdentifier);
            })
        );
    }

    public function onAuthenticationSuccess(Request $request, $token, string $firewallName): ?\Symfony\Component\HttpFoundation\Response
    {
        return new RedirectResponse($this->router->generate('app_home'));
    }

    public function onAuthenticationFailure(Request $request, AuthenticationException $exception): \Symfony\Component\HttpFoundation\Response
    {
        return new RedirectResponse('/login?error=' . urlencode($exception->getMessage()));
    }
}
