<?php declare(strict_types=1);

namespace Laser\Core\Checkout\Cart\LineItem\Group\Exception;

use Laser\Core\Framework\Log\Package;
use Laser\Core\Framework\LaserHttpException;
use Symfony\Component\HttpFoundation\Response;

#[Package('checkout')]
class LineItemGroupPackagerNotFoundException extends LaserHttpException
{
    public function __construct(string $key)
    {
        parent::__construct('Packager "{{ key }}" has not been found!', ['key' => $key]);
    }

    public function getErrorCode(): string
    {
        return 'CHECKOUT__GROUP_PACKAGER_NOT_FOUND';
    }

    public function getStatusCode(): int
    {
        return Response::HTTP_BAD_REQUEST;
    }
}