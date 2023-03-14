<?php declare(strict_types=1);

namespace Laser\Core\Content\Product\Aggregate\ProductConfiguratorSetting;

use Laser\Core\Content\Media\MediaDefinition;
use Laser\Core\Content\Product\ProductDefinition;
use Laser\Core\Content\Property\Aggregate\PropertyGroupOption\PropertyGroupOptionDefinition;
use Laser\Core\Framework\DataAbstractionLayer\EntityDefinition;
use Laser\Core\Framework\DataAbstractionLayer\Field\CustomFields;
use Laser\Core\Framework\DataAbstractionLayer\Field\FkField;
use Laser\Core\Framework\DataAbstractionLayer\Field\Flag\ApiAware;
use Laser\Core\Framework\DataAbstractionLayer\Field\Flag\PrimaryKey;
use Laser\Core\Framework\DataAbstractionLayer\Field\Flag\Required;
use Laser\Core\Framework\DataAbstractionLayer\Field\IdField;
use Laser\Core\Framework\DataAbstractionLayer\Field\IntField;
use Laser\Core\Framework\DataAbstractionLayer\Field\JsonField;
use Laser\Core\Framework\DataAbstractionLayer\Field\ManyToOneAssociationField;
use Laser\Core\Framework\DataAbstractionLayer\Field\ReferenceVersionField;
use Laser\Core\Framework\DataAbstractionLayer\Field\VersionField;
use Laser\Core\Framework\DataAbstractionLayer\FieldCollection;
use Laser\Core\Framework\Log\Package;

#[Package('inventory')]
class ProductConfiguratorSettingDefinition extends EntityDefinition
{
    final public const ENTITY_NAME = 'product_configurator_setting';

    public function getEntityName(): string
    {
        return self::ENTITY_NAME;
    }

    public function getCollectionClass(): string
    {
        return ProductConfiguratorSettingCollection::class;
    }

    public function getEntityClass(): string
    {
        return ProductConfiguratorSettingEntity::class;
    }

    public function since(): ?string
    {
        return '6.0.0.0';
    }

    public function getHydratorClass(): string
    {
        return ProductConfiguratorSettingHydrator::class;
    }

    protected function getParentDefinitionClass(): ?string
    {
        return ProductDefinition::class;
    }

    protected function defineFields(): FieldCollection
    {
        return new FieldCollection([
            (new IdField('id', 'id'))->addFlags(new ApiAware(), new PrimaryKey(), new Required()),
            (new VersionField())->addFlags(new ApiAware()),
            (new FkField('product_id', 'productId', ProductDefinition::class))->addFlags(new ApiAware(), new Required()),
            (new ReferenceVersionField(ProductDefinition::class))->addFlags(new ApiAware(), new Required()),
            (new FkField('media_id', 'mediaId', MediaDefinition::class))->addFlags(new ApiAware()),
            (new FkField('property_group_option_id', 'optionId', PropertyGroupOptionDefinition::class))->addFlags(new ApiAware(), new Required()),
            new JsonField('price', 'price'),
            (new IntField('position', 'position'))->addFlags(new ApiAware()),
            new ManyToOneAssociationField('product', 'product_id', ProductDefinition::class, 'id', false),
            (new ManyToOneAssociationField('media', 'media_id', MediaDefinition::class, 'id', false))->addFlags(new ApiAware()),
            (new ManyToOneAssociationField('option', 'property_group_option_id', PropertyGroupOptionDefinition::class, 'id', false))->addFlags(new ApiAware()),
            (new CustomFields())->addFlags(new ApiAware()),
        ]);
    }
}