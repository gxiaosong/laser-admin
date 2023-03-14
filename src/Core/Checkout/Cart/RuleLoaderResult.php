<?php declare(strict_types=1);

namespace Laser\Core\Checkout\Cart;

use Laser\Core\Content\Rule\RuleCollection;
use Laser\Core\Framework\Log\Package;

#[Package('checkout')]
class RuleLoaderResult
{
    public function __construct(
        private readonly Cart $cart,
        private readonly RuleCollection $matchingRules
    ) {
    }

    public function getCart(): Cart
    {
        return $this->cart;
    }

    public function getMatchingRules(): RuleCollection
    {
        return $this->matchingRules;
    }
}