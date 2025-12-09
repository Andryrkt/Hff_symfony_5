<?php

namespace App\Tests\Unit\Security\Voter;

use App\Entity\Admin\PersonnelUser\User;
use App\Security\Voter\ObjectVoter;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\AuthorizationCheckerInterface;
use Symfony\Component\Security\Core\Authorization\Voter\VoterInterface;

class ObjectVoterTest extends TestCase
{
    private ObjectVoter $voter;
    private AuthorizationCheckerInterface $authChecker;

    protected function setUp(): void
    {
        $this->authChecker = $this->createMock(AuthorizationCheckerInterface::class);
        $this->voter = new ObjectVoter($this->authChecker);
    }

    public function testSupportsObjectWithValidAction(): void
    {
        $subject = new \stdClass();
        $user = new User();
        $token = $this->createToken($user);
        
        $result = $this->voter->vote($token, $subject, ['VIEW']);
        
        // Should not abstain for valid actions on objects
        $this->assertNotEquals(VoterInterface::ACCESS_ABSTAIN, $result);
    }

    public function testAbstainsForNonObjectSubject(): void
    {
        $user = new User();
        $token = $this->createToken($user);
        
        $result = $this->voter->vote($token, 'not_an_object', ['VIEW']);
        
        $this->assertEquals(VoterInterface::ACCESS_ABSTAIN, $result);
    }

    public function testAbstainsForInvalidAction(): void
    {
        $subject = new \stdClass();
        $user = new User();
        $token = $this->createToken($user);
        
        $result = $this->voter->vote($token, $subject, ['INVALID_ACTION']);
        
        $this->assertEquals(VoterInterface::ACCESS_ABSTAIN, $result);
    }

    public function testDeniesForUnsupportedObjectClass(): void
    {
        $subject = new \stdClass(); // Not in the map
        $user = new User();
        $token = $this->createToken($user);
        
        $result = $this->voter->vote($token, $subject, ['VIEW']);
        
        // Since stdClass is not in the map, should be denied
        $this->assertEquals(VoterInterface::ACCESS_DENIED, $result);
    }

    public function testNonAuthenticatedUserIsDenied(): void
    {
        $token = $this->createMock(TokenInterface::class);
        $token->method('getUser')->willReturn(null);
        
        $subject = new \stdClass();
        
        $result = $this->voter->vote($token, $subject, ['VIEW']);
        
        $this->assertEquals(VoterInterface::ACCESS_DENIED, $result);
    }

    /**
     * Test that supported actions are correctly identified
     */
    public function testSupportedActions(): void
    {
        $supportedActions = ['VIEW', 'EDIT', 'DELETE', 'VALIDATE', 'CREATE'];
        $subject = new \stdClass();
        $user = new User();
        $token = $this->createToken($user);
        
        foreach ($supportedActions as $action) {
            $result = $this->voter->vote($token, $subject, [$action]);
            // Should not abstain for supported actions
            $this->assertNotEquals(
                VoterInterface::ACCESS_ABSTAIN,
                $result,
                "Action $action should be supported"
            );
        }
    }

    private function createToken(User $user): TokenInterface
    {
        $token = $this->createMock(TokenInterface::class);
        $token->method('getUser')->willReturn($user);
        
        return $token;
    }
}
