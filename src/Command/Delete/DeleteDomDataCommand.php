<?php

namespace App\Command\Delete;

use App\Entity\Hf\Rh\Dom\Dom;
use Doctrine\ORM\EntityManagerInterface;
use Psr\Log\LoggerInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

class DeleteDomDataCommand extends Command
{
    protected static $defaultName = 'app:delete-dom-data';
    protected static $defaultDescription = 'Supprime toutes les données de la table Dom';

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
        $this->logger->info('Début de la suppression des données Dom.');

        try {
            // Il pourrait y avoir des dépendances ici aussi. Si c'est le cas, il faudra les ajouter.
            // Pour l'instant, on suppose qu'il n'y en a pas.
            $this->logger->info('Suppression des données de la table Dom...');
            $this->entityManager->createQuery('DELETE FROM ' . Dom::class)->execute();

            // flush est inutile avec des requêtes DELETE DQL
        } catch (\Exception $e) {
            $this->logger->error('Une erreur est survenue lors de la suppression des données.', ['exception' => $e]);
            $io->error('Une erreur est survenue. Consultez le fichier de log "delete_dom.log" pour plus de détails.');
            return Command::FAILURE;
        }

        $this->logger->info('Toutes les données de la table Dom ont été supprimées avec succès.');
        $io->success('Suppression terminée avec succès. Les détails sont dans le fichier de log "delete_dom.log".');

        return Command::SUCCESS;
    }
}
