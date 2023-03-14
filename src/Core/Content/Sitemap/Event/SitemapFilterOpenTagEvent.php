<?php declare(strict_types=1);

namespace Laser\Core\Content\Sitemap\Event;

use Laser\Core\Framework\Context;
use Laser\Core\Framework\Event\LaserEvent;
use Laser\Core\Framework\Log\Package;
use Laser\Core\System\SalesChannel\SalesChannelContext;
use Symfony\Contracts\EventDispatcher\Event;

#[Package('sales-channel')]
class SitemapFilterOpenTagEvent extends Event implements LaserEvent
{
    private string $openTag = '<?xml version="1.0" encoding="UTF-8"?><urlset %urlsetNamespaces%>';

    /**
     * @var array<string, string>
     */
    private array $urlsetNamespaces = [
        'xmlns' => 'http://www.sitemaps.org/schemas/sitemap/0.9',
    ];

    public function __construct(private readonly SalesChannelContext $salesChannelContext)
    {
    }

    public function getSalesChannelContext(): SalesChannelContext
    {
        return $this->salesChannelContext;
    }

    public function getContext(): Context
    {
        return $this->salesChannelContext->getContext();
    }

    public function getOpenTag(): string
    {
        return $this->openTag;
    }

    public function getFullOpenTag(): string
    {
        $namespaces = '';
        foreach ($this->urlsetNamespaces as $name => $namespace) {
            $namespaces .= sprintf(' %s="%s"', $name, $namespace);
        }

        return strtr($this->openTag, [
            '%urlsetNamespaces%' => trim($namespaces),
        ]);
    }

    public function setOpenTag(string $openTag): void
    {
        $this->openTag = $openTag;
    }

    /**
     * @return array<string, string>
     */
    public function getUrlsetNamespaces(): array
    {
        return $this->urlsetNamespaces;
    }

    /**
     * @param array<string, string> $urlsetNamespaces
     */
    public function setUrlsetNamespaces(array $urlsetNamespaces): void
    {
        $this->urlsetNamespaces = $urlsetNamespaces;
    }

    public function addUrlsetNamespace(string $name, string $namespace): void
    {
        $this->urlsetNamespaces[$name] = $namespace;
    }
}