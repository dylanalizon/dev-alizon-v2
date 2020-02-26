<?php

namespace App\Controller;

use App\Repository\TimelineItemRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
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
     * @param TimelineItemRepository $repository
     * @return Response
     */
    public function career(TimelineItemRepository $repository): Response
    {
        $items = $repository->findBy([], ['position' => 'DESC']);
        dump($items);
        return $this->render('page/career.html.twig', ['items' => $items]);
    }
}
