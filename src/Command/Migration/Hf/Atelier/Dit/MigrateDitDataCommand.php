<?php

namespace App\Command\Migration\Hf\Atelier\Dit;

use App\Entity\Hf\Atelier\Dit\Dit;
use App\Service\Migration\Hf\Atelier\Dit\DitMigrationMapper;
use Doctrine\DBAL\Connection;
use Doctrine\ORM\EntityManagerInterface;
use Psr\Log\LoggerInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Helper\ProgressBar;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

class MigrateDitDataCommand extends Command
{
    protected static $defaultName = 'app:migrate:dit-data';
    protected static $defaultdescription = 'Migre les données DIT de l\'ancienne base vers la nouvelle structure';

    private EntityManagerInterface $em;
    private Connection $legacyConnection;
    private DitMigrationMapper $mapper;
    private LoggerInterface $logger;

    public function __construct(
        EntityManagerInterface $em,
        Connection $legacyConnection,
        DitMigrationMapper $mapper,
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
            ->addOption('batch-size', 'b', InputOption::VALUE_REQUIRED, 'Nombre d\'enregistrements à traiter par lot', 50)
            ->addOption('limit', 'l', InputOption::VALUE_REQUIRED, 'Nombre maximum d\'eneregistrments à migrer (pour test)', null)
            ->addOption('offset', 'o', InputOption::VALUE_REQUIRED, 'Décalage de départ (pour reprendre une migration)', 0)
            ->setHelp(
                <<<'HELP'
Cette commande migre les données de la table Demande_ordre_mission de l'ancienne base de données
vers la nouvelle structure de l'entité Dom.

Exemples d'utilisation:

  # Test avec 10 enregistrements en mode dry-run
  php bin/console app:migrate:dit-data --dry-run --limit=10

  # Migration complète avec lots de 50
  php bin/console app:migrate:dit-data --batch-size=50

  # Reprendre une migration à partir de l'enregistrement 1000
  php bin/console app:migrate:dit-data --offset=1000
HELP
            );
    }


    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $io = new SymfonyStyle($input, $output);
        $dryRun = $input->getOption('dry-run');
        $batchSize = (int) $input->getOption('batch-size');
        $limit = (int) $input->getOption('limit') ? (int) $input->getOption('limit') : null;
        $offset = (int) $input->getOption('offset');

        $io->title('Migration des données DIT');

        // Disable Doctrine SQL logging to prevent memory leaks in dev environment
        $this->em->getConnection()->getConfiguration()->setSQLLogger(null);

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
            $totalCount = $this->countLegacyRecords($limit);

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
            $processedNumeros = [];
            $skippedRecords = [];

            while ($processedCount < $totalCount) {
                // Garbage collection pour éviter les problèmes de mémoire sur de gros volumes
                gc_collect_cycles();

                $currentBatchSize = min($batchSize, $totalCount - $processedCount);

                // Récupère un lot de données
                $legacyRecords = $this->fetchLegacyRecords($currentBatchSize, $currentOffset);

                foreach ($legacyRecords as $legacyData) {
                    $stats['total']++;

                    try {
                        $numeroDit = $legacyData['numero_demande_dit'] ?? null;
                        // ID Legacy supposé pour le logging
                        $legacyId = $legacyData['id'] ?? 'unknown';

                        // Vérifie si déjà traité dans ce lot ou les précédents
                        if ($numeroDit && isset($processedNumeros[$numeroDit])) {
                            $stats['skipped']++;
                            $skippedRecords[] = [
                                'id' => $legacyId,
                                'numero_dit' => $numeroDit,
                                'reason' => 'Doublon dans le flux (batch)'
                            ];
                            $this->logger->info('DIT doublon dans le flux (ignoré)', [
                                'numero_dit' => $numeroDit,
                                'old_id' => $legacyId,
                            ]);
                            $progressBar->advance();
                            continue;
                        }

                        // Vérifie si le DIT existe déjà (par numeroDit) dans la BDD
                        if ($numeroDit) {
                            $existingDit = $this->em->getRepository(Dit::class)->findOneBy([
                                'numeroDit' => $numeroDit
                            ]);

                            if ($existingDit) {
                                $stats['skipped']++;
                                $processedNumeros[$numeroDit] = true;
                                $skippedRecords[] = [
                                    'id' => $legacyId,
                                    'numero_dit' => $numeroDit,
                                    'reason' => 'Existe déjà en BDD'
                                ];
                                $this->logger->info('DIT déjà existant (ignoré)', [
                                    'numero_dit' => $numeroDit,
                                    'old_id' => $legacyId,
                                ]);
                                $progressBar->advance();
                                continue;
                            }
                        }

                        // Mappe les données
                        $dit = $this->mapper->mapOldToNew($legacyData);

                        if ($dit === null) {
                            $stats['skipped']++;
                            $this->logger->warning('Enregistrement DIT ignoré (mapping failed)', [
                                'old_id' => $legacyId,
                            ]);
                            continue;
                        }

                        // Persiste si pas en mode dry-run
                        if (!$dryRun) {
                            $this->em->persist($dit);
                        }

                        if ($numeroDit) {
                            $processedNumeros[$numeroDit] = true;
                        }

                        $stats['success']++;
                    } catch (\Exception $e) {
                        $stats['errors']++;
                        $this->logger->error('Erreur lors de la migration d\'un enregistrement DIT', [
                            'old_id' => $legacyData['ID_DIT'] ?? 'unknown',
                            'error' => $e->getMessage(),
                            'trace' => $e->getTraceAsString(),
                        ]);
                    }

                    $progressBar->advance();
                }

                // Flush par lot si pas en mode dry-run
                if (!$dryRun && $stats['success'] > 0) {
                    $this->em->flush();
                }

                // Toujours vider l'EntityManager pour éviter les fuites de mémoire
                $this->em->clear();

                $currentOffset += $currentBatchSize;
                $processedCount += count($legacyRecords);
            }

