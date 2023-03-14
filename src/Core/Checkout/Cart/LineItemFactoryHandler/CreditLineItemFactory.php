<?php declare(strict_types=1);

namespace Laser\Core\Checkout\Cart\LineItemFactoryHandler;

use Laser\Core\Checkout\Cart\CartException;
use Laser\Core\Checkout\Cart\LineItem\LineItem;
use Laser\Core\Checkout\Cart\PriceDefinitionFactory;
use Laser\Core\Content\Product\Cart\ProductCartProcessor;
use Laser\Core\Framework\DataAbstractionLayer\EntityRepository;
use Laser\Core\Framework\DataAbstractionLayer\Search\Criteria;
use Laser\Core\Framework\Log\Package;
use Laser\Core\System\SalesChannel\SalesChannelContext;

#[Package('checkout')]
class CreditLineItemFactory implements LineItemFactoryInterface
{
    /**
     * @internal
     */
    public function __construct(
        private readonly PriceDefinitionFactory $priceDefinitionFactory,
        private readonly EntityRepository $mediaRepository
    ) {
    }

    public function supports(string $type): bool
    {
        return $type === LineItem::CREDIT_LINE_ITEM_TYPE;
    }

    /**
     * @param array<mixed> $data
     */
    public function create(array $data, SalesChannelContext $context): LineItem
    {
        if (!$context->hasPermission(ProductCartProcessor::ALLOW_PRODUCT_PRICE_OVERWRITES)) {
            throw CartException::insufficientPermission();
        }

        $lineItem = new LineItem($data['id'], $data['type'], $data['referencedId'] ?? null, $data['quantity'] ?? 1);
        $lineItem->markModified();

        $this->update($lineItem, $data, $context);

        return $lineItem;
    }

    /**
     * @param array<mixed> $data
     */
    public function update(LineItem $lineItem, array $data, SalesChannelContext $context): void
    {
        if (!$context->hasPermission(ProductCartProcessor::ALLOW_PRODUCT_PRICE_OVERWRITES)) {
            throw CartException::insufficientPermission();
        }

        if (isset($data['removable'])) {
            $lineItem->setRemovable($data['removable']);
        }

        if (isset($data['stackable'])) {
            $lineItem->setStackable($data['stackable']);
        }

        if (isset($data['label'])) {
            $lineItem->setLabel($data['label']);
        }

        if (isset($data['description'])) {
            $lineItem->setDescription($data['description']);
        }

        if (isset($data['coverId'])) {
            $cover = $this->mediaRepository->search(new Criteria([$data['coverId']]), $context->getContext())->first();

            $lineItem->setCover($cover);
        }

        if (isset($data['priceDefinition'])) {
            $lineItem->setPriceDefinition($this->priceDefinitionFactory->factory($context->getContext(), $data['priceDefinition'], $data['type']));
        }
    }
}