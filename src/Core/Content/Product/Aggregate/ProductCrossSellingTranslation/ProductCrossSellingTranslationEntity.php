<?php declare(strict_types=1);

namespace Laser\Core\Content\Product\Aggregate\ProductCrossSellingTranslation;

use Laser\Core\Content\Product\Aggregate\ProductCrossSelling\ProductCrossSellingEntity;
use Laser\Core\Framework\DataAbstractionLayer\TranslationEntity;
use Laser\Core\Framework\Log\Package;

#[Package('inventory')]
class ProductCrossSellingTranslationEntity extends TranslationEntity
{
    /**
     * @var string
     */
    protected $productCrossSellingId;

    /**
     * @var string|null
     */
    protected $name;

    /**
     * @var ProductCrossSellingEntity|null
     */
    protected $productCrossSelling;

    public function getProductCrossSellingId(): string
    {
        return $this->productCrossSellingId;
    }

    public function setProductCrossSellingId(string $productCrossSellingId): void
    {
        $this->productCrossSellingId = $productCrossSellingId;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(?string $name): void
    {
        $this->name = $name;
    }

    public function getProductCrossSelling(): ?ProductCrossSellingEntity
    {
        return $this->productCrossSelling;
    }

    public function setProductCrossSelling(ProductCrossSellingEntity $productCrossSelling): void
    {
        $this->productCrossSelling = $productCrossSelling;
    }
}