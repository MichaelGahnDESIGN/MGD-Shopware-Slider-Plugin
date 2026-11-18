<?php

declare(strict_types=1);

namespace Mgd\Slider\ScheduledTask;

use Shopware\Core\Framework\MessageQueue\ScheduledTask\ScheduledTask;

// Täglicher Aktiv-Ping (Free-Lizenz)
class InstallPingTask extends ScheduledTask
{
    public static function getTaskName(): string
    {
        return 'mgd_slider.install_ping';
    }

    public static function getDefaultInterval(): int
    {
        return 86400;
    }
}
