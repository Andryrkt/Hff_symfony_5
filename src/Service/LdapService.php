<?php

namespace App\Service;

use Symfony\Component\Ldap\Ldap;
use Symfony\Component\Security\Core\Exception\AuthenticationException;


class LdapService
{
    private Ldap $ldap;
    private string $searchDn;
    private string $searchPassword;

    public function __construct(Ldap $ldap, string $searchDn, string $searchPassword)
    {
        $this->ldap = $ldap;
        $this->searchDn = $searchDn;
        $this->searchPassword = $searchPassword;
    }

    public function authenticate(string $username, string $password): bool
    {
        // 1. Bind avec le compte technique
        $this->ldap->bind($this->searchDn, $this->searchPassword);

        // 2. Recherche de l'utilisateur
        $baseDn = $_ENV['LDAP_BASE_DN'] ?? 'OU=HFF Users,DC=fraise,DC=hff,DC=mg';
        $query = $this->ldap->query($baseDn, sprintf('(sAMAccountName=%s)', $username));

        if (!$query) {
            throw new AuthenticationException('La requête LDAP est invalide.');
        }

        $results = $query->execute();

        if (count($results) === 0) {
            throw new AuthenticationException('Utilisateur LDAP non trouvé.');
        }

        $dn = $results[0]->getDn();

        // 3. Bind avec le DN de l'utilisateur et son mot de passe
        try {
            $this->ldap->bind($dn, $password);
            return true;
        } catch (\Exception $e) {
            return false;
        }
    }

    public function search(string $baseDn, string $filter = '(objectClass=*)')
    {
        $this->ldap->bind($this->searchDn, $this->searchPassword);
        $query = $this->ldap->query($baseDn, $filter);
        return $query->execute();
    }
}
