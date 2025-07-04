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
    $ldap->bind(
                'CN=Lanto ANDRIANADISON,OU=Informatique,OU=HFF Tana,OU=HFF Users,DC=fraise,DC=hff,DC=mg',
                'Hasina#2025-2'
            );

    $query = $ldap->query(
        'DC=fraise,DC=hff,DC=mg',
        '(sAMAccountName=lanto)'
    );
    $results = $query->execute();
    // dd($results);

    foreach ($results as $entry) {
        dump($entry);	
        // echo "DN: ".$entry->getDn()."\n";
    }
} catch (\Exception $e) {
    echo "Erreur LDAP : " . $e->getMessage() . "\n";
}

// try {
//     // Authentification
//     $ldap->bind(
//         'CN=Lanto ANDRIANADISON,OU=Informatique,OU=HFF Tana,OU=HFF Users,DC=fraise,DC=hff,DC=mg',
//         'Hasina#2025-2'
//     );

//     // Requête LDAP
//     $search = $ldap->query(
//         'OU=HFF Users,DC=fraise,DC=hff,DC=mg',
//         '(objectClass=*)'
//     );
//     $results = $search->execute();

//     // Afficher les résultats
//     foreach ($results as $entry) {
//         // echo "DN: " . $entry->getDn() . PHP_EOL;
//          dump($entry);
//     }
// } catch (\Exception $e) {
//     echo "Erreur : " . $e->getMessage() . PHP_EOL;
// }
