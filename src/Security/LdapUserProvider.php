<?php

namespace App\Security;

use App\Entity\Admin\PersonnelUser\User;
use App\Repository\Admin\PersonnelUser\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Ldap\LdapInterface;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Core\User\UserProviderInterface;
use Symfony\Component\Security\Core\Exception\UserNotFoundException;

class LdapUserProvider implements UserProviderInterface
{
    private $ldap;
    private $baseDn;
    private $uidKey;
    private $defaultRoles;
    private $searchDn;
    private $searchPassword;
    private $userRepository;
    private $em;

    public function __construct(
        LdapInterface $ldap,
        string $baseDn,
        string $uidKey,
        string $defaultRoles,
        string $searchDn,
        string $searchPassword,
        UserRepository $userRepository,
        EntityManagerInterface $em
    ) {
        $this->ldap = $ldap;
        $this->baseDn = $baseDn;
        $this->uidKey = $uidKey;
        $this->defaultRoles = $defaultRoles;
        $this->searchDn = $searchDn;
        $this->searchPassword = $searchPassword;
        $this->userRepository = $userRepository;
        $this->em = $em;
    }

    public function loadUserByIdentifier(string $identifier): UserInterface
    {
        $this->ldap->bind($this->searchDn, $this->searchPassword);

        $query = $this->ldap->query($this->baseDn, sprintf('(%s=%s)', $this->uidKey, $identifier));
        
        if (!$query) {
            throw new UserNotFoundException(sprintf('Requête LDAP invalide pour l\'utilisateur "%s".', $identifier));
        }
        
        $results = $query->execute();

        if (count($results) === 0) {
            throw new UserNotFoundException(sprintf('Utilisateur LDAP "%s" non trouvé.', $identifier));
        }

        $entry = $results[0];

        // Rechercher ou créer l'utilisateur local
        $user = $this->userRepository->findOneBy(['username' => $identifier]);

        if (!$user) {
            $user = new User();
            $user->setUsername($identifier);
        }

        // Synchronisation des données LDAP
        $user->setFullname($entry->getAttribute('cn')[0] ?? null);
        $user->setEmail($entry->getAttribute('mail')[0] ?? null);
        $user->setRoles([$this->defaultRoles]);

        $this->em->persist($user);
        $this->em->flush();

        return $user;
    }

    public function loadUserByUsername(string $username): UserInterface
    {
        return $this->loadUserByIdentifier($username);
    }

    public function refreshUser(UserInterface $user): UserInterface
    {
        return $user;
    }

    public function supportsClass(string $class): bool
    {
        return $class === User::class;
    }
}
