<?php

namespace App\Service;

use App\Repository\ImageRepository;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

class ImageManager
{
    /**
     * @var ImageRepository
     */
    private $repository;

    /**
     * ImageManager constructor.
     *
     * @param ImageRepository $repository
     */
    public function __construct(ImageRepository $repository)
    {
        $this->repository = $repository;
    }

    public function getFolders(array $urlParams): array
    {
        $folders = $this->repository->findYears();
        if (empty($folders)) {
            $folders = [['year' => date('Y'), 'count' => 0]];
        }

        return $folders;
    }

    public function displaySize(int $size): string
    {
        $ko = pow(2, 10);
        $k = $size / $ko;
        $unit = 'k';
        if ($k > $ko) {
            $k = $k / $ko;
            $unit = 'M';
        }
        $k = $this->ceil($k, $k > 10 ? 0 : 1);

        return $k.$unit;
    }

    private function ceil(float $n, int $decimals): float
    {
        return ceil($n * pow(10, $decimals)) / pow(10, $decimals);
    }
}
