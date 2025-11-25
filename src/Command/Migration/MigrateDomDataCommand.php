<?php

namespace App\Command\Migration;

use App\Entity\Hf\Rh\Dom\Dom;
use App\Service\Migration\DomMigrationMapper;
use Doctrine\DBAL\Connection;
use Doctrine\ORM\EntityManagerInterface;
use Psr\Log\LoggerInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Helper\ProgressBar;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

/**
 * Commande de migration des données DOM depuis l'ancienne base de données
 */
class MigrateDomDataCommand extends Command
{
    protected static $defaultName = 'app:migrate:dom-data';
    protected static $defaultDescription = 'Migre les données DOM de l\'ancienne base vers la nouvelle structure';

    private EntityManagerInterface $em;
    private Connection $legacyConnection;
    private DomMigrationMapper $mapper;
    private LoggerInterface $logger;

    public function __construct(
        EntityManagerInterface $em,
        Connection $legacyConnection,
        DomMigrationMapper $mapper,
        LoggerInterface $migrationDomLogger
    ) {
        parent::__construct();
        $this->em = $em;
        $this->legacyConnection = $legacyConnection;
        $this->mapper = $mapper;
        $this->logger = $migrationDomLogger;
    }

    protected function configure(): void
    {
        $this
            ->addOption('dry-run', null, InputOption::VALUE_NONE, 'Exécute la migration sans persister les données')
            ->addOption('batch-size', 'b', InputOption::VALUE_REQUIRED, 'Nombre d\'enregistrements à traiter par lot', 100)
            ->addOption('limit', 'l', InputOption::VALUE_REQUIRED, 'Nombre maximum d\'enregistrements à migrer (pour test)', null)
            ->addOption('offset', 'o', InputOption::VALUE_REQUIRED, 'Décalage de départ (pour reprendre une migration)', 0)
            ->setHelp(
                <<<'HELP'
Cette commande migre les données de la table Demande_ordre_mission de l'ancienne base de données
vers la nouvelle structure de l'entité Dom.

Exemples d'utilisation:

  # Test avec 10 enregistrements en mode dry-run
  php bin/console app:migrate:dom-data --dry-run --limit=10

  # Migration complète avec lots de 50
  php bin/console app:migrate:dom-data --batch-size=50

  # Reprendre une migration à partir de l'enregistrement 1000
  php bin/console app:migrate:dom-data --offset=1000
HELP
            );
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);
        $dryRun = $input->getOption('dry-run');
        $batchSize = (int) $input->getOption('batch-size');
        $limit = $input->getOption('limit') ? (int) $input->getOption('limit') : null;
        $offset = (int) $input->getOption('offset');

        $io->title('Migration des données DOM');

        if ($dryRun) {
            $io->warning('Mode DRY-RUN activé - Aucune donnée ne sera persistée');
        }

        // Statistiques
        $stats = [
            'total' => 0,
            'success' => 0,
            'errors' => 0,
            'skipped' => 0,
        ];

