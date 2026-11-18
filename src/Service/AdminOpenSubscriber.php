<?php

declare(strict_types=1);

namespace Mgd\Slider\Service;

use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpKernel\Event\TerminateEvent;
use Symfony\Component\HttpKernel\KernelEvents;

// Shops ohne Cron: beim Öffnen der Administration pingen (nach der Antwort, bremst nix)
final class AdminOpenSubscriber implements EventSubscriberInterface
{
    public function __construct(private readonly InstallPing $installPing)
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
        $this->installPing->ping();
    }
}
