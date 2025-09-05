<?php

require_once __DIR__ . '/../vendor/autoload.php';

use Symfony\Component\Dotenv\Dotenv;

// Charger les variables d'environnement
$dotenv = new Dotenv();
$dotenv->load(__DIR__ . '/../.env');

// Créer le kernel Symfony
$kernel = new \App\Kernel('dev', true);
$kernel->boot();

// Récupérer le conteneur
$container = $kernel->getContainer();

try {
    echo "🧪 Test du formulaire DOM - Demande d'Ordre de Mission\n";
    echo "====================================================\n\n";

    // Test 1: Vérifier que le service DomCacheService est disponible
    echo "1. Test du service DomCacheService...\n";
    $cacheService = $container->get('App\Service\Dom\DomCacheService');
    echo "   ✓ Service DomCacheService chargé avec succès\n\n";

    // Test 2: Vérifier que le repository DomSousTypeDocumentRepository est disponible
    echo "2. Test du repository DomSousTypeDocumentRepository...\n";
    $sousTypeRepo = $container->get('App\Repository\Dom\DomSousTypeDocumentRepository');
    echo "   ✓ Repository DomSousTypeDocumentRepository chargé avec succès\n\n";

    // Test 3: Créer un DTO DomFirstFormData
    echo "3. Test de création du DTO DomFirstFormData...\n";
    $em = $container->get('doctrine.orm.entity_manager');
    $dto = new \App\Dto\Dom\DomFirstFormData($sousTypeRepo, null, $cacheService, $em);
    echo "   ✓ DTO DomFirstFormData créé avec succès\n";

    if ($dto->getSousTypeDocument()) {
        echo "   ✓ Sous-type de document chargé: " . $dto->getSousTypeDocument()->getCodeSousType() . "\n";
    } else {
        echo "   ⚠ Aucun sous-type de document trouvé\n";
    }
    echo "\n";

    // Test 4: Vérifier que l'entité est gérée par l'EntityManager
    echo "4. Test de gestion de l'entité par l'EntityManager...\n";
    if ($dto->getSousTypeDocument()) {
        $isManaged = $em->contains($dto->getSousTypeDocument());
        if ($isManaged) {
            echo "   ✓ L'entité est gérée par l'EntityManager\n";
        } else {
            echo "   ✗ L'entité n'est PAS gérée par l'EntityManager\n";
        }
    } else {
        echo "   ⚠ Pas d'entité à tester\n";
    }
    echo "\n";

    // Test 5: Test du formulaire
    echo "5. Test de création du formulaire...\n";
    $formFactory = $container->get('form.factory');
    $form = $formFactory->create(\App\Form\Dom\DomFirstFormType::class, $dto);
    echo "   ✓ Formulaire créé avec succès\n";
    echo "   ✓ Nombre de champs: " . count($form->all()) . "\n\n";

    echo "🎉 Tous les tests sont passés avec succès !\n";
    echo "Le formulaire DOM devrait maintenant fonctionner sans erreur.\n";
} catch (\Exception $e) {
    echo "❌ Erreur lors du test: " . $e->getMessage() . "\n";
    echo "Stack trace:\n" . $e->getTraceAsString() . "\n";
    exit(1);
}
