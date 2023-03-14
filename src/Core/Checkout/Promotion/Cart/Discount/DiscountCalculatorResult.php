<?php declare(strict_types=1);

namespace Laser\Core\Checkout\Promotion\Cart\Discount;

use Laser\Core\Checkout\Cart\Price\Struct\CalculatedPrice;
use Laser\Core\Checkout\Promotion\Cart\Discount\Composition\DiscountCompositionItem;
use Laser\Core\Framework\Log\Package;

#[Package('checkout')]
class DiscountCalculatorResult
{
    /**
     * @param DiscountCompositionItem[] $compositionItems
     */
    public function __construct(
        private readonly CalculatedPrice $price,
        private readonly array $compositionItems
    ) {
    }

    public function getPrice(): CalculatedPrice
    {
        return $this->price;
    }

    /**
     * @return DiscountCompositionItem[]
     */
    public function getCompositionItems(): array
    {
        return $this->compositionItems;
    }
}