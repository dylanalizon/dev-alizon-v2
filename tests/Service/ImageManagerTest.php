<?php

namespace App\Tests\Service;

use App\Repository\ImageRepository;
use App\Service\ImageManager;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

class ImageManagerTest extends TestCase
{
    /** @var MockObject|ImageRepository */
    private MockObject $imageRepository;

    protected function setUp(): void
    {
        $this->imageRepository = $this->createMock(ImageRepository::class);
    }

    public function testGetFolders(): void
    {
        $findYearsReturn = [
          ['year' => 2020, 'count' => rand(0, 100)],
          ['year' => 2021, 'count' => rand(0, 100)],
        ];
        $this->imageRepository
            ->expects($this->once())
            ->method('findYears')
            ->willReturn($findYearsReturn);
        $service = new ImageManager($this->imageRepository);
        $this->assertEquals($findYearsReturn, $service->getFolders());
    }

    public function testGetFoldersWithEmptyFolders(): void
    {
        $expected = [['year' => date('Y'), 'count' => 0]];
        $this->imageRepository
            ->expects($this->once())
            ->method('findYears')
            ->willReturn([]);
        $service = new ImageManager($this->imageRepository);
        $this->assertEquals($expected, $service->getFolders());
    }

    /**
     * @dataProvider provideSizes
     */
    public function testDisplaySize(int $size, string $expected): void
    {
        $service = new ImageManager($this->imageRepository);
        $this->assertEquals($expected, $service->displaySize($size));
    }

    public function provideSizes(): \Generator
    {
        yield [1000, '1k'];
        yield [10000000, '9.6M'];
    }
}
