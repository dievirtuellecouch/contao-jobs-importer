<?php

namespace DVC\JobsImporter\DataMap\Getter;

use DataMap\Getter\Getter;
use DataMap\Input\Input;

class GetDefault implements Getter
{
    public function __construct(
        private mixed $default = null,
    ) {
    }

    public function __invoke(Input $input): mixed
    {
        return $this->default;
    }
}
