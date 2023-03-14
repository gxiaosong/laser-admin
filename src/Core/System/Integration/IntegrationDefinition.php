<?php declare(strict_types=1);

namespace Laser\Core\System\Integration;

use Laser\Core\Framework\Api\Acl\Role\AclRoleDefinition;
use Laser\Core\Framework\App\AppDefinition;
use Laser\Core\Framework\DataAbstractionLayer\EntityDefinition;
use Laser\Core\Framework\DataAbstractionLayer\Field\BoolField;
use Laser\Core\Framework\DataAbstractionLayer\Field\CustomFields;
use Laser\Core\Framework\DataAbstractionLayer\Field\DateTimeField;
use Laser\Core\Framework\DataAbstractionLayer\Field\Flag\Deprecated;
use Laser\Core\Framework\DataAbstractionLayer\Field\Flag\PrimaryKey;
use Laser\Core\Framework\DataAbstractionLayer\Field\Flag\Required;
use Laser\Core\Framework\DataAbstractionLayer\Field\Flag\RestrictDelete;
use Laser\Core\Framework\DataAbstractionLayer\Field\IdField;
use Laser\Core\Framework\DataAbstractionLayer\Field\ManyToManyAssociationField;
use Laser\Core\Framework\DataAbstractionLayer\Field\OneToOneAssociationField;
use Laser\Core\Framework\DataAbstractionLayer\Field\PasswordField;
use Laser\Core\Framework\DataAbstractionLayer\Field\StringField;
use Laser\Core\Framework\DataAbstractionLayer\FieldCollection;
use Laser\Core\Framework\Feature;
use Laser\Core\Framework\Log\Package;
use Laser\Core\System\Integration\Aggregate\IntegrationRole\IntegrationRoleDefinition;

#[Package('system-settings')]
class IntegrationDefinition extends EntityDefinition
{
    final public const ENTITY_NAME = 'integration';

    public function getEntityName(): string
    {
        return self::ENTITY_NAME;
    }

    public function getCollectionClass(): string
    {
        return IntegrationCollection::class;
    }

    public function getEntityClass(): string
    {
        return IntegrationEntity::class;
    }

    public function getDefaults(): array
    {
        return [
            'admin' => false,
            /** @deprecated tag:v6.6.0 - field will be removed */
            'writeAccess' => false,
        ];
    }

    public function since(): ?string
    {
        return '6.0.0.0';
    }

    protected function defineFields(): FieldCollection
    {
        $collection = new FieldCollection([
            (new IdField('id', 'id'))->addFlags(new PrimaryKey(), new Required()),
            (new StringField('label', 'label'))->addFlags(new Required()),
            (new StringField('access_key', 'accessKey'))->addFlags(new Required()),
            (new PasswordField('secret_access_key', 'secretAccessKey'))->addFlags(new Required()),
            new DateTimeField('last_usage_at', 'lastUsageAt'),
            new BoolField('admin', 'admin'),
            new CustomFields(),
            new DateTimeField('deleted_at', 'deletedAt'),

            (new OneToOneAssociationField('app', 'id', 'integration_id', AppDefinition::class, false))->addFlags(new RestrictDelete()),
            new ManyToManyAssociationField('aclRoles', AclRoleDefinition::class, IntegrationRoleDefinition::class, 'integration_id', 'acl_role_id'),
        ]);

        if (!Feature::isActive('v6.6.0.0')) {
            $collection->add((new BoolField('write_access', 'writeAccess'))->addFlags(new Deprecated('v6.5.0.0', 'v6.6.0.0')));
        }

        return $collection;
    }
}