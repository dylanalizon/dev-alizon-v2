<?php

namespace App\Controller\Api;

use App\Dto\TimelineItemDto;
use App\Entity\TimelineItem;
use App\Form\Api\TimelineItemType;
use Doctrine\ORM\EntityManagerInterface;
use Nelmio\ApiDocBundle\Annotation\Model;
use Swagger\Annotations as SWG;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/api/timeline-items")
 * @SWG\Tag(name="Timeline items")
 */
class TimelineItemController extends AbstractApiController
{
    /**
     * Update an timeline item.
     *
     * @Route("/{id}", methods={"PUT", "PATCH"})
     *
     * @SWG\Parameter(
     *     name="id",
     *     in="path",
     *     type="integer",
     *     description="the timeline item id",
     *     required=true
     * )
     *
     * @SWG\Parameter(
     *     name="timeline_item",
     *     in="body",
     *     description="the timeline item object",
     *     required=true,
     *     @SWG\Schema(type="object", ref=@Model(type=TimelineItemType::class))
     * )
     *
     * @SWG\Response(
     *     response=200,
     *     description="Returns the timeline item object updated",
     *     @SWG\Schema(type="object", ref=@Model(type=TimelineItem::class, groups={"timeline_item:read"}))
     * )
     */
    public function update(TimelineItem $timelineItem, Request $request, EntityManagerInterface $em): JsonResponse
    {
        $dto = new TimelineItemDto();
        $this->handleApiRequest($request, TimelineItemType::class, $dto);
        $timelineItem->setPosition($dto->position);
        $em->flush();

        return $this->json($timelineItem, Response::HTTP_OK, [], ['groups' => 'timeline_item:read']);
    }
}
