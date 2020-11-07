<?php

namespace App\Tests\Entity;

use App\Entity\Image;
use DateTimeImmutable;
use PHPUnit\Framework\TestCase;
use Symfony\Component\HttpFoundation\File\File;

class ImageTest extends TestCase
{
    public function testImage(): void
    {
        $fileName = 'test file name';
        $createdAt = new DateTimeImmutable();
        $filesize = 100000;
        $image = new Image();
        $fileMock = $this->createMock(File::class);
        $image->setFileName($fileName);
        $image->setCreatedAt($createdAt);
        $image->setFileSize($filesize);
        $image->setFile($fileMock);
        $this->assertNull($image->getId());
        $this->assertSame($fileName, $image->getFileName());
        $this->assertSame($createdAt, $image->getCreatedAt());
        $this->assertSame($filesize, $image->getFileSize());
        $this->assertSame($fileMock, $image->getFile());
        $this->assertSame($fileName, (string) $image);
    }
}
