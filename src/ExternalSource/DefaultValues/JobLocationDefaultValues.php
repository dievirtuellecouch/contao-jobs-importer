<?php

namespace DVC\JobsImporter\ExternalSource\DefaultValues;

use DateTime;

class JobLocationDefaultValues
{
    public function __construct(
        private int $parentId = 1,
    )
    {
    }

    public function getAll(): array
    {
        return [
            'tstamp' => new DateTime(),
            'pid' => $this->parentId,
            'jobTypeLocation' => 'onPremise',
            'importDate' => new DateTime(),
        ];
    }
}