<?php

namespace DVC\JobsImporterToPlentaBasic\ExternalSource\DefaultValues;

use DateTime;

class JobLocationDefaultValues
{
    public function getAll(): array
    {
        return [
            'tstamp' => new DateTime(),
            'jobTypeLocation' => 'onPremise',
            'importDate' => new DateTime(),
        ];
    }
}