<?php

namespace App\EventListener;

use Doctrine\Common\Persistence\Event\LifecycleEventArgs;
use App\Entity\TimelineItem;
use Doctrine\ORM\EntityManagerInterface;

class TimelineItemListener
{
    /**
     * @var EntityManagerInterface
     */
    private $em;

    /**
     * TimelineItemListener constructor.
     *
     * @param EntityManagerInterface $em
     */
    public function __construct(EntityManagerInterface $em)
    {
        $this->em = $em;
    }

    /**
     * @param LifecycleEventArgs $args
     *
     * @throws \Doctrine\ORM\NonUniqueResultException
     */
    public function prePersist(LifecycleEventArgs $args)
    {
        $entity = $args->getObject();

        if (!$entity instanceof TimelineItem) {
            return;
        }

        $repository = $this->em->getRepository(TimelineItem::class);
        $positionData = $repository->findPositionData();
        if (null === $positionData) {
            $entity->setPosition(1);

            return;
        }

        $nextPosition = $positionData['max_position'];
        if ($positionData['max_id'] !== $entity->getId()) {
            ++$nextPosition;
        }

        $position = $entity->getPosition();
        if ($position === $nextPosition) {
            return;
        }

        if ($position > $nextPosition) {
            $entity->setPosition($nextPosition);
        } else {
            $repository->updateHigherPosition($position);
        }
    }

    /**
     * @param LifecycleEventArgs $args
     *
     * @throws \Doctrine\ORM\NonUniqueResultException
     */
    public function preUpdate(LifecycleEventArgs $args)
    {
        $entity = $args->getObject();

        if (!$entity instanceof TimelineItem) {
            return;
        }

        $uow = $this->em->getUnitOfWork();
        $entityChanges = $uow->getEntityChangeSet($entity);
        if (!isset($entityChanges['position'])) {
            return;
        }
        $positionOld = $entityChanges['position'][0];
        $repository = $this->em->getRepository(TimelineItem::class);
        $positionData = $repository->findPositionData();

        if ($entity->getPosition() > $positionData['max_position']) {
            $entity->setPosition($positionData['max_position']);
        }

        $repository->updatePositionsFromOldAndNew($positionOld, $entity->getPosition());
    }

    /**
     * @param LifecycleEventArgs $args
     */
    public function postRemove(LifecycleEventArgs $args)
    {
        $entity = $args->getObject();

        if (!$entity instanceof TimelineItem) {
            return;
        }

        $position = $entity->getPosition();
        $this->em->getRepository(TimelineItem::class)->recomputeHigherPositions($position);
    }
}
