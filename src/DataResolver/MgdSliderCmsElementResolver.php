<?php

declare(strict_types=1);

namespace Mgd\Slider\DataResolver;

use Mgd\Slider\Service\SliderHtml;
use Mgd\Slider\Struct\MgdSliderStruct;
use Shopware\Core\Content\Cms\Aggregate\CmsSlot\CmsSlotEntity;
use Shopware\Core\Content\Cms\DataResolver\CriteriaCollection;
use Shopware\Core\Content\Cms\DataResolver\Element\AbstractCmsElementResolver;
use Shopware\Core\Content\Cms\DataResolver\Element\ElementDataCollection;
use Shopware\Core\Content\Cms\DataResolver\ResolverContext\ResolverContext;

/**
 * CMS Element mgd-slider: Config liefert die Slider-Id, HTML kommt aus SliderHtml.
 * 
 */
class MgdSliderCmsElementResolver extends AbstractCmsElementResolver
{
    // damit CSS/JS pro Seite nur einmal eingebunden wird
    private bool $assetsDone = false;

    public function __construct(private readonly SliderHtml $sliderHtml)
    {
    }

    public function getType(): string
    {
        return 'mgd-slider';
    }

    public function collect(CmsSlotEntity $slot, ResolverContext $resolverContext): ?CriteriaCollection
    {
        // keine DAL Criteria nötig, Daten holt enrich selbst
        return null;
    }

    public function enrich(CmsSlotEntity $slot, ResolverContext $resolverContext, ElementDataCollection $result): void
    {
        $config = $slot->getFieldConfig()->get('sliderId');
        $sliderId = $config?->getValue();

        if (!is_string($sliderId) || $sliderId === '') {
            $slot->setData(new MgdSliderStruct());

            return;
        }

        $html = $this->sliderHtml->render($sliderId, $resolverContext->getSalesChannelContext());
        $first = !$this->assetsDone && $html !== '';
        if ($html !== '') {
            $this->assetsDone = true;
        }
        $slot->setData(new MgdSliderStruct($html, $first));
    }
}
