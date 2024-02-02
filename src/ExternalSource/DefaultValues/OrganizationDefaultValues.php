<?php

namespace DVC\JobsImporter\ExternalSource\DefaultValues;

use DateTime;

class OrganizationDefaultValues
{
    public function getAll(): array
    {
        return [
            'tstamp' => new DateTime(),
            'sameAs' => '',
            'logo' => null,
        ];
    }
}
