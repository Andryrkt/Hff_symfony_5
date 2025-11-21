<?php

namespace App\Tests\Unit\Security\Voter;

use App\Entity\Admin\ApplicationGroupe\Permission;
use App\Entity\Admin\ApplicationGroupe\Vignette;
use App\Entity\Admin\PersonnelUser\User;
use App\Entity\Admin\PersonnelUser\UserAccess;
use App\Security\Voter\VignetteVoter;
use Doctrine\Common\Collections\ArrayCollection;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\VoterInterface;

class VignetteVoterTest extends TestCase
{
    private VignetteVoter $voter;

    protected function setUp(): void
    {
        $this->voter = new VignetteVoter();
    }

    public function testSupportsApplicationAccessAttribute(): void
    {
        $vignette = $this->createMock(Vignette::class);
        $user = new User();
        $token = $this->createToken($user);
        
        $result = $this->voter->vote($token, $vignette, [VignetteVoter::ACCESS]);
        
        $this->assertNotEquals(VoterInterface::ACCESS_ABSTAIN, $result);
    }

    public function testAdminUserHasFullAccess(): void
    {
        $user = new User();
        $user->setRoles(['ROLE_ADMIN']);
        
        $vignette = $this->createMock(Vignette::class);
        $vignette->method('getNom')->willReturn('RH');
        
        $token = $this->createToken($user);
        
        $result = $this->voter->vote($token, $vignette, [VignetteVoter::ACCESS]);
        
        $this->assertEquals(VoterInterface::ACCESS_GRANTED, $result);
    }

    public function testUserWithDirectPermissionIsGranted(): void
    {
        $user = new User();
        
        $vignette = $this->createMock(Vignette::class);
        $vignette->method('getNom')->willReturn('RH');
        
        $permission = $this->createMock(Permission::class);
        $permission->method('getCode')->willReturn('RH_CONGE_VIEW');
        
        $user->addPermissionsDirecte($permission);
        
        $token = $this->createToken($user);
        
        $result = $this->voter->vote($token, $vignette, [VignetteVoter::ACCESS]);
        
        $this->assertEquals(VoterInterface::ACCESS_GRANTED, $result);
    }

    public function testUserWithPermissionViaUserAccessIsGranted(): void
    {
        $user = new User();
        
        $vignette = $this->createMock(Vignette::class);
        $vignette->method('getNom')->willReturn('RH');
        
        $permission = $this->createMock(Permission::class);
        $permission->method('getCode')->willReturn('RH_CONGE_CREATE');
        
        $userAccess = $this->createMock(UserAccess::class);
        $userAccess->method('getPermissions')->willReturn(
            new ArrayCollection([$permission])
        );
        
        $user->addUserAccess($userAccess);
        
        $token = $this->createToken($user);
        
        $result = $this->voter->vote($token, $vignette, [VignetteVoter::ACCESS]);
        
        $this->assertEquals(VoterInterface::ACCESS_GRANTED, $result);
    }

    public function testUserWithoutMatchingPermissionIsDenied(): void
    {
        $user = new User();
        $user->setRoles(['ROLE_USER']);
        
        $vignette = $this->createMock(Vignette::class);
        $vignette->method('getNom')->willReturn('RH');
        
        // User has permission for different vignette
        $permission = $this->createMock(Permission::class);
        $permission->method('getCode')->willReturn('FINANCE_BUDGET_VIEW');
        
        $user->addPermissionsDirecte($permission);
        
        $token = $this->createToken($user);
        
        $result = $this->voter->vote($token, $vignette, [VignetteVoter::ACCESS]);
        
        $this->assertEquals(VoterInterface::ACCESS_DENIED, $result);
    }

    public function testNonAuthenticatedUserIsDenied(): void
    {
        $token = $this->createMock(TokenInterface::class);
        $token->method('getUser')->willReturn(null);
        
        $vignette = $this->createMock(Vignette::class);
        
        $result = $this->voter->vote($token, $vignette, [VignetteVoter::ACCESS]);
        
        $this->assertEquals(VoterInterface::ACCESS_DENIED, $result);
    }

    public function testAbstainsForNonVignetteSubject(): void
    {
        $user = new User();
        $token = $this->createToken($user);
        
        $result = $this->voter->vote($token, new \stdClass(), [VignetteVoter::ACCESS]);
        
        $this->assertEquals(VoterInterface::ACCESS_ABSTAIN, $result);
    }

    private function createToken(User $user): TokenInterface
    {
        $token = $this->createMock(TokenInterface::class);
        $token->method('getUser')->willReturn($user);
        
        return $token;
    }
}
