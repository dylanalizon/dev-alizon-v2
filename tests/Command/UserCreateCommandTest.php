<?php

namespace App\Tests\Command;

use App\Command\UserCreateCommand;
use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use PHPUnit\Framework\MockObject\MockObject;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Component\Console\Application;
use Symfony\Component\Console\Tester\CommandTester;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class UserCreateCommandTest extends KernelTestCase
{
    /** @var MockObject|UserPasswordEncoderInterface  */
    private MockObject $passwordEncoder;

    /** @var MockObject|EntityManagerInterface */
    private MockObject $em;

    public function setUp(): void
    {
        $this->passwordEncoder = $this->createMock(UserPasswordEncoderInterface::class);
        $this->em = $this->createMock(EntityManagerInterface::class);
    }

    /**
     * @dataProvider provideUsers
     */
    public function testExecute(array $userData, int $expected): void
    {
        $this->passwordEncoder
            ->expects($this->once())
            ->method('encodePassword')
            ->with($this->isInstanceOf(User::class), $this->equalTo($userData['password']))
            ->willReturn('');

        $commandTester = $this->createCommandTester();
        $result = $commandTester->execute($userData);

        $this->assertEquals($expected, $result);
        $this->assertRegExp("/Created user {$userData['email']}/", $commandTester->getDisplay());
    }

    public function testInteract(): void
    {
        $this->passwordEncoder
            ->expects($this->once())
            ->method('encodePassword')
            ->with($this->isInstanceOf(User::class), $this->equalTo('passw0rd'))
            ->willReturn('');

        $commandTester = $this->createCommandTester();
        $commandTester->setInputs(['test@test.test', 'passw0rd']);
        $result = $commandTester->execute([], ['interactive' => true]);

        $this->assertEquals(0, $result);
        $this->assertRegExp("/Created user test@test.test/", $commandTester->getDisplay());
    }

    public function testInteractWithEmptyEmail(): void
    {
        $commandTester = $this->createCommandTester();
        $commandTester->setInputs(['']);
        $this->expectException(\Exception::class);
        $commandTester->execute([], ['interactive' => true]);
    }

    public function testInteractWithEmptyPassword(): void
    {
        $commandTester = $this->createCommandTester();
        $commandTester->setInputs(['test@test.test', '']);
        $this->expectException(\Exception::class);
        $commandTester->execute([], ['interactive' => true]);
    }

    private function createCommandTester(Application $application = null): CommandTester
    {
        if (null === $application) {
            $application = new Application();
        }
        $application->setAutoExit(false);
        $command = new UserCreateCommand($this->passwordEncoder, $this->em);
        $application->add($command);
        return new CommandTester($application->find('app:user:create'));
    }

    public function provideUsers(): \Generator
    {
        yield [['email' => 'test@test.test', 'password' => 'password'], 0];
        yield [['email' => 'test2@test2.test2', 'password' => 'password2', '--admin' => true], 0];
    }
}
