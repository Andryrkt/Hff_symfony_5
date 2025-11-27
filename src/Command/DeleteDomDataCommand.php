<?php

namespace App\Command;

use App\Entity\Hf\Rh\Dom\Dom;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

class DeleteDomDataCommand extends Command
{
    protected static $defaultName = 'app:delete-dom-data';
    protected static $defaultDescription = 'Supprime toutes les données de la table Dom';

    private EntityManagerInterface $entityManager;

    public function __construct(EntityManagerInterface $entityManager)
    {
        parent::__construct();
        $this->entityManager = $entityManager;
    }

    protected function configure(): void
    {
        // Aucun argument ou option nécessaire pour cette commande
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);

        $io->note('Suppression de toutes les données de la table Dom...');

        $this->entityManager->createQuery('DELETE FROM ' . Dom::class)->execute();
        $this->entityManager->flush();

        $io->success('Toutes les données de la table Dom ont été supprimées avec succès.');

        return Command::SUCCESS;
    }
}
