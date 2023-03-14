<?php declare(strict_types=1);

namespace Laser\Core\Content\Cms\Aggregate\CmsSection;

use Laser\Core\Content\Cms\Aggregate\CmsBlock\CmsBlockCollection;
use Laser\Core\Framework\DataAbstractionLayer\EntityCollection;
use Laser\Core\Framework\Log\Package;

/**
 * @extends EntityCollection<CmsSectionEntity>
 */
#[Package('content')]
class CmsSectionCollection extends EntityCollection
{
    public function getBlocks(): CmsBlockCollection
    {
        $blocks = new CmsBlockCollection();

        /** @var CmsSectionEntity $section */
        foreach ($this->elements as $section) {
            if (!$section->getBlocks()) {
                continue;
            }

            $blocks->merge($section->getBlocks());
        }

        return $blocks;
    }

    public function getApiAlias(): string
    {
        return 'cms_page_section_collection';
    }

    protected function getExpectedClass(): string
    {
        return CmsSectionEntity::class;
    }
}