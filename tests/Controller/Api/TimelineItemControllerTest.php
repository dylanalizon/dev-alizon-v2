<?php

namespace App\Tests\Controller\Api;

use App\Entity\TimelineItem;
use App\Repository\TimelineItemRepository;
use App\Repository\UserRepository;
use Symfony\Bundle\FrameworkBundle\KernelBrowser;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\HttpFoundation\Response;

class TimelineItemControllerTest extends WebTestCase
{
    protected KernelBrowser $client;

    public function setUp(): void
    {
        $this->client = static::createClient([], ['HTTP_ACCEPT' => 'application/json']);
        $userRepository = static::$container->get(UserRepository::class);
        $testUser = $userRepository->findOneBy([]);
        $this->client->loginUser($testUser);
    }

    public function testUpdate(): void
    {
        $timeLineItemRepository = static::$container->get(TimelineItemRepository::class);
        /** @var TimelineItem $timeLineItem */
        $timeLineItem = $timeLineItemRepository->findOneBy([]);
        $this->client->request('PUT', "/api/timeline-items/{$timeLineItem->getId()}", [], [], [],
            json_encode(['position' => 2])
        );
        $response = $this->client->getResponse();
        $this->assertSame(Response::HTTP_OK, $response->getStatusCode());
        $content = json_decode($response->getContent(), true);
        $this->assertIsArray($content);
        $this->assertArrayHasKey('id', $content);
        $this->assertArrayHasKey('title', $content);
        $this->assertArrayHasKey('description', $content);
        $this->assertArrayHasKey('position', $content);
        $this->assertArrayHasKey('subtitle', $content);
        $this->assertArrayHasKey('image', $content);
        $this->assertSame(2, $content['position']);
    }

    public function testUpdateWithoutContent(): void
    {
        $timeLineItemRepository = static::$container->get(TimelineItemRepository::class);
        /** @var TimelineItem $timeLineItem */
        $timeLineItem = $timeLineItemRepository->findOneBy([]);
        $this->client->request('PUT', "/api/timeline-items/{$timeLineItem->getId()}");
        $response = $this->client->getResponse();
        $this->assertSame(Response::HTTP_UNPROCESSABLE_ENTITY, $response->getStatusCode());
        $content = json_decode($response->getContent(), true);
        $this->assertIsArray($content);
        $this->assertArrayHasKey('code', $content);
        $this->assertArrayHasKey('errors', $content);
    }
}
