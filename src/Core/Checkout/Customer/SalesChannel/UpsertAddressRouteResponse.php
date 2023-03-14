<?php declare(strict_types=1);

namespace Laser\Core\Checkout\Customer\SalesChannel;

use Laser\Core\Checkout\Customer\Aggregate\CustomerAddress\CustomerAddressEntity;
use Laser\Core\Framework\Log\Package;
use Laser\Core\System\SalesChannel\StoreApiResponse;

#[Package('customer-order')]
class UpsertAddressRouteResponse extends StoreApiResponse
{
    /**
     * @var CustomerAddressEntity
     */
    protected $object;

    public function __construct(CustomerAddressEntity $address)
    {
        parent::__construct($address);
    }

    public function getAddress(): CustomerAddressEntity
    {
        return $this->object;
    }
}