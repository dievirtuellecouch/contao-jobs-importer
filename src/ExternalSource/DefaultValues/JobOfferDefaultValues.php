<?php

namespace DVC\JobsImporterToPlentaBasic\ExternalSource\DefaultValues;

use DateTime;

class JobOfferDefaultValues
{
    public function getAll(): array
    {
        return [
            'validThrough' => null,
            'directApply' => true,
            'translations' => [],
            'robots' => 'index,follow',
            'author' => 0,
            'importDate' => new DateTime(),
        ];
    }
}