<?php

namespace App\Controller;

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
     */
    public function career(): Response
    {
        return $this->render('page/career.html.twig');
    }
}