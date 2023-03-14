<?php declare(strict_types=1);

namespace Laser\Core\Content\Product\Aggregate\ProductSearchConfigField;

use Laser\Core\Content\Product\Aggregate\ProductSearchConfig\ProductSearchConfigDefinition;
use Laser\Core\Framework\DataAbstractionLayer\EntityDefinition;
use Laser\Core\Framework\DataAbstractionLayer\Field\BoolField;
use Laser\Core\Framework\DataAbstractionLayer\Field\FkField;
use Laser\Core\Framework\DataAbstractionLayer\Field\Flag\PrimaryKey;
use Laser\Core\Framework\DataAbstractionLayer\Field\Flag\Required;
use Laser\Core\Framework\DataAbstractionLayer\Field\IdField;
use Laser\Core\Framework\DataAbstractionLayer\Field\IntField;
use Laser\Core\Framework\DataAbstractionLayer\Field\ManyToOneAssociationField;
use Laser\Core\Framework\DataAbstractionLayer\Field\StringField;
use Laser\Core\Framework\DataAbstractionLayer\FieldCollection;
use Laser\Core\Framework\Log\Package;
use Laser\Core\System\CustomField\CustomFieldDefinition;

#[Package('inventory')]
class ProductSearchConfigFieldDefinition extends EntityDefinition
{
    final public const ENTITY_NAME = 'product_search_config_field';

    public function getEntityName(): string
    {
        return self::ENTITY_NAME;
    }

    public function getEntityClass(): string
    {
        return ProductSearchConfigFieldEntity::class;
    }

    public function getCollectionClass(): string
    {
        return ProductSearchConfigFieldCollection::class;
    }

    public function since(): ?string
    {
        return '6.3.5.0';
    }

    public function getDefaults(): array
    {
        return [
            'tokenize' => false,
            'searchable' => false,
            'ranking' => 0,
        ];
    }

    public function getHydratorClass(): string
    {
        return ProductSearchConfigFieldHydrator::class;
    }

    protected function getParentDefinitionClass(): ?string
    {
        return ProductSearchConfigDefinition::class;
    }

    protected function defineFields(): FieldCollection
    {
        return new FieldCollection([
            (new IdField('id', 'id'))->addFlags(new PrimaryKey(), new Required()),
            (new FkField('product_search_config_id', 'searchConfigId', ProductSearchConfigDefinition::class))->addFlags(new Required()),
            (new FkField('custom_field_id', 'customFieldId', CustomFieldDefinition::class)),
            (new StringField('field', 'field'))->addFlags(new Required()),
            (new BoolField('tokenize', 'tokenize'))->addFlags(new Required()),
            (new BoolField('searchable', 'searchable'))->addFlags(new Required()),
            (new IntField('ranking', 'ranking'))->addFlags(new Required()),
            new ManyToOneAssociationField('searchConfig', 'product_search_config_id', ProductSearchConfigDefinition::class, 'id', false),
            new ManyToOneAssociationField('customField', 'custom_field_id', CustomFieldDefinition::class, 'id', false),
        ]);
    }
}