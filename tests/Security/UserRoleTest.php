<?php

namespace App\Tests\Security;

use App\Entity\Admin\PersonnelUser\User;
use PHPUnit\Framework\TestCase;

class UserRoleTest extends TestCase
{
    public function testRoles()
    {
        $user = new User();
        $user->setRoles(['ROLE_ADMIN', 'ROLE_USER']);
        $roles = $user->getRoles();

        $this->assertContains('ROLE_ADMIN', $roles);
        $this->assertContains('ROLE_USER', $roles);
        $this->assertContains('ROLE_USER', $roles); // Symfony ajoute toujours ROLE_USER
    }
}
