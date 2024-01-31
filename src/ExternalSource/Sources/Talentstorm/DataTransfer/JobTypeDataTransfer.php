<?php

namespace DVC\JobsImporter\ExternalSource\Sources\Talentstorm\DataTransfer;

use DVC\JobsImporter\ExternalSource\DataTransferInterface;

class JobTypeDataTransfer implements DataTransferInterface
{
    public string $label;

    public string $labelPlural;
}
