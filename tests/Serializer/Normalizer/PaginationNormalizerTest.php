<?php

namespace App\Tests\Serializer\Normalizer;

use App\Serializer\Normalizer\PaginationNormalizer;
use Knp\Bundle\PaginatorBundle\Pagination\SlidingPagination;
use Knp\Bundle\PaginatorBundle\Pagination\SlidingPaginationInterface;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Symfony\Component\HttpFoundation\ParameterBag;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Serializer\Normalizer\ObjectNormalizer;

class PaginationNormalizerTest extends TestCase
{
    /** @var MockObject|ObjectNormalizer */
    private MockObject $normalizer;

    /** @var MockObject|UrlGeneratorInterface */
    private MockObject $urlGenerator;

    /** @var MockObject|RequestStack */
    private MockObject $requestStack;

    public function setUp(): void
    {
        $this->normalizer = $this->createMock(ObjectNormalizer::class);
        $this->urlGenerator = $this->createMock(UrlGeneratorInterface::class);
        $this->requestStack = $this->createMock(RequestStack::class);
    }

    /**
     * @dataProvider providePaginations
     */
    public function testNormalize(array $data, int $countMethodCalls, array $expected): void
    {
        $paginationMock = $this->createMock(SlidingPaginationInterface::class);
        $paginationMock
            ->expects($this->once())
            ->method('getPaginationData')
            ->willReturn($data);
        $requestMock = $this->createMock(Request::class);
        $requestMock->query = $this->createMock(ParameterBag::class);
        $this->requestStack
            ->expects($this->once())
            ->method('getCurrentRequest')
            ->willReturn($requestMock);
        $requestMock
            ->expects($this->once())
            ->method('get')
            ->with('_route')
            ->willReturn('a route name');
        $requestMock
            ->query
            ->expects($this->exactly($countMethodCalls))
            ->method('all')
            ->willReturn([]);
        $this->urlGenerator
            ->expects($this->exactly($countMethodCalls))
            ->method('generate')
            ->with('a route name', $this->isType('array'))
            ->willReturn('an url');
        $normalizer = new PaginationNormalizer($this->normalizer, $this->urlGenerator, $this->requestStack);
        $result = $normalizer->normalize($paginationMock);
        $this->assertEquals($expected, $result);
    }

    public function testSupportsNormalizationOk(): void
    {
        $normalizer = new PaginationNormalizer($this->normalizer, $this->urlGenerator, $this->requestStack);
        $this->assertTrue($normalizer->supportsNormalization(new SlidingPagination([])));
    }

    public function testSupportsNormalizationKo(): void
    {
        $normalizer = new PaginationNormalizer($this->normalizer, $this->urlGenerator, $this->requestStack);
        $this->assertFalse($normalizer->supportsNormalization(new \stdClass()));
    }

    public function testHasCacheableSupportsMethod(): void
    {
        $normalizer = new PaginationNormalizer($this->normalizer, $this->urlGenerator, $this->requestStack);
        $this->assertTrue($normalizer->hasCacheableSupportsMethod());
    }

    public function providePaginations(): array
    {
        return [
            [
                // Data
                [
                    'current' => 1,
                    'first' => 1,
                    'last' => 1,
                ],
                // Count method calls
                3,
                // Expected
                [
                    'current' => [
                        'value' => 1,
                        'url' => 'an url',
                    ],
                    'first' => [
                        'value' => 1,
                        'url' => 'an url',
                    ],
                    'last' => [
                        'value' => 1,
                        'url' => 'an url',
                    ],
                    'next' => null,
                    'previous' => null,
                ],
            ],
            [
                // Data
                [
                    'current' => 1,
                    'first' => 1,
                    'last' => 10,
                    'next' => 2,
                ],
                // Count method calls
                4,
                // Expected
                [
                    'current' => [
                        'value' => 1,
                        'url' => 'an url',
                    ],
                    'first' => [
                        'value' => 1,
                        'url' => 'an url',
                    ],
                    'last' => [
                        'value' => 10,
                        'url' => 'an url',
                    ],
                    'next' => [
                        'value' => 2,
                        'url' => 'an url',
                    ],
                    'previous' => null,
                ],
            ],
            [
                // Data
                [
                    'current' => 5,
                    'first' => 1,
                    'last' => 10,
                    'next' => 6,
                    'previous' => 4,
                ],
                // Count method calls
                5,
                // Expected
                [
                    'current' => [
                        'value' => 5,
                        'url' => 'an url',
                    ],
                    'first' => [
                        'value' => 1,
                        'url' => 'an url',
                    ],
                    'last' => [
                        'value' => 10,
                        'url' => 'an url',
                    ],
                    'next' => [
                        'value' => 6,
                        'url' => 'an url',
                    ],
                    'previous' => [
                        'value' => 4,
                        'url' => 'an url',
                    ],
                ],
            ],
            [
                // Data
                [
                    'current' => 10,
                    'first' => 1,
                    'last' => 10,
                    'previous' => 9,
                ],
                // Count method calls
                4,
                // Expected
                [
                    'current' => [
                        'value' => 10,
                        'url' => 'an url',
                    ],
                    'first' => [
                        'value' => 1,
                        'url' => 'an url',
                    ],
                    'last' => [
                        'value' => 10,
                        'url' => 'an url',
                    ],
                    'next' => null,
                    'previous' => [
                        'value' => 9,
                        'url' => 'an url',
                    ],
                ],
            ],
        ];
    }
}
