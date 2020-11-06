<?php

namespace App\Tests\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\HttpFoundation\Response;

class PageControllerTest extends WebTestCase
{
    public function testHome(): void
    {
        $client = static::createClient();
        $client->request('GET', '');
        $this->assertEquals(Response::HTTP_OK, $client->getResponse()->getStatusCode());
        $this->assertPageTitleContains('Développeur Web Symfony - Angers');
        $this->assertSelectorTextContains('h1', 'Dylan ALIZON');
    }

    public function testCareer(): void
    {
        $client = static::createClient();
        $client->request('GET', '/mon-parcours');
        $response = $client->getResponse();
        $this->assertEquals(Response::HTTP_OK, $client->getResponse()->getStatusCode());
        $this->assertPageTitleContains('Mon parcours');
        $this->assertSelectorTextContains('h1', 'Mon parcours');
    }
}
