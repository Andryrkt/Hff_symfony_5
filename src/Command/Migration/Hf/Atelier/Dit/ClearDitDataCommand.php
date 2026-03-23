<?php

namespace App\Command\Migration\Hf\Atelier\Dit;

use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\Console\Question\ConfirmationQuestion;

class ClearDitDataCommand extends Command
{
    protected static $defaultName = 'app:migrate:dit-clear';
    protected static $defaultDescription = 'Efface toutes les données de la table DIT (utile pour recommencer la migration)';

    private EntityManagerInterface $em;

    public function __construct(EntityManagerInterface $em)
    {
        parent::__construct();
        $this->em = $em;
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);
        $io->title('Suppression des données DIT');

        $helper = $this->getHelper('question');
        $question = new ConfirmationQuestion('<question>Êtes-vous sûr de vouloir effacer TOUTES les données de la table DIT ? (y/N)</question> ', false);

        if (!$input->getOption('no-interaction') && !$helper->ask($input, $output, $question)) {
            $io->warning('Opération annulée.');
            return Command::SUCCESS;
        }

        try {
            $connection = $this->em->getConnection();

            // On utilise DELETE au lieu de TRUNCATE au cas où il y aurait des contraintes
            $io->text('Suppression des enregistrements...');
            $connection->executeStatement('DELETE FROM dit');

            // Tentative de réinitialisation du compteur d'auto-incrément (SQL Server)
            try {
                $connection->executeStatement("DBCC CHECKIDENT ('dit', RESEED, 0)");
                $io->text('ID réinitialisé à 0.');
            } catch (\Exception $e) {
                // Ignore l'erreur si la commande DBCC n'est pas supportée ou si on n'a pas les droits
            }

            $io->success('Toutes les données de la table DIT ont été effacées avec succès !');

            return Command::SUCCESS;
        } catch (\Exception $e) {
            $io->error('Erreur lors de la suppression : ' . $e->getMessage());
            return Command::FAILURE;
        }
    }
}
