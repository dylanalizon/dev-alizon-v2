<?php

namespace App\Response;

use Symfony\Component\HttpFoundation\JsonResponse;

class UnauthorizedJsonResponse extends JsonResponse
{
    private string $message;

    public function __construct()
    {
        $this->message = static::$statusTexts[static::HTTP_UNAUTHORIZED];
        parent::__construct(null, static::HTTP_UNAUTHORIZED);
    }

    public function setData($data = []): self
    {
        return parent::setData(['code' => $this->statusCode, 'message' => $this->message]);
    }
}
