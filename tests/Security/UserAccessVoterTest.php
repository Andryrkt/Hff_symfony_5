<?php

namespace App\Tests\Security;

use App\Entity\Admin\PersonnelUser\User;
use App\Entity\Admin\ApplicationGroupe\Group;
use App\Entity\Admin\ApplicationGroupe\Application;
use App\Entity\Admin\ApplicationGroupe\GroupAccess;
use App\Security\Voter\UserAccessVoter;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Security\Core\Authentication\Token\UsernamePasswordToken;

class UserAccessVoterTest extends TestCase
{
    public function testUserHasPermissionViaGroup()
    {
        // Créer l'application
        $application = new Application();
        $application->setName('RH');

        // Créer le groupe avec des droits
        $group = new Group();
        $group->setName('RH');
        
        // Créer un GroupAccess pour ce groupe
        $groupAccess = new GroupAccess();
        $groupAccess->setGroup($group);
        $groupAccess->setApplication($application);
        $groupAccess->setAccessType('ALL');
        $group->getGroupAccesses()->add($groupAccess);

        // Créer l'utilisateur et l'ajouter au groupe
        $user = new User();
        $user->addGroup($group);

        $voter = new UserAccessVoter();
        $token = new UsernamePasswordToken($user, 'memory');

        $result = $voter->vote($token, $application, ['VIEW']);
        $this->assertEquals(UserAccessVoter::ACCESS_GRANTED, $result);
    }
}
