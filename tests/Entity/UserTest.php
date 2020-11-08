<?php

namespace App\Tests\Entity;

use App\Entity\User;
use PHPUnit\Framework\TestCase;

class UserTest extends TestCase
{
    public function testUser(): void
    {
        $email = 'email';
        $roles = ['ROLE_TEST'];
        $password = 'password';
        $user = new User();
        $user->setEmail($email);
        $user->setRoles($roles);
        $user->setPassword($password);
        $this->assertNull($user->getId());
        $this->assertSame($email, $user->getEmail());
        $this->assertSame($email, $user->getUsername());
        $this->assertSame(array_merge($roles, ['ROLE_USER']), $user->getRoles());
        $this->assertSame($password, $user->getPassword());
        $this->assertSame($email, (string) $user);
        $this->assertNull($user->getSalt());
        $this->assertNull($user->eraseCredentials());
    }
}
