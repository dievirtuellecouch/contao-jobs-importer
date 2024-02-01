<?php

namespace DVC\JobsImporter\Cron;

use Contao\CoreBundle\DependencyInjection\Attribute\AsCronJob;
use DVC\JobsImporter\Import\Importer;

#[AsCronJob('hourly')]
class ImportCron
{
    public function __construct(
        private Importer $importer,
    ) {
    }

    public function __invoke()
    {
        $this->importer->importAll();
    }
}
