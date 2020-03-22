<?php

namespace App\Controller\Api;

use App\Entity\Image;
use App\Form\Api\ImageType;
use App\Repository\ImageRepository;
use App\Service\ImageManager;
use Doctrine\ORM\EntityManagerInterface;
use Swagger\Annotations as SWG;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Contracts\Translation\TranslatorInterface;

/**
 * @Route("/api/images")
 * @SWG\Tag(name="Images")
 */
class ImageController extends AbstractController
{
    /**
     * List the images by year.
     *
     * @Route("/year/{year}", methods={"GET"})
     *
     * @SWG\Parameter(
     *     name="year",
     *     in="path",
     *     type="integer",
     *     description="The year"
     * )
     *
     * @SWG\Response(
     *     response=200,
     *     description="Returns the images by year",
     *     @SWG\Schema(type="array", @SWG\Items(type="object", ref="#/definitions/image"))
     * )
     */
    public function list(ImageRepository $imageRepository, int $year): JsonResponse
    {
        $images = $imageRepository->findByYear($year);

        return $this->json($images, Response::HTTP_OK, [], ['groups' => 'image:read']);
    }

    /**
     * List the image folders.
     *
     * @Route("/folders", methods={"GET"})
     *
     * @SWG\Response(
     *     response=200,
     *     description="Returns the folders",
     *     @SWG\Schema(type="array", @SWG\Items(type="object", ref="#/definitions/folder"))
     * )
     */
    public function folders(ImageManager $imageManager): JsonResponse
    {
        $folders = $imageManager->getFolders();

        return $this->json($folders, Response::HTTP_OK);
    }

    /**
     * Create an image.
     *
     * @Route(methods={"POST"})
     *
     * @SWG\Parameter(
     *     name="file",
     *     in="formData",
     *     type="file",
     *     description="The image",
     *     required=true
     * )
     *
     * @SWG\Response(
     *     response=201,
     *     description="Returns the image created",
     *     @SWG\Schema(type="object", ref="#/definitions/image")
     * )
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
            'message' => $translator->trans('image.error.create', [], 'api'),
        ], Response::HTTP_UNPROCESSABLE_ENTITY);
    }

    /**
     * Delete an image.
     *
     * @Route("/{id}", methods={"DELETE"})
     *
     * @SWG\Parameter(
     *     name="id",
     *     in="path",
     *     type="integer",
     *     description="The image id"
     * )
     *
     * @SWG\Response(
     *     response=204,
     *     description="Returned when successful",
     * )
     */
    public function delete(Image $image, EntityManagerInterface $em): JsonResponse
    {
        $em->remove($image);
        $em->flush();

        return $this->json([], Response::HTTP_NO_CONTENT);
    }
}
