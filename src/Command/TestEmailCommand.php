<?php

namespace App\Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\Mailer\Exception\TransportExceptionInterface;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Email;

class TestEmailCommand extends Command
{
    protected static $defaultName = 'app:test-email';
    private $mailer;

    public function __construct(MailerInterface $mailer)
    {
        parent::__construct();
        $this->mailer = $mailer;
    }

    protected function configure(): void
    {
        $this
            ->setDescription('Envoie un email de test pour vérifier la configuration du mailer.')
            ->addArgument('to', InputArgument::OPTIONAL, 'Adresse email du destinataire', 'test@example.com');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);
        $to = $input->getArgument('to');

        $email = (new Email())
            ->from('no-reply@example.com')
            ->to($to)
            ->subject('Email de test depuis la commande Symfony')
            ->text('Ceci est un email de test envoyé depuis votre application Symfony.')
            ->html('<p>Ceci est un <strong>email de test</strong> envoyé depuis votre application Symfony.</p>');

        try {
            $this->mailer->send($email);
            $io->success(sprintf('Email de test envoyé avec succès à %s !', $to));
            $io->note('Veuillez vérifier votre boîte de réception Mailtrap.');

            return Command::SUCCESS;
        } catch (TransportExceptionInterface $e) {
            $io->error('Une erreur est survenue lors de l\'envoi de l\'email.');
            $io->writeln($e->getMessage());

            return Command::FAILURE;
        }
    }
}
