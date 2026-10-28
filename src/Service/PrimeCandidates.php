<?php

declare(strict_types=1);

namespace Mgd\Slider\Service;

use MGD\Slider\Prime;
use Shopware\Core\Content\Product\ProductCollection;
use Shopware\Core\Content\Product\ProductEntity;
use Shopware\Core\Content\Seo\SeoUrlPlaceholderHandlerInterface;
use Shopware\Core\Framework\DataAbstractionLayer\Search\Criteria;
use Shopware\Core\Framework\DataAbstractionLayer\Search\Filter\EqualsAnyFilter;
use Shopware\Core\Framework\DataAbstractionLayer\Search\Filter\EqualsFilter;
use Shopware\Core\Framework\DataAbstractionLayer\Search\Filter\MultiFilter;
use Shopware\Core\Framework\DataAbstractionLayer\Search\Sorting\FieldSorting;
use Shopware\Core\Framework\Uuid\Uuid;
use Shopware\Core\System\Currency\CurrencyFormatter;
use Shopware\Core\System\SalesChannel\Entity\SalesChannelRepository;
use Shopware\Core\System\SalesChannel\SalesChannelContext;

/**
 * Prime-Datenabfrage über das Shopware Product Repo (DAL).
 * UNGETESTET: nicht gegen eine echte Shopware-Instanz geprüft, Pre-Alpha.
 */
class PrimeCandidates
{
    // so viele Artikel holen wir maximal, Prime::select wählt danach aus
    private const POOL_LIMIT = 60;

    /**
     * @param SalesChannelRepository<ProductCollection> $productRepository
     */
    public function __construct(
        private readonly SalesChannelRepository $productRepository,
        private readonly CurrencyFormatter $currencyFormatter,
        private readonly SeoUrlPlaceholderHandlerInterface $seoUrls
    ) {
    }

    /**
     * Fertige Slides für einen Prime-Slider
     *
     * @param array<string,mixed> $cfg normalisierter Slider (Config::normalize)
     * @return list<array<string,string>>
     */
    public function slides(array $cfg, SalesChannelContext $context): array
    {
        $source = $cfg['prime']['source'];
        $criteria = $this->baseCriteria();

        if ($source === 'bestseller') {
            $criteria->addSorting(new FieldSorting('sales', FieldSorting::DESCENDING));
        } elseif ($source === 'category') {
            // ohne Kategorie gibts nix
            if ($cfg['prime']['categories'] === []) {
                return [];
            }
            $criteria->addFilter(new EqualsAnyFilter('product.categoryTree', $cfg['prime']['categories']));
        } elseif ($source === 'manual') {
            if ($cfg['prime']['products'] === []) {
                return [];
            }
            $criteria->addFilter($this->manualFilter($cfg['prime']['products']));
        }

        /** @var ProductCollection $products */
        $products = $this->productRepository->search($criteria, $context)->getEntities();

        $candidates = [];
        foreach ($products as $product) {
            $candidates[] = $this->toCandidate($product, $context);
        }

        if ($source === 'manual') {
            // Eingabe darf Artikelnummer, ID oder Name sein > auf die gefundenen IDs umbiegen
            $cfg['prime']['products'] = array_map(static fn (array $c): string => $c['id'], $candidates);
        }

        return Prime::select($candidates, $cfg);
    }

    private function baseCriteria(): Criteria
    {
        $criteria = new Criteria();
        $criteria->setLimit(self::POOL_LIMIT);
        $criteria->addAssociation('cover.media');
        // nur Hauptartikel, sonst kommen Varianten doppelt
        $criteria->addFilter(new EqualsFilter('product.parentId', null));

        return $criteria;
    }

    /**
     * Manuell: Artikelnummer, ID oder Name (exakt)
     *
     * @param list<string> $terms
     */
    private function manualFilter(array $terms): MultiFilter
    {
        $ids = array_values(array_filter($terms, static fn (string $t): bool => Uuid::isValid($t)));

        $filters = [
            new EqualsAnyFilter('product.productNumber', $terms),
            new EqualsAnyFilter('product.name', $terms),
        ];
        if ($ids !== []) {
            $filters[] = new EqualsAnyFilter('product.id', $ids);
        }

        return new MultiFilter(MultiFilter::CONNECTION_OR, $filters);
    }

    /** @return array<string,mixed> Format wie Prime::select es erwartet */
    private function toCandidate(ProductEntity $product, SalesChannelContext $context): array
    {
        $media = $product->getCover()?->getMedia();
        $name = (string) ($product->getTranslation('name') ?? $product->getName());
        $price = $product->get('calculatedPrice');
        $priceText = '';
        if ($price !== null && method_exists($price, 'getUnitPrice')) {
            $priceText = $this->currencyFormatter->formatCurrencyByLanguage(
                (float) $price->getUnitPrice(),
                $context->getCurrency()->getIsoCode(),
                $context->getLanguageId(),
                $context->getContext()
            );
        }

        return [
            'id' => $product->getId(),
            'sku' => (string) $product->getProductNumber(),
            'name' => $name,
            // Platzhalter wird beim Ausliefern der Seite durch die SEO-URL ersetzt
            'url' => $this->seoUrls->generate('frontend.detail.page', ['productId' => $product->getId()]),
            'image' => (string) $media?->getUrl(),
            'alt' => (string) ($media?->getTranslation('alt') ?? ''),
            'price' => $priceText,
            'sales' => (int) $product->getSales(),
            'categories' => array_values($product->getCategoryTree() ?? []),
            'inStock' => !$product->getIsCloseout() || (int) $product->getAvailableStock() > 0,
        ];
    }
}
