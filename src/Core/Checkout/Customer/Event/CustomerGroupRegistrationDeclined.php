<?php declare(strict_types=1);

namespace Laser\Core\Checkout\Customer\Event;

use Laser\Core\Checkout\Customer\Aggregate\CustomerGroup\CustomerGroupDefinition;
use Laser\Core\Checkout\Customer\Aggregate\CustomerGroup\CustomerGroupEntity;
use Laser\Core\Checkout\Customer\CustomerDefinition;
use Laser\Core\Checkout\Customer\CustomerEntity;
use Laser\Core\Framework\Context;
use Laser\Core\Framework\Event\CustomerAware;
use Laser\Core\Framework\Event\CustomerGroupAware;
use Laser\Core\Framework\Event\EventData\EntityType;
use Laser\Core\Framework\Event\EventData\EventDataCollection;
use Laser\Core\Framework\Event\EventData\MailRecipientStruct;
use Laser\Core\Framework\Event\MailAware;
use Laser\Core\Framework\Event\SalesChannelAware;
use Laser\Core\Framework\Log\Package;
use Symfony\Contracts\EventDispatcher\Event;

#[Package('customer-order')]
class CustomerGroupRegistrationDeclined extends Event implements SalesChannelAware, CustomerAware, MailAware, CustomerGroupAware
{
    final public const EVENT_NAME = 'customer.group.registration.declined';

    /**
     * @internal
     */
    public function __construct(
        private readonly CustomerEntity $customer,
        private readonly CustomerGroupEntity $customerGroup,
        private readonly Context $context,
        private readonly ?MailRecipientStruct $mailRecipientStruct = null
    ) {
    }

    public static function getAvailableData(): EventDataCollection
    {
        return (new EventDataCollection())
            ->add('customer', new EntityType(CustomerDefinition::class))
            ->add('customerGroup', new EntityType(CustomerGroupDefinition::class));
    }

    public function getName(): string
    {
        return self::EVENT_NAME;
    }

    public function getMailStruct(): MailRecipientStruct
    {
        if ($this->mailRecipientStruct) {
            return $this->mailRecipientStruct;
        }

        return new MailRecipientStruct(
            [
                $this->customer->getEmail() => $this->customer->getFirstName() . ' ' . $this->customer->getLastName(),
            ]
        );
    }

    public function getSalesChannelId(): string
    {
        return $this->customer->getSalesChannelId();
    }

    public function getContext(): Context
    {
        return $this->context;
    }

    public function getCustomer(): CustomerEntity
    {
        return $this->customer;
    }

    public function getCustomerGroup(): CustomerGroupEntity
    {
        return $this->customerGroup;
    }

    public function getCustomerId(): string
    {
        return $this->getCustomer()->getId();
    }

    public function getCustomerGroupId(): string
    {
        return $this->getCustomerGroup()->getId();
    }
}