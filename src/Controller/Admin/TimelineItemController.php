<?php

namespace App\Controller\Admin;

use App\Entity\TimelineItem;
use App\Repository\TimelineItemRepository;
use Doctrine\ORM\NonUniqueResultException;
use EasyCorp\Bundle\EasyAdminBundle\Controller\EasyAdminController;

class TimelineItemController extends EasyAdminController
{
    /**
     * @var TimelineItemRepository
     */
    private $repository;

    /**
     * TimelineItemController constructor.
     * @param TimelineItemRepository $repository
     */
    public function __construct(TimelineItemRepository $repository)
    {
        $this->repository = $repository;
    }

    /**
     * @return TimelineItem
     * @throws NonUniqueResultException
     */
    protected function createNewTimelineItemEntity()
    {
        $positionData = $this->repository->findPositionData();

        return (new TimelineItem())->setPosition($positionData['position_max'] + 1);
    }
}
