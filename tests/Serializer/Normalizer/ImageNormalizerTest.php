<?php

namespace App\Tests\Serializer\Normalizer;

use App\Entity\Image;
use App\Serializer\Normalizer\ImageNormalizer;
use App\Service\ImageManager;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Serializer\Normalizer\ObjectNormalizer;
use Vich\UploaderBundle\Templating\Helper\UploaderHelper;

class ImageNormalizerTest extends TestCase
{
    /** @var MockObject|ObjectNormalizer */
    private MockObject $normalizer;

    /** @var MockObject|UploaderHelper */
    private MockObject $uploaderHelper;

    /** @var MockObject|ImageManager */
    private MockObject $imageManager;

    public function setUp(): void
    {
        $this->normalizer = $this->createMock(ObjectNormalizer::class);
        $this->uploaderHelper = $this->createMock(UploaderHelper::class);
        $this->imageManager = $this->createMock(ImageManager::class);
    }

    public function testNormalize(): void
    {
        $image = new Image();
        $this->normalizer
            ->expects($this->once())
            ->method('normalize')
            ->with($this->isInstanceOf(Image::class))
            ->willReturn([]);

        $normalizer = new ImageNormalizer($this->normalizer, $this->uploaderHelper, $this->imageManager);
        $result = $normalizer->normalize($image);
        $this->assertEquals([], $result);
    }

    public function testNormalizeWithImageReadGroup(): void
    {
        $datetime = new \DateTime();
        $image = new Image();
        $image->setFileName('image-test-hash123.jpg');
        $image->setFileSize(1000);
        $image->setCreatedAt($datetime);
        $context = ['groups' => 'image:read'];
        $expected = [
            'id' => 1,
            'url' => '/resize/xs/url',
            'name' => 'image-test.jpg',
            'size' => 'a size',
            'year' => $datetime->format('Y'),
        ];
        $this->normalizer
            ->expects($this->once())
            ->method('normalize')
            ->with($this->isInstanceOf(Image::class))
            ->willReturn(['id' => 1]);
        $this->uploaderHelper
            ->expects($this->once())
            ->method('asset')
            ->with($this->isInstanceOf(Image::class))
            ->willReturn('/url');
        $this->imageManager
            ->expects($this->once())
            ->method('displaySize')
            ->with($this->equalTo($image->getFileSize()))
            ->willReturn('a size');

        $normalizer = new ImageNormalizer($this->normalizer, $this->uploaderHelper, $this->imageManager);
        $result = $normalizer->normalize($image, null, $context);
        $this->assertEquals($expected, $result);
    }

    public function testSupportsNormalizationOk(): void
    {
        $image = new Image();
        $normalizer = new ImageNormalizer($this->normalizer, $this->uploaderHelper, $this->imageManager);
        $this->assertTrue($normalizer->supportsNormalization($image));
    }

    public function testSupportsNormalizationKo(): void
    {
        $object = new \stdClass();
        $normalizer = new ImageNormalizer($this->normalizer, $this->uploaderHelper, $this->imageManager);
        $this->assertFalse($normalizer->supportsNormalization($object));
    }

    public function testHasCacheableSupportsMethod(): void
    {
        $normalizer = new ImageNormalizer($this->normalizer, $this->uploaderHelper, $this->imageManager);
        $this->assertTrue($normalizer->hasCacheableSupportsMethod());
    }
}
