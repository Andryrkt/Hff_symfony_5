<?php

namespace App\Security;

use Symfony\Component\Ldap\LdapInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Security\Http\Authenticator\Passport\Passport;
use Symfony\Component\Security\Core\Exception\AuthenticationException;
use Symfony\Component\Security\Http\Authenticator\AbstractAuthenticator;
use Symfony\Component\Security\Http\Authenticator\Passport\Badge\UserBadge;
use Symfony\Component\Security\Http\Authenticator\Passport\SelfValidatingPassport;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;

class ApiJsonAuthenticator extends AbstractAuthenticator
{
    private $ldap;
    private $userProvider;
    private $searchDn;
    private $searchPassword;
    private $baseDn;
    private $uidKey;
    private $successHandler;

    public function __construct(
        LdapInterface $ldap,
        LdapUserProvider $userProvider,
        string $searchDn,
        string $searchPassword,
        string $baseDn,
        string $uidKey,
        JwtAuthenticationSuccessHandler $successHandler
    ) {
        $this->ldap = $ldap;
        $this->userProvider = $userProvider;
        $this->searchDn = $searchDn;
        $this->searchPassword = $searchPassword;
        $this->baseDn = $baseDn;
        $this->uidKey = $uidKey;
        $this->successHandler = $successHandler;
    }

    public function supports(Request $request): ?bool
    {
        return $request->getPathInfo() === '/api/login'
            && $request->isMethod('POST')
            && $request->getContentType() === 'json';
    }

    public function authenticate(Request $request): Passport
    {
        $data = json_decode($request->getContent(), true);

        if (!isset($data['username']) || !isset($data['password'])) {
            throw new AuthenticationException('Username and password are required.');
        }

        $username = $data['username'];
        $password = $data['password'];

        if (empty($username) || empty($password)) {
            throw new AuthenticationException('Username and password cannot be empty.');
        }

        // Bind avec le compte technique LDAP
        try {
            $this->ldap->bind($this->searchDn, $this->searchPassword);
        } catch (\Exception $e) {
            throw new AuthenticationException('LDAP connection failed.');
        }

        // Recherche de l'utilisateur dans LDAP
        $query = $this->ldap->query($this->baseDn, sprintf('(%s=%s)', $this->uidKey, $username));

        if (!$query) {
            throw new AuthenticationException('Invalid LDAP query.');
        }

        $results = $query->execute();

        if (count($results) === 0) {
            throw new AuthenticationException('User not found in LDAP.');
        }

        $dn = $results[0]->getDn();

        // Validation du mot de passe via LDAP bind
        try {
            $this->ldap->bind($dn, $password);
        } catch (\Exception $e) {
            throw new AuthenticationException('Invalid credentials.');
        }

        // Charger ou créer l'utilisateur local
        $user = $this->userProvider->loadUserByIdentifier($username);

        return new SelfValidatingPassport(
            new UserBadge($username, function () use ($user) {
                return $user;
            })
        );
    }

    public function onAuthenticationSuccess(Request $request, TokenInterface $token, string $firewallName): ?Response
    {
        return $this->successHandler->onAuthenticationSuccess($request, $token);
    }

    public function onAuthenticationFailure(Request $request, AuthenticationException $exception): ?Response
    {
        return new JsonResponse([
            'error' => $exception->getMessage()
        ], Response::HTTP_UNAUTHORIZED);
    }
}
