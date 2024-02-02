<?php

namespace DVC\JobsImporter\ExternalSource\Model;

use Contao\Model;
use DVC\JobsImporter\ExternalSource\SupportedModel;

class ModelSearchParameter
{
    public function __construct(
        private string|array $columns,
        private mixed $values,
    ) {
    }

    public function getColumns(): string|array
    {
        return $this->columns;
    }

    public function getValuesForItem(object $item): mixed
    {
        if (is_iterable($this->values)) {
            return \array_map(fn($key) => $item->{$key}, $this->values);
        }

        return $item->{$this->values};
    }
}
