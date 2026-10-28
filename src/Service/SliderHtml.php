<?php

declare(strict_types=1);

namespace Mgd\Slider\Service;

use MGD\Slider\Renderer;
use Shopware\Core\System\SalesChannel\SalesChannelContext;

/**
 * Slider-Id rein > fertiger HTML Block aus MGD\Slider\Renderer raus
 */
class SliderHtml
{
    // zählt mit, damit zwei gleiche Slider auf einer Seite unterschiedliche IDs bekommen
    private int $counter = 0;

    public function __construct(
        private readonly SliderService $sliders,
        private readonly PrimeCandidates $prime
    ) {
    }

    public function render(string $sliderId, SalesChannelContext $context): string
    {
        $slider = $this->sliders->get($sliderId);
        if ($slider === null) {
            return '';
        }

        $cfg = $slider['config'];
        $slides = $cfg['mode'] === 'prime' ? $this->prime->slides($cfg, $context) : $cfg['slides'];

        return Renderer::render($cfg, $slides, substr($slider['id'], 0, 8) . '-' . (++$this->counter));
    }
}
