<?php

declare(strict_types=1);

namespace Mgd\Slider\Exception;

use MGD\Slider\Limits;
use Shopware\Core\Framework\ShopwareHttpException;
use Symfony\Component\HttpFoundation\Response;

/** Limit erreicht > verständliche Meldung aus Limits */
class SliderLimitReachedException extends ShopwareHttpException
{
    public function __construct()
    {
        parent::__construct(Limits::reachedMessage());
    }

    public function getErrorCode(): string
    {
        return 'MGD_SLIDER__LIMIT_REACHED';
    }

    public function getStatusCode(): int
    {
        return Response::HTTP_FORBIDDEN;
    }
}
