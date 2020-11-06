<?php

namespace App\Serializer\Normalizer;

use Knp\Bundle\PaginatorBundle\Pagination\SlidingPaginationInterface;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Serializer\Normalizer\CacheableSupportsMethodInterface;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;
use Symfony\Component\Serializer\Normalizer\ObjectNormalizer;

class PaginationNormalizer implements NormalizerInterface, CacheableSupportsMethodInterface
{
    /**
     * @var ObjectNormalizer
     */
    private $normalizer;

    /**
     * @var UrlGeneratorInterface
     */
    private $urlGenerator;

    /**
     * @var RequestStack
     */
    private $requestStack;

    public function __construct(
        ObjectNormalizer $normalizer,
        UrlGeneratorInterface $urlGenerator,
        RequestStack $requestStack
    ) {
        $this->normalizer = $normalizer;
        $this->urlGenerator = $urlGenerator;
        $this->requestStack = $requestStack;
    }

    public function normalize($object, string $format = null, array $context = []): array
    {
        /** @var SlidingPaginationInterface $object */
        $paginationData = $object->getPaginationData();
        $request = $this->requestStack->getCurrentRequest();
        $route = $request->get('_route');
        $currentData = [
            'value' => $paginationData['current'],
            'url' => $this->urlGenerator->generate($route, array_merge($request->query->all(), ['page' => $paginationData['current']])),
        ];
        $firstData = [
            'value' => $paginationData['first'],
            'url' => $this->urlGenerator->generate($route, array_merge($request->query->all(), ['page' => $paginationData['first']])),
        ];
        $lastData = [
            'value' => $paginationData['last'],
            'url' => $this->urlGenerator->generate($route, array_merge($request->query->all(), ['page' => $paginationData['last']])),
        ];
        $nextData = isset($paginationData['next']) ? [
            'value' => $paginationData['next'],
            'url' => $this->urlGenerator->generate($route, array_merge($request->query->all(), ['page' => $paginationData['next']])),
        ] : null;
        $previousData = isset($paginationData['previous']) ? [
            'value' => $paginationData['previous'],
            'url' => $this->urlGenerator->generate($route, array_merge($request->query->all(), ['page' => $paginationData['previous']])),
        ] : null;

        return [
            'current' => $currentData,
            'first' => $firstData,
            'last' => $lastData,
            'next' => $nextData,
            'previous' => $previousData,
        ];
    }

    public function supportsNormalization($data, string $format = null): bool
    {
        return $data instanceof SlidingPaginationInterface;
    }

    public function hasCacheableSupportsMethod(): bool
    {
        return true;
    }
}
