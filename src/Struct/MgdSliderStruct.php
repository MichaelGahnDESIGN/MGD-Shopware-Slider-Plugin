<?php

declare(strict_types=1);

namespace Mgd\Slider\Struct;

use Shopware\Core\Framework\Struct\Struct;

/** Daten für das CMS Element (Twig: element.data.html) */
class MgdSliderStruct extends Struct
{
    public function __construct(protected string $html = '')
    {
    }

    public function getHtml(): string
    {
        return $this->html;
    }

    public function getApiAlias(): string
    {
        return 'cms_mgd_slider';
    }
}
