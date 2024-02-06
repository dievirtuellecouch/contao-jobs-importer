<?php

namespace DVC\JobsImporterToPlentaBasic\ExternalSource\Sources\Talentstorm\DataTransfer;

use DVC\JobsImporterToPlentaBasic\ExternalSource\DataTransferInterface;
use Symfony\Component\Serializer\Annotation\SerializedPath;

class JobLocationDataTransfer implements DataTransferInterface
{
    #[SerializedPath('[location][id]')]
    public int $id;

    #[SerializedPath('[location][label]')]
    public string $label;

    #[SerializedPath('[location][street]')]
    public string $street;

    #[SerializedPath('[location][zip]')]
    public string $zip;

    #[SerializedPath('[location][city]')]
    public string $city;

    #[SerializedPath('[location][region]')]
    public string $region;

    #[SerializedPath('[location][country][abbreviation2]')]
    public string $country;
}
