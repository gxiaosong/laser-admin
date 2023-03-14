<?php declare(strict_types=1);

namespace Laser\Core\Content\Product\Aggregate\ProductVisibility;

use Laser\Core\Content\Product\ProductDefinition;
use Laser\Core\Framework\DataAbstractionLayer\EntityDefinition;
use Laser\Core\Framework\DataAbstractionLayer\Field\FkField;
use Laser\Core\Framework\DataAbstractionLayer\Field\Flag\PrimaryKey;
use Laser\Core\Framework\DataAbstractionLayer\Field\Flag\Required;
use Laser\Core\Framework\DataAbstractionLayer\Field\IdField;
use Laser\Core\Framework\DataAbstractionLayer\Field\IntField;
use Laser\Core\Framework\DataAbstractionLayer\Field\ManyToOneAssociationField;
use Laser\Core\Framework\DataAbstractionLayer\Field\ReferenceVersionField;
use Laser\Core\Framework\DataAbstractionLayer\FieldCollection;
use Laser\Core\Framework\Log\Package;
use Laser\Core\System\SalesChannel\SalesChannelDefinition;

#[Package('inventory')]
class ProductVisibilityDefinition extends EntityDefinition
{
    final public const ENTITY_NAME = 'product_visibility';

    final public const VISIBILITY_LINK = 10;

    final public const VISIBILITY_SEARCH = 20;

    final public const VISIBILITY_ALL = 30;

    public function getEntityName(): string
    {
        return self::ENTITY_NAME;
    }

    public function getEntityClass(): string
    {
        return ProductVisibilityEntity::class;
    }

    public function getCollectionClass(): string
    {
        return ProductVisibilityCollection::class;
    }

    public function since(): ?string
    {
        return '6.0.0.0';
    }

    public function getHydratorClass(): string
    {
        return ProductVisibilityHydrator::class;
    }

    protected function getParentDefinitionClass(): ?string
    {
        return ProductDefinition::class;
    }

    protected function defineFields(): FieldCollection
    {
        return new FieldCollection([
            (new IdField('id', 'id'))->addFlags(new Required(), new PrimaryKey()),

            (new FkField('product_id', 'productId', ProductDefinition::class))->addFlags(new Required()),
            (new ReferenceVersionField(ProductDefinition::class))->addFlags(new Required()),

            (new FkField('sales_channel_id', 'salesChannelId', SalesChannelDefinition::class))->addFlags(new Required()),
            (new IntField('visibility', 'visibility'))->addFlags(new Required()),
            new ManyToOneAssociationField('salesChannel', 'sales_channel_id', SalesChannelDefinition::class, 'id', false),
            new ManyToOneAssociationField('product', 'product_id', ProductDefinition::class, 'id', false),
        ]);
    }
}