<?php

namespace App\Controller\Api;

use App\Entity\Image;
use App\Form\Api\ImageType;
use App\Repository\ImageRepository;
use App\Service\ImageManager;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Contracts\Translation\TranslatorInterface;

/**
 * @Route("/api/images")
 */
class ImageController extends AbstractController
{
    /**
     * @Route("/year/{year}", name="api_image_list_by_year", methods={"GET"})
     *
     * @param ImageRepository $imageRepository
     * @param int             $year
     *
     * @return JsonResponse
     */
    public function list(ImageRepository $imageRepository, int $year): JsonResponse
    {
        $images = $imageRepository->findByYear($year);

        return $this->json($images, Response::HTTP_OK, [], ['groups' => 'image:read']);
    }

    /**
     * @Route("/folders", name="api_image_folders", methods={"GET"})
     *
     * @param ImageManager $imageManager
     * @return JsonResponse
     */
    public function folders(ImageManager $imageManager): JsonResponse
    {
        $folders = $imageManager->getFolders();

        return $this->json($folders, Response::HTTP_OK);
    }

    /**
     * @Route("", name="api_image_create", methods={"POST"})
     *
     * @param Request                $request
     * @param EntityManagerInterface $em
     * @param TranslatorInterface    $translator
     *
     * @return JsonResponse
     */
    public function create(Request $request, EntityManagerInterface $em, TranslatorInterface $translator): JsonResponse
    {
        $image = new Image();
        $form = $this->createForm(ImageType::class, $image);

        $data = ['file' => $request->files->get('file')];
        $form->submit($data);
        if ($form->isSubmitted() && $form->isValid()) {
            $em->persist($image);
            $em->flush();

            return $this->json($image, Response::HTTP_CREATED, [], ['groups' => 'image:read']);
        }

        return $this->json([
            'message' => $translator->trans('image.error.create', [], 'admin'),
        ], Response::HTTP_BAD_REQUEST);
    }

    /**
     * @Route("/{id}", name="api_image_delete", methods={"DELETE"})
     *
     * @param Image                  $image
     * @param EntityManagerInterface $em
     *
     * @return JsonResponse
     */
    public function delete(Image $image, EntityManagerInterface $em): JsonResponse
    {
        $em->remove($image);
        $em->flush();

        return $this->json([]);
    }
}
