<?php

namespace App\Command;

use App\Entity\Admin\PersonnelUser\Personnel;
use App\Entity\Admin\PersonnelUser\User;
use App\Entity\Admin\PersonnelUser\UserAccess;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

class DeletePersonnelDataCommand extends Command
{
    protected static $defaultName = 'app:delete-personnel-data';
    protected static $defaultDescription = 'Supprime toutes les données des tables UserAccess, User et Personnel';

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

        $io->note('Suppression de toutes les données de la table UserAccess...');
        $this->entityManager->createQuery('DELETE FROM ' . UserAccess::class)->execute();

        $io->note('Suppression de toutes les données de la table User...');
        $this->entityManager->createQuery('DELETE FROM ' . User::class)->execute();

        $io->note('Suppression de toutes les données de la table Personnel...');
        $this->entityManager->createQuery('DELETE FROM ' . Personnel::class)->execute();
        
        $this->entityManager->flush();

        $io->success('Toutes les données des tables UserAccess, User et Personnel ont été supprimées avec succès.');

        return Command::SUCCESS;
    }
}
