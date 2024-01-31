<?php

namespace DVC\JobsImporter\ExternalSource;

use Contao\Model;

interface TransformerInterface
{
    public function transform(DataTransferInterface $dataTransfer, Model &$model): void;
}
