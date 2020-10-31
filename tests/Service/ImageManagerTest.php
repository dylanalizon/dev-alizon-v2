<?php

namespace App\Tests\Service;

use App\Repository\ImageRepository;
use App\Service\ImageManager;
use PHPUnit\Framework\TestCase;

class ImageManagerTest extends TestCase
{
    /** @var ImageRepository|PHPUnit_Framework_MockObject_MockObject */
    private $imageRepository;

    protected function setUp(): void
    {
        $this->imageRepository = $this->getMockBuilder(ImageRepository::class)
            ->disableOriginalConstructor()
            ->getMock();
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
    public function testDisplaySize($size, $expected): void
    {
        $service = new ImageManager($this->imageRepository);
        $this->assertEquals($expected, $service->displaySize($size));
    }

    public function provideSizes(): array
    {
        return [
            [1000, '1k'],
            [10000000, '9.6M'],
        ];
    }
}
