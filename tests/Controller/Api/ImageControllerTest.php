<?php

namespace App\Tests\Controller\Api;

use App\Entity\Image;
use App\Repository\ImageRepository;
use App\Repository\UserRepository;
use Symfony\Bundle\FrameworkBundle\KernelBrowser;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\Response;

class ImageControllerTest extends WebTestCase
{
    protected KernelBrowser $client;

    public function setUp(): void
    {
        $this->client = static::createClient();
        $userRepository = static::$container->get(UserRepository::class);
        $testUser = $userRepository->findOneBy([]);
        $this->client->loginUser($testUser);
    }

    public function testList(): void
    {
        $year = date('Y');
        $this->client->request('GET', "api/images/year/$year");
        $response = $this->client->getResponse();
        $this->assertEquals(Response::HTTP_OK, $response->getStatusCode());
        $content = json_decode($response->getContent(), true);
        $this->assertIsArray($content);
        $this->assertCount(2, $content);
        $firstImage = $content[0];
        $this->assertArrayHasKey('id', $firstImage);
        $this->assertArrayHasKey('name', $firstImage);
        $this->assertArrayHasKey('size', $firstImage);
        $this->assertArrayHasKey('url', $firstImage);
        $this->assertArrayHasKey('year', $firstImage);
    }

    public function testFolders(): void
    {
        $this->client->request('GET', "api/images/folders");
        $response = $this->client->getResponse();
        $this->assertEquals(Response::HTTP_OK, $response->getStatusCode());
        $content = json_decode($response->getContent(), true);
        $this->assertIsArray($content);
        $this->assertCount(2, $content);
        $firstFolder = $content[0];
        $this->assertArrayHasKey('year', $firstFolder);
        $this->assertArrayHasKey('count', $firstFolder);
    }

    public function testCreate(): void
    {
        $image = tempnam(sys_get_temp_dir(), 'test');
        imagejpeg(imagecreatetruecolor(10, 10), $image);
        $file = new UploadedFile($image, 'image_fixture.jpg');
        $this->client->request('POST', '/api/images', [], [
           'file' => $file
        ]);
        $response = $this->client->getResponse();
        $this->assertEquals(Response::HTTP_CREATED, $response->getStatusCode());
        $content = json_decode($response->getContent(), true);
        $this->assertIsArray($content);
        $this->assertArrayHasKey('id', $content);
        $this->assertArrayHasKey('name', $content);
        $this->assertArrayHasKey('size', $content);
        $this->assertArrayHasKey('url', $content);
        $this->assertArrayHasKey('year', $content);
        // Remove the file from the disk
        $imageRepository = static::$container->get(ImageRepository::class);
        /** @var Image $image */
        $image = $imageRepository->find($content['id']);
        $this->assertInstanceOf(Image::class, $image);
        unlink(static::$kernel->getProjectDir() . "/public/uploads/images/{$content['year']}/{$image->getFileName()}");
    }

    public function testCreateWithoutFile(): void
    {
        $this->client->request('POST', '/api/images');
        dd($this->client->getResponse());
    }

    public function testDelete(): void
    {
        $imageRepository = static::$container->get(ImageRepository::class);
        /** @var Image $image */
        $image = $imageRepository->findOneBy([]);
        $this->client->request('DELETE', "api/images/{$image->getId()}");
        $response = $this->client->getResponse();
        $this->assertEquals(Response::HTTP_NO_CONTENT, $response->getStatusCode());
        $images = $imageRepository->findAll();
        $this->assertCount(2, $images);
    }
}
