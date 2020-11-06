<?php

namespace App\Tests\Response;

use App\Response\UnauthorizedJsonResponse;
use PHPUnit\Framework\TestCase;
use Symfony\Component\HttpFoundation\Response;

class UnauthorizedJsonResponseTest extends TestCase
{
    public function testResponse(): void
    {
        $expected = [
            'code' => Response::HTTP_UNAUTHORIZED,
            'message' => Response::$statusTexts[Response::HTTP_UNAUTHORIZED],
        ];
        $response = new UnauthorizedJsonResponse();
        $this->assertSame($expected['code'], $response->getStatusCode());
        $this->assertSame(json_encode($expected), $response->getContent());
    }
}
