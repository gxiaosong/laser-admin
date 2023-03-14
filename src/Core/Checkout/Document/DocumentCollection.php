<?php declare(strict_types=1);

namespace Laser\Core\Checkout\Document;

use Laser\Core\Framework\DataAbstractionLayer\EntityCollection;
use Laser\Core\Framework\Log\Package;

/**
 * @extends EntityCollection<DocumentEntity>
 */
#[Package('customer-order')]
class DocumentCollection extends EntityCollection
{
    public function getApiAlias(): string
    {
        return 'document_collection';
    }

    protected function getExpectedClass(): string
    {
        return DocumentEntity::class;
    }
}