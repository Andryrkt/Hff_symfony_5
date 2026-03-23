<?php

namespace App\Command\Migration\Hf\Materiel\Casier;

use Psr\Log\LoggerInterface;
use Doctrine\DBAL\Connection;
use Doctrine\ORM\EntityManagerInterface;
use App\Entity\Hf\Materiel\Casier\Casier;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Helper\ProgressBar;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use App\Service\Migration\Hf\Materiel\Casier\CasierMigrationMapper;

class MigrateCasierDataCommand extends Command
{
    protected static $defaultName = 'app:migrate:casier-data';
    protected static $defaultDescription = 'Migre les données du Casier de l\'ancienne base vers la nouvelle structure';

    private EntityManagerInterface $em;
    private Connection $legacyConnection;
    private CasierMigrationMapper $mapper;
    private LoggerInterface $logger;

    public function __construct(
        EntityManagerInterface $em,
        Connection $legacyConnection,
        CasierMigrationMapper $mapper,
        LoggerInterface $logger
    ) {
        parent::__construct();
        $this->em = $em;
        $this->legacyConnection = $legacyConnection;
        $this->mapper = $mapper;
        $this->logger = $logger;
    }

    protected function configure(): void
    {
        $this
            ->addOption('dry-run', null, InputOption::VALUE_NONE, 'Exécute la migration sans persister les données')
            ->addOption(
                'batch-size',
                'b',
                InputOption::VALUE_REQUIRED,
                'Nombre d\'enregistrements à traiter par lot',
                50  // Réduit pour éviter les problèmes de mémoire
            )->addOption('limit', 'l', InputOption::VALUE_REQUIRED, 'Nombre maximum d\'enregistrements à migrer (pour test)', null)
            ->addOption('offset', 'o', InputOption::VALUE_REQUIRED, 'Décalage de départ (pour reprendre une migration)', 0)
            ->setHelp(
                <<<'HELP'
Cette commande migre les données de la table Casier_Materiels de l'ancienne base de données
vers la nouvelle structure de l'entité Casier.

Exemples d'utilisation:

  # Test avec 10 enregistrements en mode dry-run
  php bin/console app:migrate:casier-data --dry-run --limit=10

  # Migration complète avec lots de 50
  php bin/console app:migrate:casier-data --batch-size=50

  # Reprendre une migration à partir de l'enregistrement 1000
  php bin/console app:migrate:casier-data --offset=1000
HELP
            )
            ->setDescription(self::$defaultDescription);
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);
        $dryRun = $input->getOption('dry-run');
        $batchSize = (int) $input->getOption('batch-size');
        $limit = $input->getOption('limit') ? (int) $input->getOption('limit') : null;
        $offset = (int) $input->getOption('offset');

        $io->title('Migration des données CASIER');

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
            $processedNumeros = [];
            $skippedRecords = [];

            while ($processedCount < $totalCount) {
                $currentBatchSize = min($batchSize, $totalCount - $processedCount);

                // Récupère un lot de données
                $legacyRecords = $this->fetchLegacyRecords($currentBatchSize, $currentOffset);

                foreach ($legacyRecords as $legacyData) {
                    $stats['total']++;

                    try {
                        $numeroCasier = $legacyData['Numero_CAS'] ?? null;

                        // Vérifie si déjà traité dans ce lot ou les précédents (pour éviter les doublons dans le même flux)
                        if ($numeroCasier && isset($processedNumeros[$numeroCasier])) {
                            $stats['skipped']++;
                            $skippedRecords[] = [
                                'id' => $legacyData['Id'] ?? 'unknown',
                                'numero_casier' => $numeroCasier,
                                'reason' => 'Doublon dans le flux (batch)'
                            ];
                            $this->logger->info('CASIER doublon dans le flux (ignoré)', [
                                'numero_casier' => $numeroCasier,
                                'old_id' => $legacyData['Id'] ?? 'unknown',
                            ]);
                            $progressBar->advance();
                            continue;
                        }

                        // Vérifie si le CASIER existe déjà (par Numero_CAS) dans la BDD
                        $existingCasier = $this->em->getRepository(Casier::class)->findOneBy([
                            'numero' => $numeroCasier
                        ]);

                        if ($existingCasier) {
                            $stats['skipped']++;
                            if ($numeroCasier) {
                                $processedNumeros[$numeroCasier] = true;
                            }
                            $skippedRecords[] = [
                                'id' => $legacyData['Id'] ?? 'unknown',
                                'numero_casier' => $numeroCasier,
                                'reason' => 'Existe déjà en BDD'
                            ];
                            $this->logger->info('CASIER déjà existant (ignoré)', [
                                'numero_casier' => $numeroCasier,
                                'old_id' => $legacyData['Id'] ?? 'unknown',
                            ]);
                            $progressBar->advance();
                            continue;
                        }

                        // Mappe les données
                        $dom = $this->mapper->mapOldToNew($legacyData);

                        if ($dom === null) {
                            $stats['skipped']++;
                            $this->logger->warning('Enregistrement ignoré (mapping failed)', [
                                'old_id' => $legacyData['Id'] ?? 'unknown',
                            ]);
                            continue;
                        }

                        // Persiste si pas en mode dry-run
                        if (!$dryRun) {
                            $this->em->persist($dom);
                        }

                        if ($numeroCasier) {
                            $processedNumeros[$numeroCasier] = true;
                        }

                        $stats['success']++;
                    } catch (\Exception $e) {
                        $stats['errors']++;
                        $this->logger->error('Erreur lors de la migration d\'un enregistrement', [
                            'old_id' => $legacyData['Id'] ?? 'unknown',
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

            // Génération du fichier CSV des rejetés
            if (!empty($skippedRecords)) {
                $projectDir = dirname(__DIR__, 6);
                $logDir = $projectDir . '/var/log';
                if (!is_dir($logDir)) {
                    mkdir($logDir, 0777, true);
                }

                $filename = sprintf('skipped_casier_migration_%s.csv', date('Y-m-d_H-i-s'));
                $filePath = $logDir . '/' . $filename;

                $fp = fopen($filePath, 'w');
                fputcsv($fp, ['ID_Legacy', 'Numero_Casier', 'Raison']);

                foreach ($skippedRecords as $record) {
                    fputcsv($fp, $record);
                }

                fclose($fp);

                $io->warning(sprintf(
                    'Des enregistrements ont été ignorés. La liste a été sauvegardée dans : %s',
                    $filePath
                ));
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

            $io->success('Migration terminée avec succès !');
            return Command::SUCCESS;
        } catch (\Exception $e) {
            $io->error('Erreur fatale lors de la migration: ' . $e->getMessage());
            $this->logger->critical('Erreur fatale lors de la migration du CASIER', [
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
        $sql = 'SELECT COUNT(*) as total FROM Casier_Materiels';

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
            FROM Casier_Materiels
            ORDER BY Numero_CAS, Id
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