            $progressBar->finish();
            $io->newLine(2);

            // Génération du fichier CSV des rejetés
            if (!empty($skippedRecords)) {
                $this->generateSkippedCsv($skippedRecords, $io);
            }

            // Affiche les statistiques
            $this->displayStats($io, $stats, $dryRun);

            if ($stats['errors'] > 0) {
                $io->warning(sprintf(
                    '%d erreur(s) détectée(s). Consultez les logs pour plus de détails.',
                    $stats['errors']
                ));
                return Command::FAILURE;
            }

            $io->success('Migration DIT terminée avec succès !');
            return Command::SUCCESS;
        } catch (\Exception $e) {
            $io->error('Erreur fatale lors de la migration: ' . $e->getMessage());
            $this->logger->critical('Erreur fatale lors de la migration DIT', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);
            return Command::FAILURE;
        }
    }

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

        if ($stats['total'] > 0) {
            $successRate = ($stats['success'] / $stats['total']) * 100;
            $io->text(sprintf('Taux de réussite: %.2f%%', $successRate));
        }
    }

    private function generateSkippedCsv(array $skippedRecords, SymfonyStyle $io): void
    {
        $projectDir = dirname(__DIR__, 6);

        $logDir = $projectDir . '/var/log/migration';
        if (!is_dir($logDir)) {
            mkdir($logDir, 0777, true);
        }

        $filename = sprintf('skipped_dit_migration_%s.csv', date('Y-m-d_H-i-s'));
        $filePath = $logDir . '/' . $filename;

        $fp = fopen($filePath, 'w');
        fputcsv($fp, ['ID_Legacy', 'Numero_DIT', 'Raison']);

        foreach ($skippedRecords as $record) {
            fputcsv($fp, $record);
        }

        fclose($fp);

        $io->warning(sprintf(
            'Des enregistrements ont été ignorés. La liste a été sauvegardée dans : %s',
            $filePath
        ));
    }

    /**
     * Compte le nombre d'enregistrements dans l'ancienne base
     */
    private function countLegacyRecords(?int $limit): int
    {
        $sql = 'SELECT COUNT(*) as total FROM demande_intervention';

        if ($limit !== null) {
            // Note: SQL Server ne supporte pas LIMIT directement
            return min($limit, (int) $this->legacyConnection->fetchOne($sql));
        }

        return (int) $this->legacyConnection->fetchOne($sql);
    }

    private function fetchLegacyRecords(int $limit, int $offset): array
    {
        // SQL Server offset syntax
        $sql = <<<SQL
            SELECT *
            FROM demande_intervention
            ORDER BY id
            OFFSET :offset ROWS
            FETCH NEXT :limit ROWS ONLY
        SQL;

        return $this->legacyConnection->fetchAllAssociative($sql, [
            'offset' => $offset,
            'limit' => $limit,
        ]);
    }
}
