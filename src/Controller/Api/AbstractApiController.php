<?php

namespace App\Controller\Api;

use App\Exception\ValidationFailedApiException;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\HttpFoundation\Request;

abstract class AbstractApiController extends AbstractController
{
    protected function handleApiRequest(
        Request $request,
        string $formClass,
        $data = null,
        bool $withFiles = false
    ): void {
        $form = $this->createForm($formClass, $data);
        $data = json_decode($request->getContent(), true) ?? [];
        if ($withFiles) {
            $files = $request->files->all();
            $data += $files;
        }
        $clearMissing = 'PATCH' !== $request->getMethod();
        $form->submit($data, $clearMissing);
        if (!$form->isSubmitted() || !$form->isValid()) {
            throw new ValidationFailedApiException($this->getErrorsFromForm($form));
        }
    }

    private function getErrorsFromForm(FormInterface $form): array
    {
        $errors = [];
        foreach ($form->getErrors() as $error) {
            $errors[] = $error->getMessage();
        }

        foreach ($form->all() as $childForm) {
            if ($childForm instanceof FormInterface) {
                if ($childErrors = $this->getErrorsFromForm($childForm)) {
                    $errors[$childForm->getName()] = $childErrors;
                }
            }
        }

        return $errors;
    }
}
