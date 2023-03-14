<?php declare(strict_types=1);

namespace Laser\Core\Checkout\Cart\Order\Transformer;

use Laser\Core\Checkout\Customer\Aggregate\CustomerAddress\CustomerAddressCollection;
use Laser\Core\Checkout\Customer\Aggregate\CustomerAddress\CustomerAddressEntity;
use Laser\Core\Framework\Log\Package;
use Laser\Core\Framework\Uuid\Uuid;

#[Package('checkout')]
class AddressTransformer
{
    public static function transformCollection(CustomerAddressCollection $addresses, bool $useIdAsKey = false): array
    {
        $output = [];
        foreach ($addresses as $address) {
            if (\array_key_exists($address->getId(), $output)) {
                continue;
            }
            $output[$address->getId()] = self::transform($address);
        }

        if (!$useIdAsKey) {
            $output = array_values($output);
        }

        return $output;
    }

    public static function transform(CustomerAddressEntity $address): array
    {
        return array_filter([
            'id' => Uuid::randomHex(),
            'company' => $address->getCompany(),
            'department' => $address->getDepartment(),
            'salutationId' => $address->getSalutationId(),
            'title' => $address->getTitle(),
            'firstName' => $address->getFirstName(),
            'lastName' => $address->getLastName(),
            'street' => $address->getStreet(),
            'zipcode' => $address->getZipcode(),
            'city' => $address->getCity(),
            'phoneNumber' => $address->getPhoneNumber(),
            'additionalAddressLine1' => $address->getAdditionalAddressLine1(),
            'additionalAddressLine2' => $address->getAdditionalAddressLine2(),
            'countryId' => $address->getCountryId(),
            'countryStateId' => $address->getCountryStateId(),
            'customFields' => $address->getCustomFields(),
        ]);
    }
}