<?php

namespace App\Tests\Security;

use App\Entity\User;
use App\Repository\UserRepository;
use App\Security\AppAuthenticator;
use App\Tests\TestHelper;
use Doctrine\ORM\EntityManagerInterface;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Symfony\Component\HttpFoundation\ParameterBag;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use Symfony\Component\Security\Core\Exception\CustomUserMessageAuthenticationException;
use Symfony\Component\Security\Core\Exception\InvalidCsrfTokenException;
use Symfony\Component\Security\Core\Security;
use Symfony\Component\Security\Core\User\UserProviderInterface;
use Symfony\Component\Security\Csrf\CsrfTokenManagerInterface;
use Symfony\Contracts\Translation\TranslatorInterface;

class AppAuthenticatorTest extends TestCase
{
    use TestHelper;

    /** @var EntityManagerInterface|MockObject */
    private MockObject $entityManager;

    /** @var UrlGeneratorInterface|MockObject */
    private MockObject $urlGenerator;

    /** @var CsrfTokenManagerInterface|MockObject */
    private MockObject $csrfTokenManager;

    /** @var UserPasswordEncoderInterface|MockObject */
    private MockObject $passwordEncoder;

    /** @var TranslatorInterface|MockObject */
    private MockObject $translator;

    /** @var AppAuthenticator */
    private AppAuthenticator $authenticator;

    protected function setUp(): void
    {
        $this->entityManager = $this->createMock(EntityManagerInterface::class);
        $this->urlGenerator = $this->createMock(UrlGeneratorInterface::class);
        $this->csrfTokenManager = $this->createMock(CsrfTokenManagerInterface::class);
        $this->passwordEncoder = $this->createMock(UserPasswordEncoderInterface::class);
        $this->translator = $this->createMock(TranslatorInterface::class);
        $this->authenticator = new AppAuthenticator(
            $this->entityManager,
            $this->urlGenerator,
            $this->csrfTokenManager,
            $this->passwordEncoder,
            $this->translator
        );
    }

    /**
     * @dataProvider provideTestSupports
     */
    public function testSupports(string $routeName, bool $methodIsPost, bool $expected): void
    {
        $requestMock = $this->createMock(Request::class);
        $requestMock->attributes = $this->createMock(ParameterBag::class);
        $requestMock
            ->attributes
            ->expects($this->once())
            ->method('get')
            ->with('_route')
            ->willReturn($routeName);
        $requestMock
            ->method('isMethod')
            ->with('POST')
            ->willReturn($methodIsPost);
        $this->assertEquals($expected, $this->authenticator->supports($requestMock));
    }

    public function testGetCredentials(): void
    {
        $email = 'email@email.test';
        $password = 'passw0rd';
        $csrfToken = 'aCsrfToken';
        $requestMock = $this->createMock(Request::class);
        $requestMock->request = $this->createMock(ParameterBag::class);
        $requestMock
            ->request
            ->expects($this->at(0))
            ->method('get')
            ->with('email')
            ->willReturn($email);
        $requestMock
            ->request
            ->expects($this->at(1))
            ->method('get')
            ->with('password')
            ->willReturn($password);
        $requestMock
            ->request
            ->expects($this->at(2))
            ->method('get')
            ->with('_csrf_token')
            ->willReturn($csrfToken);
        $sessionMock = $this->createMock(SessionInterface::class);
        $requestMock
            ->expects($this->once())
            ->method('getSession')
            ->willReturn($sessionMock);
        $sessionMock
            ->expects($this->once())
            ->method('set')
            ->with(Security::LAST_USERNAME, $email);
        $this->assertEquals([
            'email' => $email,
            'password' => $password,
            'csrf_token' => $csrfToken,
        ], $this->authenticator->getCredentials($requestMock));
    }

    public function testGetUser(): void
    {
        $credentials = [
            'email' => 'email@email.test',
            'csrf_token' => 'aCsrfToken',
        ];
        $this->csrfTokenManager
            ->expects($this->once())
            ->method('isTokenValid')
            ->with($credentials['csrf_token'])
            ->willReturn(true);
        $userRepositoryMock = $this->createMock(UserRepository::class);
        $this->entityManager
            ->expects($this->once())
            ->method('getRepository')
            ->with(User::class)
            ->willReturn($userRepositoryMock);
        $user = new User();
        $userRepositoryMock
            ->expects($this->once())
            ->method('findOneBy')
            ->with(['email' => $credentials['email']])
            ->willReturn($user);
        $provider = $this->createMock(UserProviderInterface::class);
        $this->assertSame($user, $this->authenticator->getUser($credentials, $provider));
    }

