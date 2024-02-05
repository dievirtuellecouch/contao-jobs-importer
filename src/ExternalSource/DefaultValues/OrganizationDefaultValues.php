<?php

namespace DVC\JobsImporterToPlentaBasic\ExternalSource\DefaultValues;

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
