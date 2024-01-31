<?php

namespace DVC\JobsImporter\ExternalSource;

enum SupportedModel: string
{
    case Location = 'location';
    case Offer = 'offer';
}
