<?php

namespace DVC\JobsImporter\ExternalSource;

interface ExternalSourceInterface
{
    public function getReader(): ReaderInterface;

    public function getTransformer(string $name): ?TransformerInterface;

    public function getName(): string;
}
