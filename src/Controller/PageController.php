<?php

namespace App\Controller;

use App\Repository\TimelineItemRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class PageController extends AbstractController
{
    /**
     * @Route("", name="home")
     */
    public function home(): Response
    {
        return $this->render('page/home.html.twig');
    }

    /**
     * @Route("/mon-parcours", name="career")
     *
     * @param Request                $request
     * @param TimelineItemRepository $repository
     *
     * @return Response
     */
    public function career(Request $request, TimelineItemRepository $repository): Response
    {
        $sort = $request->query->get('sort');
        if ('desc' !== $sort && 'asc' !== $sort) {
            $sort = 'desc';
        }

        $items = $repository->findBy([], ['position' => $sort]);

        return $this->render('page/career.html.twig', ['items' => $items, 'sort' => $sort]);
    }
}
