<?php

namespace App\Repository;

use App\Entity\Image;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Common\Persistence\ManagerRegistry;

/**
 * @method Image|null find($id, $lockMode = null, $lockVersion = null)
 * @method Image|null findOneBy(array $criteria, array $orderBy = null)
 * @method Image[]    findAll()
 * @method Image[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ImageRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Image::class);
    }

    public function findYears(): array
    {
        return $this->createQueryBuilder('image')
            ->select('EXTRACT(YEAR FROM image.createdAt) as year, COUNT(image.id) as count')
            ->groupBy('year')
            ->orderBy('year', 'DESC')
            ->getQuery()
            ->getResult();
    }

    public function findByYear(int $year): array
    {
        return $this->createQueryBuilder('image')
            ->where('YEAR(image.createdAt) = :year')
            ->setParameter('year', $year)
            ->getQuery()
            ->getResult()
        ;
    }
}
