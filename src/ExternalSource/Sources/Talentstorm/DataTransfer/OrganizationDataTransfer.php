<?php

namespace DVC\JobsImporterToPlentaBasic\ExternalSource\Sources\Talentstorm\DataTransfer;

use DVC\JobsImporterToPlentaBasic\ExternalSource\DataTransferInterface;
use Symfony\Component\Serializer\Annotation\SerializedPath;

class OrganizationDataTransfer implements DataTransferInterface
{
    #[SerializedPath('[location][label]')]
    public string $label;
}
