<?php

namespace App\Tests\Controller;

use App\Repository\UserRepository;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\HttpFoundation\Response;

class SecurityControllerTest extends WebTestCase
{
    public function testLogin(): void
    {
        $client = static::createClient();
        $client->request('GET', '/connexion');
        $this->assertEquals(Response::HTTP_OK, $client->getResponse()->getStatusCode());
        $this->assertPageTitleContains('Connexion');
        $this->assertSelectorTextContains('h1', 'Se connecter');
    }

    public function testLoginWithAuthenticatedUser(): void
    {
        $client = static::createClient();
        $userRepository = static::$container->get(UserRepository::class);
        $testUser = $userRepository->findOneBy([]);
        $client->loginUser($testUser);
        $client->request('GET', '/connexion');
        $this->assertResponseRedirects('/');
    }
}
