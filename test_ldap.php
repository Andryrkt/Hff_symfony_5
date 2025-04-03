<?php

require_once __DIR__ . '/vendor/autoload.php'; // Assurez-vous que votre autoloader Composer est inclus

use Symfony\Component\Ldap\Ldap;

$ldap = Ldap::create('ext_ldap', [
    'host' => '192.168.0.1',
    'port' => 389,
    'options' => [
        'protocol_version' => 3,
        'referrals' => false,
    ],
]);

try {
    // Authentification
    $ldap->bind(
        'CN=Lanto ANDRIANADISON,OU=Informatique,OU=HFF Tana,OU=HFF Users,DC=fraise,DC=hff,DC=mg',
        '@Andryrkt2971*'
    );

    // Requête LDAP
    $search = $ldap->query(
        'OU=HFF Users,DC=fraise,DC=hff,DC=mg',
        '(objectClass=*)'
    );
    $results = $search->execute();

    // Afficher les résultats
    foreach ($results as $entry) {
        echo "DN: " . $entry->getDn() . PHP_EOL;
    }
} catch (\Exception $e) {
    echo "Erreur : " . $e->getMessage() . PHP_EOL;
}
