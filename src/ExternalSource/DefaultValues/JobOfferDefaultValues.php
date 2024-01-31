<?php

namespace DVC\JobsImporter\ExternalSource\DefaultValues;

use DateTime;

class JobOfferDefaultValues
{
    public function getAll(): array
    {
        return [
            'validThrough' => null,
            'directApply' => true,
            'translations' => [],
            'teaser' => null,
            'pageTitle' => null,
            'robots' => 'index,follow',
            'author' => 0,
            'pageDescription' => null,
            'importDate' => new DateTime(),
        ];
    }
}