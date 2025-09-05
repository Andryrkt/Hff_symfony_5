<?php

require_once __DIR__ . '/../vendor/autoload.php';

use Symfony\Component\Dotenv\Dotenv;
use App\Entity\Dom\DomSousTypeDocument;
use App\Entity\Dom\DomCategorie;
use App\Entity\Dom\DomRmq;
use App\Entity\Dom\DomSite;
use App\Entity\Dom\DomIndemnite;

// Charger les variables d'environnement
$dotenv = new Dotenv();
$dotenv->load(__DIR__ . '/../.env');

// Créer le kernel Symfony
$kernel = new \App\Kernel('dev', true);
$kernel->boot();

// Récupérer l'EntityManager
$em = $kernel->getContainer()->get('doctrine.orm.entity_manager');

echo "🔄 Chargement des données DOM...\n";

try {
    // 1. Créer les sous-types de documents
    echo "1. Création des sous-types de documents...\n";

    $sousTypes = [
        'MISSION' => 'MISSION',
        'COMPLEMENT' => 'COMPLEMENT',
        'MUTATION' => 'MUTATION',
        'FRAIS EXCEPTIONNEL' => 'FRAIS EXCEPTIONNEL',
        'TROP PERCU' => 'TROP PERCU',
    ];

    $sousTypeEntities = [];
    foreach ($sousTypes as $code) {
        $existing = $em->getRepository(DomSousTypeDocument::class)->findOneBy(['codeSousType' => $code]);
        if (!$existing) {
            $sousType = new DomSousTypeDocument();
            $sousType->setCodeSousType($code);
            $em->persist($sousType);
            $sousTypeEntities[$code] = $sousType;
            echo "   ✓ Créé: $code\n";
        } else {
            $sousTypeEntities[$code] = $existing;
            echo "   ✓ Existe déjà: $code\n";
        }
    }

    // 2. Créer les RMQ
    echo "\n2. Création des RMQ...\n";

    $rmqStd = $em->getRepository(DomRmq::class)->findOneBy(['description' => 'STD']);
    if (!$rmqStd) {
        $rmqStd = new DomRmq();
        $rmqStd->setDescription('STD');
        $em->persist($rmqStd);
        echo "   ✓ Créé: STD\n";
    } else {
        echo "   ✓ Existe déjà: STD\n";
    }

    $rmq50 = $em->getRepository(DomRmq::class)->findOneBy(['description' => '50']);
    if (!$rmq50) {
        $rmq50 = new DomRmq();
        $rmq50->setDescription('50');
        $em->persist($rmq50);
        echo "   ✓ Créé: 50\n";
    } else {
        echo "   ✓ Existe déjà: 50\n";
    }

    // 3. Créer les sites
    echo "\n3. Création des sites...\n";

    $sites = [
        'AUTRES VILLES',
        'HORS TANA MOINS DE 24H',
        'ZONES ENCLAVEES',
        'ZONES TOURISTIQUES',
        'FORT-DAUPHIN',
        'AUTRES SITE ENCLAVES',
        'HORS TANA',
        'TANA'
    ];

    $siteEntities = [];
    foreach ($sites as $nomZone) {
        $existing = $em->getRepository(DomSite::class)->findOneBy(['nomZone' => $nomZone]);
        if (!$existing) {
            $site = new DomSite();
            $site->setNomZone($nomZone);
            $em->persist($site);
            $siteEntities[$nomZone] = $site;
            echo "   ✓ Créé: $nomZone\n";
        } else {
            $siteEntities[$nomZone] = $existing;
            echo "   ✓ Existe déjà: $nomZone\n";
        }
    }

    // 4. Créer les catégories
    echo "\n4. Création des catégories...\n";

    $categories = [
        'Agents de maitrise, employes specialises' => 'MISSION',
        'Cadre HC' => 'MISSION',
        'Chef de service' => 'MISSION',
        'Ouvriers et chauffeurs' => 'MISSION',
        'Toute Categorie' => 'MUTATION',
    ];

    $categorieEntities = [];
    foreach ($categories as $description => $sousTypeCode) {
        $existing = $em->getRepository(DomCategorie::class)->findOneBy(['description' => $description]);
        if (!$existing) {
            $categorie = new DomCategorie();
            $categorie->setDescription($description);
            if (isset($sousTypeEntities[$sousTypeCode])) {
                $categorie->setDomSousTypeDocumentId($sousTypeEntities[$sousTypeCode]);
            }
            $em->persist($categorie);
            $categorieEntities[$description] = $categorie;
            echo "   ✓ Créé: $description\n";
        } else {
            $categorieEntities[$description] = $existing;
            echo "   ✓ Existe déjà: $description\n";
        }
    }

    // 5. Créer quelques indemnités
    echo "\n5. Création des indemnités...\n";

    $indemnites = [
        ['montant' => 50000, 'site' => 'ZONES TOURISTIQUES', 'categorie' => 'Chef de service', 'rmq' => $rmqStd, 'sousType' => 'MISSION'],
        ['montant' => 15000, 'site' => 'HORS TANA MOINS DE 24H', 'categorie' => 'Chef de service', 'rmq' => $rmqStd, 'sousType' => 'MISSION'],
        ['montant' => 45000, 'site' => 'ZONES ENCLAVEES', 'categorie' => 'Chef de service', 'rmq' => $rmqStd, 'sousType' => 'MISSION'],
        ['montant' => 40000, 'site' => 'AUTRES VILLES', 'categorie' => 'Chef de service', 'rmq' => $rmqStd, 'sousType' => 'MISSION'],
        ['montant' => 45000, 'site' => 'AUTRES VILLES', 'categorie' => 'Cadre HC', 'rmq' => $rmqStd, 'sousType' => 'MISSION'],
    ];

    foreach ($indemnites as $indemniteData) {
        $indemnite = new DomIndemnite();
        $indemnite->setMontant($indemniteData['montant']);
        $indemnite->setDomSiteId($siteEntities[$indemniteData['site']]);
        $indemnite->setDomCategorieId($categorieEntities[$indemniteData['categorie']]);
        $indemnite->setDomRmqId($indemniteData['rmq']);
        $indemnite->setDomSousTypeDocumentId($sousTypeEntities[$indemniteData['sousType']]);
        $em->persist($indemnite);
        echo "   ✓ Créé: {$indemniteData['categorie']} - {$indemniteData['site']} - {$indemniteData['montant']}\n";
    }

    // Sauvegarder en base
    $em->flush();

    echo "\n🎉 Données DOM chargées avec succès !\n";
    echo "Vous pouvez maintenant tester le formulaire DOM.\n";
} catch (\Exception $e) {
    echo "❌ Erreur: " . $e->getMessage() . "\n";
    echo "Stack trace:\n" . $e->getTraceAsString() . "\n";
    exit(1);
}