    public function testGetUserWithBadCsrfToken(): void
    {
        $credentials = [
            'csrf_token' => 'aCsrfToken',
        ];
        $this->csrfTokenManager
            ->expects($this->once())
            ->method('isTokenValid')
            ->with($credentials['csrf_token'])
            ->willReturn(false);
        $provider = $this->createMock(UserProviderInterface::class);
        $this->expectException(InvalidCsrfTokenException::class);
        $this->authenticator->getUser($credentials, $provider);
    }

    public function testGetUserWithNoUserFound(): void
    {
        $credentials = [
            'email' => 'email@email.test',
            'csrf_token' => 'aCsrfToken',
        ];
        $this->csrfTokenManager
            ->expects($this->once())
            ->method('isTokenValid')
            ->with($credentials['csrf_token'])
            ->willReturn(true);
        $userRepositoryMock = $this->createMock(UserRepository::class);
        $this->entityManager
            ->expects($this->once())
            ->method('getRepository')
            ->with(User::class)
            ->willReturn($userRepositoryMock);
        $userRepositoryMock
            ->expects($this->once())
            ->method('findOneBy')
            ->with(['email' => $credentials['email']])
            ->willReturn(null);
        $this->translator
            ->expects($this->once())
            ->method('trans')
            ->with('errors.bad_credentials')
            ->willReturn('');
        $this->expectException(CustomUserMessageAuthenticationException::class);
        $provider = $this->createMock(UserProviderInterface::class);
        $this->authenticator->getUser($credentials, $provider);
    }

    public function testCheckCredentials(): void
    {
        $credentials = ['password' => 'passw0rd'];
        $user = new User();
        $this->passwordEncoder
            ->expects($this->once())
            ->method('isPasswordValid')
            ->with($this->isInstanceOf(User::class), $credentials['password']);
        $this->authenticator->checkCredentials($credentials, $user);
    }

    public function testGetPassword(): void
    {
        $credentials = ['password' => 'passw0rd'];
        $this->assertEquals($credentials['password'], $this->authenticator->getPassword($credentials));
    }

    public function testOnAuthenticationSuccess(): void
    {
        $requestMock = $this->createMock(Request::class);
        $tokenMock = $this->createMock(TokenInterface::class);
        $providerKey = 'key';
        $sessionMock = $this->createMock(SessionInterface::class);
        $sessionMock
            ->expects($this->once())
            ->method('get')
            ->with("_security.$providerKey.target_path")
            ->willReturn(null);
        $requestMock
            ->expects($this->once())
            ->method('getSession')
            ->willReturn($sessionMock);
        $this->urlGenerator
            ->expects($this->once())
            ->method('generate')
            ->with('home')
            ->willReturn('routeToHome');
        $result = $this->authenticator->onAuthenticationSuccess($requestMock, $tokenMock, $providerKey);
        $this->assertInstanceOf(RedirectResponse::class, $result);
        $this->assertEquals('routeToHome', $result->getTargetUrl());
    }

    public function testOnAuthenticationSuccessWithTargetPath(): void
    {
        $requestMock = $this->createMock(Request::class);
        $tokenMock = $this->createMock(TokenInterface::class);
        $providerKey = 'key';
        $sessionMock = $this->createMock(SessionInterface::class);
        $sessionMock
            ->expects($this->once())
            ->method('get')
            ->with("_security.$providerKey.target_path")
            ->willReturn('targetPath');
        $requestMock
            ->expects($this->once())
            ->method('getSession')
            ->willReturn($sessionMock);
        $result = $this->authenticator->onAuthenticationSuccess($requestMock, $tokenMock, $providerKey);
        $this->assertInstanceOf(RedirectResponse::class, $result);
        $this->assertEquals('targetPath', $result->getTargetUrl());
    }

    public function testGetLoginUrl(): void
    {
        $this->urlGenerator
            ->expects($this->once())
            ->method('generate')
            ->with('app_login');
        $this->invokeMethod($this->authenticator, 'getLoginUrl');
    }

    public function provideTestSupports(): \Generator
    {
        yield ['app_login', true, true];
        yield ['test', true, false];
        yield ['test', false, false];
        yield ['app_login', false, false];
    }
}
