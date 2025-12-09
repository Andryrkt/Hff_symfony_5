<?php

namespace App\Tests\Security;

use App\Entity\Admin\PersonnelUser\User;
use App\Entity\Admin\ApplicationGroupe\Group;
use PHPUnit\Framework\TestCase;

class GroupTest extends TestCase
{
    public function testAddAndRemoveUser()
    {
        $user = new User();
        $group = new Group();

        $this->assertCount(0, $group->getUsers());

        $group->addUser($user);
        $this->assertCount(1, $group->getUsers());
        $this->assertTrue($group->getUsers()->contains($user));

        $group->removeUser($user);
        $this->assertCount(0, $group->getUsers());
    }
}
