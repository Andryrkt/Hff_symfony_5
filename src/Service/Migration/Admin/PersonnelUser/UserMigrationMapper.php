<?php

namespace App\Service\Migration\Admin\PersonnelUser;

use App\Entity\Admin\PersonnelUser\Personnel;
use Psr\Log\LoggerInterface;
use App\Entity\Admin\PersonnelUser\User;
use App\Service\Migration\Utils\EntityRelationMapper;
use Doctrine\ORM\EntityManagerInterface;

class UserMigrationMapper
{
    private EntityManagerInterface $em;
    private EntityRelationMapper $relationMapper;
    private LoggerInterface $logger;

    public function __construct(
        EntityManagerInterface $em,
        LoggerInterface $logger,
        EntityRelationMapper $relationMapper
    ) {
        $this->em = $em;
        $this->logger = $logger;
        $this->relationMapper = $relationMapper;
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
        $user->setUsername($oldData['nom_utilisateur'] ?? '');
        $user->setEmail($oldData['mail'] ?? '');
        $user->setRoles(["ROLE_USER"]);
        $user->setMatricule($oldData['matricule'] ?? '');
    }

    private function mapRelations(User $user, array $oldData): void
    {
        //personnel
        $personnel = $this->em->getRepository(Personnel::class)->findOneBy(['matricule' => $oldData['matricule']]);
        if ($personnel) {
            $user->setPersonnel($personnel);
        }

        // fullname
        $fullname = $this->relationMapper->mapFullName($oldData['matricule']);
        $user->setFullname($fullname);
    }

    /**
     * Trouve un User existant par username pour éviter les doublons
     */
    public function findExistingByUsername(string $username): ?User
    {
        return $this->em->getRepository(User::class)->findOneBy(['username' => $username]);
    }

    /**
     * Met à jour un User existant
     */
    public function updateExisting(User $user, array $oldData): User
    {
        $this->mapSimpleFields($user, $oldData);
        $this->mapRelations($user, $oldData);

        return $user;
    }
}
