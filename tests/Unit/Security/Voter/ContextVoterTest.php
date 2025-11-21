<?php

namespace App\Tests\Unit\Security\Voter;

use App\Entity\Admin\AgenceService\Agence;
use App\Entity\Admin\AgenceService\Service;
use App\Entity\Admin\PersonnelUser\User;
use App\Entity\Admin\PersonnelUser\UserAccess;
use App\Security\Voter\ContextVoter;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\VoterInterface;

class ContextVoterTest extends TestCase
{
    private ContextVoter $voter;

    protected function setUp(): void
    {
        $this->voter = new ContextVoter();
    }

    public function testSupportsContextAccessAttribute(): void
    {
        $agence = $this->createMock(Agence::class);
        $service = $this->createMock(Service::class);
        
        $user = new User();
        $token = $this->createToken($user);
        
        $result = $this->voter->vote($token, [$agence, $service], [ContextVoter::ACCESS]);
        
        $this->assertNotEquals(VoterInterface::ACCESS_ABSTAIN, $result);
    }

    public function testAdminUserHasFullAccess(): void
    {
        $user = new User();
        $user->setRoles(['ROLE_ADMIN']);
        
        $agence = $this->createMock(Agence::class);
        $service = $this->createMock(Service::class);
        
        $token = $this->createToken($user);
        
        $result = $this->voter->vote($token, [$agence, $service], [ContextVoter::ACCESS]);
        
        $this->assertEquals(VoterInterface::ACCESS_GRANTED, $result);
    }

    public function testUserWithAllAccessIsGranted(): void
    {
        $user = new User();
        
        $userAccess = $this->createMock(UserAccess::class);
        $userAccess->method('getAllAgence')->willReturn(true);
        $userAccess->method('getAllService')->willReturn(true);
        
        $user->addUserAccess($userAccess);
        
        $agence = $this->createMock(Agence::class);
        $service = $this->createMock(Service::class);
        
        $token = $this->createToken($user);
        
        $result = $this->voter->vote($token, [$agence, $service], [ContextVoter::ACCESS]);
        
        $this->assertEquals(VoterInterface::ACCESS_GRANTED, $result);
    }

    public function testUserWithSpecificAgenceAccessIsGranted(): void
    {
        $user = new User();
        
        $agence = $this->createMock(Agence::class);
        $agence->method('getId')->willReturn(1);
        
        $service = $this->createMock(Service::class);
        $service->method('getId')->willReturn(10);
        
        $accessAgence = $this->createMock(Agence::class);
        $accessAgence->method('getId')->willReturn(1);
        
        $accessService = $this->createMock(Service::class);
        $accessService->method('getId')->willReturn(10);
        
        $userAccess = $this->createMock(UserAccess::class);
        $userAccess->method('getAllAgence')->willReturn(false);
        $userAccess->method('getAllService')->willReturn(false);
        $userAccess->method('getAgence')->willReturn($accessAgence);
        $userAccess->method('getService')->willReturn($accessService);
        
        $user->addUserAccess($userAccess);
        
        $token = $this->createToken($user);
        
        $result = $this->voter->vote($token, [$agence, $service], [ContextVoter::ACCESS]);
        
        $this->assertEquals(VoterInterface::ACCESS_GRANTED, $result);
    }

    public function testUserWithoutMatchingAccessIsDenied(): void
    {
        $user = new User();
        $user->setRoles(['ROLE_USER']);
        
        $agence = $this->createMock(Agence::class);
        $agence->method('getId')->willReturn(1);
        
        $service = $this->createMock(Service::class);
        $service->method('getId')->willReturn(10);
        
        $token = $this->createToken($user);
        
        $result = $this->voter->vote($token, [$agence, $service], [ContextVoter::ACCESS]);
        
        $this->assertEquals(VoterInterface::ACCESS_DENIED, $result);
    }

    public function testNonAuthenticatedUserIsDenied(): void
    {
        $token = $this->createMock(TokenInterface::class);
        $token->method('getUser')->willReturn(null);
        
        $agence = $this->createMock(Agence::class);
        $service = $this->createMock(Service::class);
        
        $result = $this->voter->vote($token, [$agence, $service], [ContextVoter::ACCESS]);
        
        $this->assertEquals(VoterInterface::ACCESS_DENIED, $result);
    }

    private function createToken(User $user): TokenInterface
    {
        $token = $this->createMock(TokenInterface::class);
        $token->method('getUser')->willReturn($user);
        
        return $token;
    }
}
