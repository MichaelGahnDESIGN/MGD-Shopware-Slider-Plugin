<?php

declare(strict_types=1);

namespace Mgd\Slider\Service;

use Mgd\Slider\Kit\Platform\Shopware\ShopwareKit;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpKernel\Event\TerminateEvent;
use Symfony\Component\HttpKernel\KernelEvents;

// Shops ohne Cron: beim Öffnen der Administration melden (nach der Antwort, bremst nix). Max 1x am Tag macht der Client selbst
final class AdminOpenSubscriber implements EventSubscriberInterface
{
    public function __construct(private readonly ShopwareKit $kit)
    {
    }

    public static function getSubscribedEvents(): array
    {
        return [KernelEvents::TERMINATE => 'onTerminate'];
    }

    public function onTerminate(TerminateEvent $event): void
    {
        if ($event->getRequest()->attributes->get('_route') !== 'administration.index') {
            return;
        }
        try {
            $this->kit->installClient()?->ping();
        } catch (\Throwable) {
        }
    }
}
