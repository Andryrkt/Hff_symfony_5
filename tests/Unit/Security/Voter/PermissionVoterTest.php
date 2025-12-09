<?php

namespace App\Tests\Unit\Security\Voter;

use App\Entity\Admin\ApplicationGroupe\Permission;
use App\Entity\Admin\PersonnelUser\User;
use App\Entity\Admin\PersonnelUser\UserAccess;
use App\Security\Voter\PermissionVoter;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\VoterInterface;

class PermissionVoterTest extends TestCase
{
    private PermissionVoter $voter;

    protected function setUp(): void
    {
        $this->voter = new PermissionVoter();
    }

    public function testSupportsStringAttribute(): void
    {
        $this->assertTrue($this->voter->vote(
            $this->createToken(new User()),
            null,
            ['RH_CONGE_CREATE']
        ) !== VoterInterface::ACCESS_ABSTAIN);
    }

    public function testUserWithDirectPermissionIsGranted(): void
    {
        $user = new User();
        
        $permission = $this->createMock(Permission::class);
        $permission->method('getCode')->willReturn('RH_CONGE_CREATE');
        
        // Simuler la collection de permissions
        $user->addPermissionsDirecte($permission);
        
        $token = $this->createToken($user);
        
        $result = $this->voter->vote($token, null, ['RH_CONGE_CREATE']);
        
        $this->assertEquals(VoterInterface::ACCESS_GRANTED, $result);
    }

    public function testUserWithAdminRoleIsGranted(): void
    {
        $user = new User();
        $user->setRoles(['ROLE_ADMIN']);
        
        $token = $this->createToken($user);
        
        $result = $this->voter->vote($token, null, ['ANY_PERMISSION']);
        
        $this->assertEquals(VoterInterface::ACCESS_GRANTED, $result);
    }

    public function testUserWithoutPermissionIsDenied(): void
    {
        $user = new User();
        $user->setRoles(['ROLE_USER']);
        
        $token = $this->createToken($user);
        
        $result = $this->voter->vote($token, null, ['RH_CONGE_CREATE']);
        
        $this->assertEquals(VoterInterface::ACCESS_DENIED, $result);
    }

    public function testNonAuthenticatedUserIsDenied(): void
    {
        $token = $this->createMock(TokenInterface::class);
        $token->method('getUser')->willReturn(null);
        
        $result = $this->voter->vote($token, null, ['RH_CONGE_CREATE']);
        
        $this->assertEquals(VoterInterface::ACCESS_DENIED, $result);
    }

    public function testUserWithPermissionViaUserAccessIsGranted(): void
    {
        $user = new User();
        
        $permission = $this->createMock(Permission::class);
        $permission->method('getCode')->willReturn('RH_CONGE_VIEW');
        
        $userAccess = $this->createMock(UserAccess::class);
        $userAccess->method('getPermissions')->willReturn(
            new \Doctrine\Common\Collections\ArrayCollection([$permission])
        );
        $userAccess->method('getAllAgence')->willReturn(true);
        $userAccess->method('getAllService')->willReturn(true);
        
        $user->addUserAccess($userAccess);
        
        $token = $this->createToken($user);
        
        $result = $this->voter->vote($token, null, ['RH_CONGE_VIEW']);
        
        $this->assertEquals(VoterInterface::ACCESS_GRANTED, $result);
    }

    private function createToken(User $user): TokenInterface
    {
        $token = $this->createMock(TokenInterface::class);
        $token->method('getUser')->willReturn($user);
        
        return $token;
    }
}
