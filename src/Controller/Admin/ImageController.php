<?php

namespace App\Controller\Admin;

use App\Repository\ImageRepository;
use App\Service\ImageManager;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\SerializerInterface;

class ImageController extends AbstractController
{
    /**
     * @var ImageManager
     */
    private $imageManager;

    /**
     * ImageController constructor.
     *
     * @param ImageManager $imageManager
     */
    public function __construct(ImageManager $imageManager)
    {
        $this->imageManager = $imageManager;
    }

    /**
     * @Route("/admin/image", name="admin_image_index")
     *
     * @param ImageRepository     $imageRepository
     * @param SerializerInterface $serializer
     *
     * @return Response
     */
    public function index(
        ImageRepository $imageRepository,
        SerializerInterface $serializer
    ): Response {
        $folders = $this->imageManager->getFolders();
        $images = $imageRepository->findByYear($folders[0]['year']);

        return $this->render('admin/image/index.html.twig', [
            'folders' => json_encode($folders),
            'currentYear' => $folders[0]['year'],
            'images' => $serializer->serialize($images, 'json', ['groups' => 'image:read']),
        ]);
    }
}
