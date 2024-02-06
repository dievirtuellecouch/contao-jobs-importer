<?php

namespace DVC\JobsImporterToPlentaBasic\ExternalSource;

use DVC\JobsImporterToPlentaBasic\ExternalSource\ExternalSourceInterface;

class ExternalSourceRegistry
{
    public function __construct(
        private array $configuredSources,
    ) {
    }

    public function getAll(): array
    {
        return $this->configuredSources;
    }
}
