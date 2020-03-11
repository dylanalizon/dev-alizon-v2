<?php

namespace App\Serializer\Normalizer;

use App\Entity\Image;
use App\Service\ImageManager;
use Symfony\Component\Serializer\Normalizer\CacheableSupportsMethodInterface;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;
use Symfony\Component\Serializer\Normalizer\ObjectNormalizer;
use Vich\UploaderBundle\Templating\Helper\UploaderHelper;

class ImageNormalizer implements NormalizerInterface, CacheableSupportsMethodInterface
{
    /**
     * @var ObjectNormalizer
     */
    private $normalizer;

    /**
     * @var UploaderHelper
     */
    private $uploaderHelper;

    /**
     * @var ImageManager
     */
    private $imageManager;

    public function __construct(
        ObjectNormalizer $normalizer,
        UploaderHelper $uploaderHelper,
        ImageManager $imageManager
    ) {
        $this->normalizer = $normalizer;
        $this->uploaderHelper = $uploaderHelper;
        $this->imageManager = $imageManager;
    }

    public function normalize($object, string $format = null, array $context = array()): array
    {
        /** @var Image $object */
        $data = $this->normalizer->normalize($object, $format, $context);

        $groups = $context['groups'] ?? null;
        if ('image:read' === $groups) {
            // Add URL field
            $data['url'] = '/resize/xs'.$this->uploaderHelper->asset($object);

            // Add name field
            $info = pathinfo($object->getFileName());
            $filenameParts = explode('-', $info['filename']);
            $filenameParts = array_slice($filenameParts, 0, -1);
            $filename = implode('-', $filenameParts);
            $extension = $info['extension'] ?? '';
            $data['name'] = "{$filename}.{$extension}";

            // Change size format
            $data['size'] = $this->imageManager->displaySize($object->getFileSize());

            // Year
            $data['year'] = $object->getCreatedAt()->format('Y');
        }

        return $data;
    }

    public function supportsNormalization($data, string $format = null): bool
    {
        return $data instanceof Image;
    }

    public function hasCacheableSupportsMethod(): bool
    {
        return true;
    }
}
