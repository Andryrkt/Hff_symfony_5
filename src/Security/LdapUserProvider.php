<?php

namespace App\Security;

use App\Entity\Admin\PersonnelUser\Personnel;
use App\Entity\Admin\PersonnelUser\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Ldap\LdapInterface;
use App\Repository\Admin\PersonnelUser\UserRepository;
use Symfony\Component\Security\Core\User\UserInterface;
use App\Repository\Admin\PersonnelUser\PersonnelRepository;
use Symfony\Component\Ldap\Entry;
use Symfony\Component\Security\Core\User\UserProviderInterface;

use Symfony\Component\Security\Core\Exception\UserNotFoundException;
use Symfony\Component\Security\Core\Exception\UnsupportedUserException;

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
            throw new UserNotFoundException(sprintf("Requête LDAP invalide pour l'utilisateur %s", $identifier));
        }

        $results = $query->execute();

        if (count($results) === 0) {
            throw new UserNotFoundException(sprintf('Utilisateur LDAP "%s" non trouvé.', $identifier));
        }

        $entry = $results[0];

        // Rechercher ou créer l'utilisateur local
        $user = $this->userRepository->findOneBy(['username' => $identifier]);

        if (!$user) {
            $user = $this->createUser($entry, $identifier);
        }

        return $user;
    }

    private function createUser(Entry $entry, string $identifier): User
    {
        $personnel = $this->em->getRepository(Personnel::class)->findOneBy([
            'nom' => $entry->getAttribute('sn')[0] ?? null
        ]);

        $user = new User();
        $user->setUsername($identifier);
        $user->setFullname($entry->getAttribute('cn')[0] ?? null);
        $user->setEmail($entry->getAttribute('mail')[0] ?? null);
        $user->setRoles([$this->defaultRoles]);
        $user->setPoste($entry->getAttribute('description')[0] ?? null);
        $user->setMatricule($personnel ? $personnel->getMatricule() : null);
        $user->setPersonnel($personnel);


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
        if (!$user instanceof User) {
            throw new UnsupportedUserException(sprintf('Instances of "%s" are not supported.', get_class($user)));
        }

        $reloadedUser = $this->userRepository->find($user->getId());

        if (null === $reloadedUser) {
            $e = new UserNotFoundException('User with id ' . $user->getId() . ' not found.');
            $e->setUserIdentifier($user->getUserIdentifier());
            throw $e;
        }

        return $reloadedUser;
    }

    public function supportsClass(string $class): bool
    {
        return $class === User::class;
    }
}
