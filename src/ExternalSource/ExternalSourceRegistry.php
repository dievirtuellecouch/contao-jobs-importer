<?php

namespace DVC\JobsImporter\ExternalSource;

use DVC\JobsImporter\ExternalSource\ExternalSourceInterface;

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
