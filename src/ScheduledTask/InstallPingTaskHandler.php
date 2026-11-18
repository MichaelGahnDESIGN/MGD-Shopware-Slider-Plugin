<?php

declare(strict_types=1);

namespace Mgd\Slider\ScheduledTask;

use Mgd\Slider\Service\InstallPing;
use Psr\Log\LoggerInterface;
use Shopware\Core\Framework\DataAbstractionLayer\EntityRepository;
use Shopware\Core\Framework\MessageQueue\ScheduledTask\ScheduledTaskHandler;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;

#[AsMessageHandler(handles: InstallPingTask::class)]
final class InstallPingTaskHandler extends ScheduledTaskHandler
{
    public function __construct(
        EntityRepository $scheduledTaskRepository,
        LoggerInterface $exceptionLogger,
        private readonly InstallPing $installPing,
    ) {
        parent::__construct($scheduledTaskRepository, $exceptionLogger);
    }

    public function run(): void
    {
        // max 1x am Tag, macht der Client selbst
        $this->installPing->ping();
    }
}
