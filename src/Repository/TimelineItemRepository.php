<?php

namespace App\Repository;

use App\Entity\TimelineItem;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Common\Collections\Criteria;
use Doctrine\Common\Persistence\ManagerRegistry;
use Doctrine\ORM\AbstractQuery;

/**
 * @method TimelineItem|null find($id, $lockMode = null, $lockVersion = null)
 * @method TimelineItem|null findOneBy(array $criteria, array $orderBy = null)
 * @method TimelineItem[]    findAll()
 * @method TimelineItem[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class TimelineItemRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, TimelineItem::class);
    }

    /**
     * @return array|null
     *
     * @throws \Doctrine\ORM\NonUniqueResultException
     */
    public function findPositionData(): ?array
    {
        $result = $this->createQueryBuilder('timeline_item')
            ->select('timeline_item.id as max_id, timeline_item.position as max_position')
            ->orderBy('timeline_item.position', 'DESC')
            ->setMaxResults(1)
            ->getQuery()
            ->getOneOrNullResult(AbstractQuery::HYDRATE_ARRAY)
        ;

        return $result;
    }

    /**
     * @param int $position
     */
    public function updateHigherPosition(int $position): void
    {
        $higherItems = $this->createQueryBuilder('timeline_item')
            ->select('timeline_item.id')
            ->where('timeline_item.position >= :position')
            ->setParameter('position', $position)
            ->orderBy('timeline_item.position', 'DESC')
            ->getQuery()
            ->getArrayResult();

        foreach ($higherItems as $item) {
            $this->createQueryBuilder('timeline_item')
                ->update()
                ->set('timeline_item.position', 'timeline_item.position + 1')
                ->where('timeline_item.id = :id')
                ->setParameter('id', $item['id'])
                ->getQuery()
                ->execute()
            ;
        }
    }

    /**
     * @param int $positionOld
     * @param int $positionNew
     */
    public function updatePositionsFromOldAndNew(int $positionOld, int $positionNew): void
    {
        // Set the position to 0 due to unique column
        $this->createQueryBuilder('timeline_item')
            ->update()
            ->set('timeline_item.position', 0)
            ->where('timeline_item.position = :position_old')
            ->setParameter('position_old', $positionOld)
            ->getQuery()
            ->execute()
        ;

        $qbItems = $this->createQueryBuilder('timeline_item')
            ->select('timeline_item.id');
        if ($positionOld > $positionNew) {
            $qbItems->where('timeline_item.position < :position_old')
                ->andWhere('timeline_item.position >= :position_new')
                ->orderBy('timeline_item.position', 'DESC');
        } else {
            $qbItems->where('timeline_item.position > :position_old')
                ->andWhere('timeline_item.position <= :position_new')
                ->orderBy('timeline_item.position', 'ASC');
        }
        $items = $qbItems->setParameters([
            'position_old' => $positionOld,
            'position_new' => $positionNew,
        ])
        ->getQuery()
        ->getArrayResult();

        foreach ($items as $item) {
            $qb = $this->createQueryBuilder('timeline_item')
                ->update();
            if ($positionOld > $positionNew) {
                $qb->set('timeline_item.position', 'timeline_item.position + 1');
            } else {
                $qb->set('timeline_item.position', 'timeline_item.position - 1');
            }
            $qb->where('timeline_item.id = :id')
                ->setParameter('id', $item['id'])
                ->getQuery()
                ->execute()
            ;
        }
    }

    /**
     * @param int $position
     */
    public function recomputeHigherPositions(int $position): void
    {
        $items = $this->createQueryBuilder('timeline_item')
            ->select('timeline_item.id')
            ->where('timeline_item.position > :position')
            ->setParameter('position', $position)
            ->orderBy('timeline_item.position', 'ASC')
            ->getQuery()
            ->getArrayResult();

        foreach ($items as $item) {
            $this->createQueryBuilder('timeline_item')
                ->update()
                ->set('timeline_item.position', 'timeline_item.position - 1')
                ->where('timeline_item.id = :id')
                ->setParameter('id', $item['id'])
                ->getQuery()
                ->execute()
            ;
        }
    }
}
