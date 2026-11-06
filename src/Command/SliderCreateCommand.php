<?php

declare(strict_types=1);

namespace Mgd\Slider\Command;

use Mgd\Slider\Exception\SliderLimitReachedException;
use Mgd\Slider\Service\SliderService;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

/*
 CLI mgd:slider:create - Slider anlegen, Limit gilt auch hier
 Config aus --config-file (JSON) oder aus den Prime Optionen
*/
#[AsCommand(name: 'mgd:slider:create', description: 'MGD Slider anlegen (Klassisch oder Prime)')]
class SliderCreateCommand extends Command
{
    public function __construct(private readonly SliderService $sliders)
    {
        parent::__construct();
    }

    protected function configure(): void
    {
        $this->addArgument('name', InputArgument::REQUIRED, 'Name des Sliders')
            ->addOption('mode', null, InputOption::VALUE_REQUIRED, 'classic oder prime', 'classic')
            ->addOption('config-file', null, InputOption::VALUE_REQUIRED, 'JSON Datei mit allen Einstellungen (inkl. slides)')
            ->addOption('prime-source', null, InputOption::VALUE_REQUIRED, 'random, bestseller, category oder manual', 'random')
            ->addOption('prime-count', null, InputOption::VALUE_REQUIRED, 'Anzahl Slides bei Prime (1-12)', '5');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $config = [];
        $file = $input->getOption('config-file');
        if (is_string($file) && $file !== '') {
            $json = is_file($file) ? json_decode((string) file_get_contents($file), true) : null;
            if (!is_array($json)) {
                $output->writeln('<error>Config-Datei nicht lesbar oder kein gültiges JSON: ' . $file . '</error>');

                return Command::FAILURE;
            }
            $config = $json;
        }
        $config['mode'] ??= (string) $input->getOption('mode');
        $config['prime'] = array_merge(
            ['source' => (string) $input->getOption('prime-source'), 'count' => (int) $input->getOption('prime-count')],
            is_array($config['prime'] ?? null) ? $config['prime'] : []
        );

        try {
            $id = $this->sliders->create((string) $input->getArgument('name'), $config);
        } catch (SliderLimitReachedException $e) {
            $output->writeln('<error>' . $e->getMessage() . '</error>');

            return Command::FAILURE;
        }
        $output->writeln('Slider angelegt: ' . $id);

        return Command::SUCCESS;
    }
}
