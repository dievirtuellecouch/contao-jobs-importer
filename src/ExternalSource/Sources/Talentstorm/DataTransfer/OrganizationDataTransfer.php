<?php

namespace DVC\JobsImporter\ExternalSource\Sources\Talentstorm\DataTransfer;

use DVC\JobsImporter\ExternalSource\DataTransferInterface;
use Symfony\Component\Serializer\Annotation\SerializedPath;

class OrganizationDataTransfer implements DataTransferInterface
{
    #[SerializedPath('[location][label]')]
    public string $label;
}
