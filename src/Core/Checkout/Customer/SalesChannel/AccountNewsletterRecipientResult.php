<?php declare(strict_types=1);

namespace Laser\Core\Checkout\Customer\SalesChannel;

use Laser\Core\Framework\Log\Package;
use Laser\Core\Framework\Struct\Struct;

#[Package('customer-order')]
class AccountNewsletterRecipientResult extends Struct
{
    final public const UNDEFINED = 'undefined';

    protected string $status;

    public function __construct(?string $status = null)
    {
        if ($status === null) {
            $this->status = self::UNDEFINED;

            return;
        }
        $this->status = $status;
    }

    public function getStatus(): string
    {
        return $this->status;
    }

    public function setStatus(string $status): void
    {
        $this->status = $status;
    }

    public function getApiAlias(): string
    {
        return 'account_newsletter_recipient';
    }
}