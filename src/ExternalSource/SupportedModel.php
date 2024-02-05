<?php

namespace DVC\JobsImporterToPlentaBasic\ExternalSource;

enum SupportedModel: string
{
    case Location = 'location';
    case Offer = 'offer';
    case Organization = 'organization';
}
