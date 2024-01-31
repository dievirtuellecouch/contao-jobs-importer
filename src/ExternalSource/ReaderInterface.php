<?php

namespace DVC\JobsImporter\ExternalSource;

interface ReaderInterface
{
    public function getAllAvailableJobs(): ?array;

    public function getAllLocations(): ?array;

    public function getItemsForIdentifier(SupportedModel $identifier): ?array;
}
