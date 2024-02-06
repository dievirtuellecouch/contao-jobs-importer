<?php

namespace DVC\JobsImporterToPlentaBasic\ExternalSource\Sources\Talentstorm\DataTransfer;

use DVC\JobsImporterToPlentaBasic\ExternalSource\DataTransferInterface;

class JobTypeDataTransfer implements DataTransferInterface
{
    public string $label;

    public string $labelPlural;
}