        try {
            // Compte le nombre total d'enregistrements à migrer
            $totalCount = $this->countLegacyRecords($limit, $offset);

            if ($totalCount === 0) {
                $io->warning('Aucun enregistrement à migrer');
                return Command::SUCCESS;
            }

            $io->info(sprintf('Nombre d\'enregistrements à migrer: %d', $totalCount));
            $io->info(sprintf('Taille des lots: %d', $batchSize));

            // Barre de progression
            $progressBar = new ProgressBar($output, $totalCount);
            $progressBar->setFormat('verbose');
            $progressBar->start();

            // Migration par lots
            $currentOffset = $offset;
            $processedCount = 0;

            while ($processedCount < $totalCount) {
                $currentBatchSize = min($batchSize, $totalCount - $processedCount);

                // Récupère un lot de données
                $legacyRecords = $this->fetchLegacyRecords($currentBatchSize, $currentOffset);

                foreach ($legacyRecords as $legacyData) {
                    $stats['total']++;

                    try {
                        // Mappe les données
                        $dom = $this->mapper->mapOldToNew($legacyData);

                        if ($dom === null) {
                            $stats['skipped']++;
                            $this->logger->warning('Enregistrement ignoré (mapping failed)', [
                                'old_id' => $legacyData['ID_Demande_Ordre_Mission'] ?? 'unknown',
                            ]);
                            continue;
                        }

                        // Persiste si pas en mode dry-run
                        if (!$dryRun) {
                            $this->em->persist($dom);
                        }

                        $stats['success']++;
                    } catch (\Exception $e) {
                        $stats['errors']++;
                        $this->logger->error('Erreur lors de la migration d\'un enregistrement', [
                            'old_id' => $legacyData['ID_Demande_Ordre_Mission'] ?? 'unknown',
                            'error' => $e->getMessage(),
                            'trace' => $e->getTraceAsString(),
                        ]);
                    }

                    $progressBar->advance();
                }

                // Flush par lot si pas en mode dry-run
                if (!$dryRun && $stats['success'] > 0) {
                    $this->em->flush();
                    $this->em->clear(); // Libère la mémoire
                }

                $currentOffset += $currentBatchSize;
                $processedCount += count($legacyRecords);
            }

            $progressBar->finish();
            $io->newLine(2);

            // Affiche les statistiques
            $this->displayStats($io, $stats, $dryRun);

            if ($stats['errors'] > 0) {
                $io->warning(sprintf(
                    '%d erreur(s) détectée(s). Consultez les logs pour plus de détails.',
                    $stats['errors']
                ));
                return Command::FAILURE;
            }

            $io->success('Migration terminée avec succès !');
            return Command::SUCCESS;
        } catch (\Exception $e) {
            $io->error('Erreur fatale lors de la migration: ' . $e->getMessage());
            $this->logger->critical('Erreur fatale lors de la migration DOM', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);
            return Command::FAILURE;
        }
    }

    /**
     * Compte le nombre d'enregistrements dans l'ancienne base
     */
    private function countLegacyRecords(?int $limit, int $offset): int
    {
        $sql = 'SELECT COUNT(*) as total FROM Demande_ordre_mission';

        if ($limit !== null) {
            // Note: SQL Server ne supporte pas LIMIT directement
            return min($limit, (int) $this->legacyConnection->fetchOne($sql));
        }

        return (int) $this->legacyConnection->fetchOne($sql);
    }

    /**
     * Récupère un lot d'enregistrements de l'ancienne base
     */
    private function fetchLegacyRecords(int $limit, int $offset): array
    {
        // SQL Server utilise OFFSET/FETCH au lieu de LIMIT
        $sql = <<<SQL
            SELECT *
            FROM Demande_ordre_mission
            ORDER BY ID_Demande_Ordre_Mission
            OFFSET :offset ROWS
            FETCH NEXT :limit ROWS ONLY
        SQL;

        return $this->legacyConnection->fetchAllAssociative($sql, [
            'offset' => $offset,
            'limit' => $limit,
        ]);
    }

    /**
     * Affiche les statistiques de migration
     */
    private function displayStats(SymfonyStyle $io, array $stats, bool $dryRun): void
    {
        $io->section('Statistiques de migration');

        $tableData = [
            ['Total traité', $stats['total']],
            ['Succès', sprintf('<fg=green>%d</>', $stats['success'])],
            ['Erreurs', $stats['errors'] > 0 ? sprintf('<fg=red>%d</>', $stats['errors']) : '0'],
            ['Ignorés', $stats['skipped'] > 0 ? sprintf('<fg=yellow>%d</>', $stats['skipped']) : '0'],
        ];

        if ($dryRun) {
            $tableData[] = ['Mode', '<fg=yellow>DRY-RUN (aucune donnée persistée)</>'];
        }

        $io->table(['Métrique', 'Valeur'], $tableData);

        // Taux de réussite
        if ($stats['total'] > 0) {
            $successRate = ($stats['success'] / $stats['total']) * 100;
            $io->text(sprintf('Taux de réussite: %.2f%%', $successRate));
        }
    }
}
