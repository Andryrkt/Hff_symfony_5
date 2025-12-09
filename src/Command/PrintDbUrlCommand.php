<?php

namespace App\Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;

class PrintDbUrlCommand extends Command
{
    protected static $defaultName = 'app:print-db-url';

    private $params;

    public function __construct(ParameterBagInterface $params)
    {
        $this->params = $params;
        parent::__construct();
    }

    protected function configure()
    {
        $this
            ->setDescription('Prints the DATABASE_URL.');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $databaseUrl = $_ENV['DATABASE_URL'] ?? null;

        if ($databaseUrl) {
            $output->writeln($databaseUrl);
        } else {
            $output->writeln('DATABASE_URL is not set.');
        }

        return Command::SUCCESS;
    }
}
