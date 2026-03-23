<?php

namespace App\Command\Delete\Admin;

use App\Entity\Admin\PersonnelUser\Personnel;
use App\Entity\Admin\PersonnelUser\User;
use App\Entity\Admin\PersonnelUser\UserAccess;
use Doctrine\ORM\EntityManagerInterface;
use Psr\Log\LoggerInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

class DeletePersonnelDataCommand extends Command
{
    protected static $defaultName = 'app:delete-personnel-data';
    protected static $defaultDescription = 'Supprime toutes les données des tables UserAccess, User et Personnel';

    private EntityManagerInterface $entityManager;
    private LoggerInterface $logger;

    public function __construct(EntityManagerInterface $entityManager, LoggerInterface $logger)
    {
        parent::__construct();
        $this->entityManager = $entityManager;
        $this->logger = $logger;
    }

    protected function configure(): void
    {
        // Aucun argument ou option nécessaire pour cette commande
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);
        $this->logger->info('Début de la suppression des données Personnel, User et UserAccess.');

        try {
            $this->logger->info('Suppression des données de la table UserAccess...');
            $this->entityManager->createQuery('DELETE FROM ' . UserAccess::class)->execute();

            $this->logger->info('Suppression des données de la table User...');
            $this->entityManager->createQuery('DELETE FROM ' . User::class)->execute();

            $this->logger->info('Suppression des données de la table Personnel...');
            $this->entityManager->createQuery('DELETE FROM ' . Personnel::class)->execute();

            // flush est inutile avec des requêtes DELETE DQL qui s'exécutent directement en base
            // $this->entityManager->flush();

        } catch (\Exception $e) {
            $this->logger->error('Une erreur est survenue lors de la suppression des données.', ['exception' => $e]);
            $io->error('Une erreur est survenue. Consultez le fichier de log "delete_personnel.log" pour plus de détails.');
            return Command::FAILURE;
        }

        $this->logger->info('Toutes les données des tables UserAccess, User et Personnel ont été supprimées avec succès.');
        $io->success('Suppression terminée avec succès. Les détails sont dans le fichier de log "delete_personnel.log".');

        return Command::SUCCESS;
    }
}
