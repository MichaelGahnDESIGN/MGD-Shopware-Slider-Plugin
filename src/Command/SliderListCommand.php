<?php

declare(strict_types=1);

namespace Mgd\Slider\Command;

use Mgd\Slider\Service\SliderService;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Helper\Table;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

// CLI mgd:slider:list
#[AsCommand(name: 'mgd:slider:list', description: 'Alle MGD Slider anzeigen')]
class SliderListCommand extends Command
{
    public function __construct(private readonly SliderService $sliders)
    {
        parent::__construct();
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $rows = [];
        foreach ($this->sliders->list() as $s) {
            $rows[] = [$s['id'], $s['name'], $s['config']['mode'], count($s['config']['slides'])];
        }
        (new Table($output))->setHeaders(['ID', 'Name', 'Modus', 'Slides'])->setRows($rows)->render();

        return Command::SUCCESS;
    }
}
