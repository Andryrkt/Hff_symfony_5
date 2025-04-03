<?php

namespace App\Security;

use Symfony\Component\Ldap\Ldap;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Core\User\User;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Http\Authenticator\Passport\Passport;
use Symfony\Component\Security\Core\Exception\AuthenticationException;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Http\Authenticator\AbstractAuthenticator;
use Symfony\Component\Security\Http\Authenticator\Passport\Badge\UserBadge;
use Symfony\Component\Security\Http\Authenticator\Passport\Badge\RememberMeBadge;
use Symfony\Component\Security\Http\Authenticator\Passport\Credentials\PasswordCredentials;

class LdapAuthenticator extends AbstractAuthenticator
{
    public function supports(Request $request): ?bool
    {
        return $request->attributes->get('_route') === 'app_login' && $request->isMethod('POST');
    }

    public function authenticate(Request $request): Passport
{
    $username = $request->request->get('username');
    $password = $request->request->get('password');

    return new Passport(
        new UserBadge($username, function ($userIdentifier) use ($password) {
            // Récupération de l'utilisateur à partir de LDAP
            $ldap = Ldap::create('ext_ldap', [
                'host' => '192.168.0.1',
                'port' => 389,
                'options' => [
                    'protocol_version' => 3,
                    'referrals' => false,
                ],
            ]);

            try {
                // Bind avec les informations fournies par l'utilisateur
                $ldap->bind("CN=$userIdentifier,OU=HFF Users,DC=fraise,DC=hff,DC=mg", $password);

                // L'utilisateur existe et les informations sont valides
                return new User($userIdentifier, $password, ['ROLE_USER']);
            } catch (\Exception $e) {
                throw new AuthenticationException('Utilisateur ou mot de passe invalide.');
            }
        }),
        new PasswordCredentials($password)
    );
}


    public function onAuthenticationSuccess(Request $request, TokenInterface $token, string $firewallName): ?RedirectResponse
    {
        return new RedirectResponse('/');
        //return new Response('Connexion réussie !', 200);
    }

    public function onAuthenticationFailure(Request $request, AuthenticationException $exception): ?RedirectResponse
    {
        // return new RedirectResponse('/login?error=' . urlencode($exception->getMessage()));
        $error = urlencode('Connexion échouée : ' . $exception->getMessage());
        return new RedirectResponse('/login?error=' . $error);
    }
}
