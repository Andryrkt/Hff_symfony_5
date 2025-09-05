<?php

namespace App\Command;

use App\Entity\Dom\DomSousTypeDocument;
use App\Entity\Dom\DomCategorie;
use App\Entity\Dom\DomRmq;
use App\Service\Dom\DomCacheService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class TestDomCategoriesCommand extends Command
{
    protected static $defaultName = 'app:test-dom-categories';
    protected static $defaultDescription = 'Test l\'affichage des catégories DOM';

    private EntityManagerInterface $em;
    private DomCacheService $cacheService;

    public function __construct(EntityManagerInterface $em, DomCacheService $cacheService)
    {
        $this->em = $em;
        $this->cacheService = $cacheService;
        parent::__construct();
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $output->writeln('🧪 Test des catégories DOM...');
        $output->writeln('============================');

        try {
            // 1. Vérifier les sous-types de documents
            $output->writeln('1. Vérification des sous-types de documents...');
            $sousTypes = $this->em->getRepository(DomSousTypeDocument::class)->findAll();
            foreach ($sousTypes as $sousType) {
                $output->writeln("   ✓ {$sousType->getCodeSousType()} (ID: {$sousType->getId()})");
            }
            $output->writeln('');

            // 2. Vérifier les RMQ
            $output->writeln('2. Vérification des RMQ...');
            $rmqs = $this->em->getRepository(DomRmq::class)->findAll();
            foreach ($rmqs as $rmq) {
                $output->writeln("   ✓ {$rmq->getDescription()} (ID: {$rmq->getId()})");
            }
            $output->writeln('');

            // 3. Vérifier les catégories
            $output->writeln('3. Vérification des catégories...');
            $categories = $this->em->getRepository(DomCategorie::class)->findAll();
            foreach ($categories as $categorie) {
                $sousType = $categorie->getDomSousTypeDocumentId();
                $sousTypeCode = $sousType ? $sousType->getCodeSousType() : 'Aucun';
                $output->writeln("   ✓ {$categorie->getDescription()} (Sous-type: {$sousTypeCode})");
            }
            $output->writeln('');

            // 4. Test de la requête des catégories pour MISSION
            $output->writeln('4. Test de la requête des catégories pour MISSION...');
            $missionSousType = $this->em->getRepository(DomSousTypeDocument::class)
                ->findOneBy(['codeSousType' => 'MISSION']);

            if ($missionSousType) {
                $output->writeln("   ✓ Sous-type MISSION trouvé (ID: {$missionSousType->getId()})");

                // Test avec RMQ STD
                $output->writeln('   Test avec RMQ STD...');
                $categoriesStd = $this->em->createQueryBuilder()
                    ->select('DISTINCT c')
                    ->from(DomCategorie::class, 'c')
                    ->join('c.domIndemnites', 'i')
                    ->join('i.domRmqId', 'r')
                    ->where('i.domSousTypeDocumentId = :sousTypeDoc')
                    ->andWhere('r.description = :rmqDescription')
                    ->setParameter('sousTypeDoc', $missionSousType)
                    ->setParameter('rmqDescription', 'STD')
                    ->getQuery()
                    ->getResult();

                if (count($categoriesStd) > 0) {
                    $output->writeln("   ✓ " . count($categoriesStd) . " catégories trouvées pour MISSION + STD:");
                    foreach ($categoriesStd as $categorie) {
                        $output->writeln("     - {$categorie->getDescription()}");
                    }
                } else {
                    $output->writeln('   ⚠ Aucune catégorie trouvée pour MISSION + STD');
                }

                // Test avec RMQ 50
                $output->writeln('   Test avec RMQ 50...');
                $categories50 = $this->em->createQueryBuilder()
                    ->select('DISTINCT c')
                    ->from(DomCategorie::class, 'c')
                    ->join('c.domIndemnites', 'i')
                    ->join('i.domRmqId', 'r')
                    ->where('i.domSousTypeDocumentId = :sousTypeDoc')
                    ->andWhere('r.description = :rmqDescription')
                    ->setParameter('sousTypeDoc', $missionSousType)
                    ->setParameter('rmqDescription', '50')
                    ->getQuery()
                    ->getResult();

                if (count($categories50) > 0) {
                    $output->writeln("   ✓ " . count($categories50) . " catégories trouvées pour MISSION + 50:");
                    foreach ($categories50 as $categorie) {
                        $output->writeln("     - {$categorie->getDescription()}");
                    }
                } else {
                    $output->writeln('   ⚠ Aucune catégorie trouvée pour MISSION + 50');
                }
            } else {
                $output->writeln('   ✗ Sous-type MISSION non trouvé');
            }
            $output->writeln('');

            // 5. Test du service de cache
            $output->writeln('5. Test du service de cache...');
            if ($missionSousType) {
                $cachedCategories = $this->cacheService->getCategoriesByCriteria($missionSousType->getId(), 'STD');
                if (count($cachedCategories) > 0) {
                    $output->writeln("   ✓ Service de cache: " . count($cachedCategories) . " catégories trouvées");
                    foreach ($cachedCategories as $categorie) {
                        $output->writeln("     - {$categorie['description']}");
                    }
                } else {
                    $output->writeln('   ⚠ Service de cache: Aucune catégorie trouvée');
                }
            }
            $output->writeln('');

            $output->writeln('🎉 Test terminé !');
            return Command::SUCCESS;
        } catch (\Exception $e) {
            $output->writeln('❌ Erreur: ' . $e->getMessage());
            $output->writeln('Stack trace:');
            $output->writeln($e->getTraceAsString());
            return Command::FAILURE;
        }
    }
}
