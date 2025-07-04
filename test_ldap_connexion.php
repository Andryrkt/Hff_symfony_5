<?php
$ldap_host = '192.168.0.1';
$ldap_port = 389;
$ldap_dn = 'CN=Lanto ANDRIANADISON,OU=Informatique,OU=HFF Tana,OU=HFF Users,DC=fraise,DC=hff,DC=mg';
$ldap_password = 'Hasina#2025-2';
$base_dn = 'DC=fraise,DC=hff,DC=mg';

$ldapconn = ldap_connect($ldap_host, $ldap_port);
ldap_set_option($ldapconn, LDAP_OPT_PROTOCOL_VERSION, 3);
ldap_set_option($ldapconn, LDAP_OPT_REFERRALS, 0);

if ($ldapconn) {
    $bind = ldap_bind($ldapconn, $ldap_dn, $ldap_password);
    if ($bind) {
        echo "Bind OK\n";
        $filter = '(sAMAccountName=lanto)';
        $result = ldap_search($ldapconn, $base_dn, $filter);
        if ($result) {
            $entries = ldap_get_entries($ldapconn, $result);
            print_r($entries);
        } else {
            echo "Erreur de recherche LDAP\n";
        }
    } else {
        echo "Bind échoué : ".ldap_error($ldapconn)."\n";
    }
    ldap_unbind($ldapconn);
} else {
    echo "Connexion LDAP impossible\n";
}