<?php

namespace App\Response;

use App\Exception\ValidationFailedApiException;
use Symfony\Component\HttpFoundation\JsonResponse;

class ValidationFailedJsonResponse extends JsonResponse
{
    private array $errors;

    public function __construct(ValidationFailedApiException $e)
    {
        $this->errors = $e->getErrors();
        parent::__construct(null, $e->getStatusCode());
    }

    public function setData($data = [])
    {
        return parent::setData([
            'code' => $this->statusCode,
            'errors' => $this->errors,
        ]);
    }
}
