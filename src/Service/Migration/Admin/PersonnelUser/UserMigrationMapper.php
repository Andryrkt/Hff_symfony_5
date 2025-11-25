<?php

namespace App\Service\Migration\Admin\PersonnelUser;

use Psr\Log\LoggerInterface;
use App\Entity\Admin\PersonnelUser\User;
use Doctrine\ORM\EntityManagerInterface;

class UserMigrationMapper
{
    private EntityManagerInterface $em;
    private LoggerInterface $logger;

    public function __construct(
        EntityManagerInterface $em,
        LoggerInterface $logger
    ) {
        $this->em = $em;
        $this->logger = $logger;
    }

    public function mapOldToNew(array $oldData): ?User
    {
        try {
            $user = new User();

            // Mapping des champs simples
            $this->mapSimpleFields($user, $oldData);

            // Mapping des relations
            $this->mapRelations($user, $oldData);

            return $user;
        } catch (\Exception $e) {
            $this->logger->error('Erreur lors du mapping des données utilisateur', [
                'error' => $e->getMessage(),
                'old_id' => $oldData['ID'] ?? 'unknown',
            ]);
            return null;
        }
    }

    private function mapSimpleFields(User $user, array $oldData): void
    {
        $user->setUsername($oldData['Username'] ?? '');
        $user->setEmail($oldData['Email'] ?? '');
        $user->setRoles($oldData['Roles'] ?? []);
    }

    private function mapRelations(User $user, array $oldData): void
    {
        // Relations
        // $user->setAgence($this->em->getRepository(Agence::class)->find($oldData['Agence_ID']));
        // $user->setService($this->em->getRepository(Service::class)->find($oldData['Service_ID']));
    }
}
