<?php

namespace App\Service;

use Symfony\Component\Ldap\Ldap;
use Symfony\Component\Ldap\Adapter\ExtLdap\AdapterInterface;

class LdapService
{
    private Ldap $ldap;

    public function __construct(Ldap $ldap)
    {
        $this->ldap = $ldap;
    }

    public function authenticate(string $dn, string $password): bool
    {
        try {
            $this->ldap->bind($dn, $password);
            return true;
        } catch (\Exception $e) {
            return false;
        }
    }

    public function search(string $baseDn, string $filter = '(objectClass=*)')
    {
        $query = $this->ldap->query($baseDn, $filter);
        return $query->execute();
    }
}
