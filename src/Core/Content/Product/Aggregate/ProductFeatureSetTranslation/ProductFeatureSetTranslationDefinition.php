<?php declare(strict_types=1);

namespace Laser\Core\Content\Product\Aggregate\ProductFeatureSetTranslation;

use Laser\Core\Content\Product\Aggregate\ProductFeatureSet\ProductFeatureSetDefinition;
use Laser\Core\Framework\DataAbstractionLayer\EntityTranslationDefinition;
use Laser\Core\Framework\DataAbstractionLayer\Field\Flag\ApiAware;
use Laser\Core\Framework\DataAbstractionLayer\Field\Flag\Required;
use Laser\Core\Framework\DataAbstractionLayer\Field\StringField;
use Laser\Core\Framework\DataAbstractionLayer\FieldCollection;
use Laser\Core\Framework\Log\Package;

#[Package('inventory')]
class ProductFeatureSetTranslationDefinition extends EntityTranslationDefinition
{
    final public const ENTITY_NAME = ProductFeatureSetDefinition::ENTITY_NAME . '_translation';

    public function getCollectionClass(): string
    {
        return ProductFeatureSetTranslationCollection::class;
    }

    public function getEntityClass(): string
    {
        return ProductFeatureSetTranslationEntity::class;
    }

    public function getEntityName(): string
    {
        return self::ENTITY_NAME;
    }

    public function getParentDefinitionClass(): string
    {
        return ProductFeatureSetDefinition::class;
    }

    public function since(): ?string
    {
        return '6.3.0.0';
    }

    protected function defineFields(): FieldCollection
    {
        return new FieldCollection([
            (new StringField('name', 'name'))->addFlags(new Required(), new ApiAware()),
            (new StringField('description', 'description'))->addFlags(new ApiAware()),
        ]);
    }
}