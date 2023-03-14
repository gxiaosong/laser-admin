<?php declare(strict_types=1);

namespace Laser\Core\Content\ImportExport\Exception;

use Laser\Core\Framework\Log\Package;
use Laser\Core\Framework\LaserHttpException;
use Symfony\Component\HttpFoundation\Response;

#[Package('system-settings')]
class DeleteDefaultProfileException extends LaserHttpException
{
    public function __construct(array $ids)
    {
        parent::__construct('Cannot delete system default import_export_profile', ['ids' => $ids]);
    }

    public function getErrorCode(): string
    {
        return 'CONTENT__IMPORT_EXPORT_DELETE_DEFAULT_PROFILE';
    }

    public function getStatusCode(): int
    {
        return Response::HTTP_BAD_REQUEST;
    }
}