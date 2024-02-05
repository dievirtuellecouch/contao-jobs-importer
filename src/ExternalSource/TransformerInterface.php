<?php

namespace DVC\JobsImporterToPlentaBasic\ExternalSource;

use Contao\Model;

interface TransformerInterface
{
    public function transform(DataTransferInterface $dataTransfer, Model &$model): void;
}
