<?php declare(strict_types=1);

namespace Laser\Core\Content\ImportExport\Exception;

use Laser\Core\Framework\Log\Package;
use Laser\Core\Framework\LaserHttpException;
use Symfony\Component\HttpFoundation\Response;

#[Package('system-settings')]
class UnexpectedFileTypeException extends LaserHttpException
{
    public function __construct(
        ?string $givenType,
        string $expectedType
    ) {
        parent::__construct(
            'Given file does not match MIME-Type for selected profile. Given: {{ given }}. Expected: {{ expected }}',
            ['given' => $givenType, 'expected' => $expectedType]
        );
    }

    public function getErrorCode(): string
    {
        return 'CONTENT__IMPORT_FILE_HAS_UNEXPECTED_TYPE';
    }

    public function getStatusCode(): int
    {
        return Response::HTTP_BAD_REQUEST;
    }
}