<?php declare(strict_types=1);

namespace Laser\Core\Framework\Test\DataAbstractionLayer\Field\TestDefinition;

use Laser\Core\Content\Product\Aggregate\ProductManufacturer\ProductManufacturerDefinition;
use Laser\Core\Framework\DataAbstractionLayer\EntityDefinition;
use Laser\Core\Framework\DataAbstractionLayer\Field\FkField;
use Laser\Core\Framework\DataAbstractionLayer\Field\Flag\ApiAware;
use Laser\Core\Framework\DataAbstractionLayer\Field\Flag\PrimaryKey;
use Laser\Core\Framework\DataAbstractionLayer\Field\Flag\Required;
use Laser\Core\Framework\DataAbstractionLayer\Field\IdField;
use Laser\Core\Framework\DataAbstractionLayer\Field\ManyToOneAssociationField;
use Laser\Core\Framework\DataAbstractionLayer\Field\OneToOneAssociationField;
use Laser\Core\Framework\DataAbstractionLayer\Field\StringField;
use Laser\Core\Framework\DataAbstractionLayer\FieldCollection;
use Laser\Core\Framework\Log\Package;
use Laser\Core\System\Language\LanguageDefinition;

/**
 * @internal
 */
#[Package('inventory')]
class ExtendedProductManufacturerDefinition extends EntityDefinition
{
    public function getEntityName(): string
    {
        return 'extended_product_manufacturer';
    }

    public function since(): ?string
    {
        return '6.0.0.0';
    }

    protected function defineFields(): FieldCollection
    {
        return new FieldCollection([
            (new IdField('id', 'id'))->addFlags(new ApiAware(), new Required(), new PrimaryKey()),
            (new StringField('name', 'name'))->addFlags(new ApiAware()),
            (new FkField('manufacturer_id', 'manufacturerId', ProductManufacturerDefinition::class))->addFlags(new ApiAware()),
            (new FkField('language_id', 'languageId', LanguageDefinition::class))->addFlags(new ApiAware()),
            (new ManyToOneAssociationField('language', 'language_id', LanguageDefinition::class, 'id', false))->addFlags(new ApiAware()),
            (new OneToOneAssociationField('toOne', 'manufacturer_id', 'id', ProductManufacturerDefinition::class))->addFlags(new ApiAware()),
            (new ManyToOneAssociationField('manyToOne', 'manufacturer_id', ProductManufacturerDefinition::class, 'id'))->addFlags(new ApiAware()),
        ]);
    }
}