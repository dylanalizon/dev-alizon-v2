<?php

namespace App\Controller\Api;

use App\Entity\TimelineItem;
use App\Form\Api\TimelineItemType;
use Doctrine\ORM\EntityManagerInterface;
use Nelmio\ApiDocBundle\Annotation\Model;
use Swagger\Annotations as SWG;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Contracts\Translation\TranslatorInterface;

/**
 * @Route("/api/timeline_items")
 * @SWG\Tag(name="Timeline items")
 */
class TimelineItemController extends AbstractController
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
    public function create(TimelineItem $timelineItem, Request $request, EntityManagerInterface $em, TranslatorInterface $translator): JsonResponse
    {
        $form = $this->createForm(TimelineItemType::class, $timelineItem);
        $data = json_decode($request->getContent(), true);
        $form->submit($data, 'PATCH' !== $request->getMethod());
        if ($form->isSubmitted() && $form->isValid()) {
            $em->flush();

            return $this->json($timelineItem, Response::HTTP_OK, [], ['groups' => 'timeline_item:read']);
        }

        return $this->json([
            'message' => $translator->trans('timeline_item.error.update', [], 'api'),
        ], Response::HTTP_UNPROCESSABLE_ENTITY);
    }
}
