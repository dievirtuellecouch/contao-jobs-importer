<?php

namespace DVC\JobsImporter\Command;

use Contao\CoreBundle\Framework\ContaoFramework;
use DVC\JobsImporter\Import\Importer;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

#[AsCommand('dvc-jobs-importer:import-all')]
class ImportCommand extends Command
{
    public function __construct(
        private ContaoFramework $framework,
        private Importer $importer,
    )
    {
        parent::__construct();
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $this->framework->initialize();

        $this->importer->importAll();

        return Command::SUCCESS;
    }
}
