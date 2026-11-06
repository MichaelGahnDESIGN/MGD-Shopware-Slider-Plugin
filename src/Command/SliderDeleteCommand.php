<?php

declare(strict_types=1);

namespace Mgd\Slider\Command;

use Mgd\Slider\Service\SliderService;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

// CLI mgd:slider:delete
#[AsCommand(name: 'mgd:slider:delete', description: 'MGD Slider löschen')]
class SliderDeleteCommand extends Command
{
    public function __construct(private readonly SliderService $sliders)
    {
        parent::__construct();
    }

    protected function configure(): void
    {
        $this->addArgument('id', InputArgument::REQUIRED, 'Slider-ID (siehe mgd:slider:list)');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $this->sliders->delete((string) $input->getArgument('id'));
        $output->writeln('Gelöscht (falls vorhanden).');

        return Command::SUCCESS;
    }
}
