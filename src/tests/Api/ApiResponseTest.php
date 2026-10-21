<?php

declare(strict_types=1);

namespace Armasquads\Tests\Api;

use Armasquads\Api\ApiResponse;
use PHPUnit\Framework\TestCase;

final class ApiResponseTest extends TestCase
{
    public function testSuccessBodyKeepsTheOldJsonEscaping(): void
    {
        $response = new ApiResponse(['logo' => '/uploads/logos/a/a.paa', 'name' => 'Äö']);

        self::assertFalse($response->hasError());
        self::assertSame('{"success":true,"results":{"logo":"\/uploads\/logos\/a\/a.paa","name":"\u00c4\u00f6"}}', $response->body());
    }

    public function testErrorMessageIsWrappedInAList(): void
    {
        self::assertSame('{"success":false,"error":["api key not found"]}', ApiResponse::error('api key not found', 401)->body());
    }

    public function testValidationErrorsStayAnObject(): void
    {
        $response = ApiResponse::error(['tag' => ['isEmpty' => 'Please enter a Squad Tag']], 422);

        self::assertSame(422, $response->status);
        self::assertSame('{"success":false,"error":{"tag":{"isEmpty":"Please enter a Squad Tag"}}}', $response->body());
    }

    public function testEmptySuccessHasNullResults(): void
    {
        self::assertSame('{"success":true,"results":null}', (new ApiResponse())->body());
    }
}
