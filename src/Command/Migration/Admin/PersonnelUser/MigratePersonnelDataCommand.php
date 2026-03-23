<?php

namespace App\Command\Migration\Admin\PersonnelUser;


use App\Service\Migration\Admin\PersonnelUser\PersonnelMigrationMapper;
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
 * Commande de migration des données Personnel depuis l'ancienne base de données
 */
class MigratePersonnelDataCommand extends Command
{
    protected static $defaultName = 'app:migrate:personnel-data';
    protected static $defaultDescription = 'Migre les données Personnel de l\'ancienne base vers la nouvelle structure';

    private EntityManagerInterface $em;
    private Connection $legacyConnection;
    private PersonnelMigrationMapper $mapper;
    private LoggerInterface $logger;

    public function __construct(
        EntityManagerInterface $em,
        Connection $legacyConnection,
        PersonnelMigrationMapper $mapper,
        LoggerInterface $migrationLogger
    ) {
        parent::__construct();
        $this->em = $em;
        $this->legacyConnection = $legacyConnection;
        $this->mapper = $mapper;
        $this->logger = $migrationLogger;
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
                50
            )
            ->addOption('limit', 'l', InputOption::VALUE_REQUIRED, 'Nombre maximum d\'enregistrements à migrer (pour test)', null)
            ->addOption('offset', 'o', InputOption::VALUE_REQUIRED, 'Décalage de départ (pour reprendre une migration)', 0)
            ->addOption('update', 'u', InputOption::VALUE_NONE, 'Met à jour les enregistrements existants au lieu de les ignorer')
            ->setHelp(
                <<<'HELP'
Cette commande migre les données de la table personnels de l'ancienne base de données
vers la nouvelle structure de l'entité Personnel.

Exemples d'utilisation:

  # Test avec 10 enregistrements en mode dry-run
  php bin/console app:migrate:personnel-data --dry-run --limit=10

  # Migration complète avec lots de 50
  php bin/console app:migrate:personnel-data --batch-size=50

  # Reprendre une migration à partir de l'enregistrement 1000
  php bin/console app:migrate:personnel-data --offset=1000

  # Mettre à jour les enregistrements existants
  php bin/console app:migrate:personnel-data --update
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
        $updateExisting = $input->getOption('update');

        $io->title('Migration des données Personnel');

        if ($dryRun) {
            $io->warning('Mode DRY-RUN activé - Aucune donnée ne sera persistée');
        }

        if ($updateExisting) {
            $io->note('Mode UPDATE activé - Les enregistrements existants seront mis à jour');
        }

        // Statistiques
        $stats = [
            'total' => 0,
            'success' => 0,
            'updated' => 0,
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
                        // Vérifie si le personnel existe déjà (par matricule)
                        $existingPersonnel = $this->mapper->findExistingByMatricule($legacyData['matricule'] ?? null);

                        if ($existingPersonnel && !$updateExisting) {
                            $stats['skipped']++;
                            $this->logger->info('Personnel déjà existant (ignoré)', [
                                'matricule' => $legacyData['Matricule'] ?? 'N/A',
                            ]);
                            $progressBar->advance();
                            continue;
                        }

                        if ($existingPersonnel && $updateExisting) {
                            // Mise à jour
                            $personnel = $this->mapper->updateExisting($existingPersonnel, $legacyData);
                            $stats['updated']++;
                        } else {
                            // Création
                            $personnel = $this->mapper->mapOldToNew($legacyData);

                            if ($personnel === null) {
                                $stats['skipped']++;
                                $this->logger->warning('Enregistrement ignoré (mapping failed)', [
                                    'old_id' => $legacyData['id'] ?? 'unknown',
                                ]);
                                $progressBar->advance();
                                continue;
                            }
                        }

                        // Persiste si pas en mode dry-run
                        if (!$dryRun) {
                            $this->em->persist($personnel);
                        }

                        $stats['success']++;
                    } catch (\Exception $e) {
                        $stats['errors']++;
                        $this->logger->error('Erreur lors de la migration d\'un enregistrement', [
                            'old_id' => $legacyData['id'] ?? 'unknown',
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
            $this->logger->critical('Erreur fatale lors de la migration Personnel', [
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
        $sql = "SELECT COUNT(*) as total FROM Personnel";



        if ($limit !== null) {
            $nombre = $this->legacyConnection->fetchOne($sql);
            return min($limit, (int) $nombre);
        }

        $nombre = $this->legacyConnection->fetchOne($sql);
        return (int) $nombre;
    }

    /**
     * Récupère un lot d'enregistrements de l'ancienne base de données
     * 
     * @param int $limit Nombre maximum d'enregistrements à récupérer
     * @param int $offset Position de départ pour la pagination
     * @return array Tableau unique contenant tous les enregistrements fusionnés
     * @throws \RuntimeException Si une erreur de base de données survient
     */
    private function fetchLegacyRecords(int $limit, int $offset): array
    {
        $sql = <<<SQL
                SELECT *
                FROM Personnel
                ORDER BY id
                OFFSET :offset ROWS
                FETCH NEXT :limit ROWS ONLY
        SQL;

        $parameters = [
            'offset' => $offset,
            'limit' => $limit,
        ];
        return  $this->legacyConnection->fetchAllAssociative($sql, $parameters);
    }


    /**
     * Affiche les statistiques de migration
     */
    private function displayStats(SymfonyStyle $io, array $stats, bool $dryRun): void
    {
        $io->section('Statistiques de migration');

        $tableData = [
            ['Total traité', $stats['total']],
            ['Succès (nouveaux)', sprintf('<fg=green>%d</>', $stats['success'] - $stats['updated'])],
            ['Mis à jour', sprintf('<fg=blue>%d</>', $stats['updated'])],
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
