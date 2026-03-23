<?php

namespace App\Command\Delete\Hf\Materiel\Badm;

use Psr\Log\LoggerInterface;
use Doctrine\ORM\EntityManagerInterface;
use App\Entity\Hf\Materiel\Badm\Badm;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class DeleteBadmDataCommand extends Command
{
    protected static $defaultName = 'app:delete-badm-data';
    protected static $defaultDescription = 'Supprime toutes les données de la table Badm';

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
        $this->logger->info('Début de la suppression des données Badm.');

        try {
            $this->logger->info('Suppression des données de la table Badm...');
            // Utilisation de DQL pour supprimer toutes les entités Badm
            $this->entityManager->createQuery('DELETE FROM ' . Badm::class)->execute();
        } catch (\Exception $e) {
            $this->logger->error('Une erreur est survenue lors de la suppression des données Badm.', ['exception' => $e]);
            $io->error('Une erreur est survenue. Consultez les logs pour plus de détails.');
            return Command::FAILURE;
        }

        $this->logger->info('Toutes les données de la table Badm ont été supprimées avec succès.');
        $io->success('Suppression des données BADM terminée avec succès.');

        return Command::SUCCESS;
    }
}
