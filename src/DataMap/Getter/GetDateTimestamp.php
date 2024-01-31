<?php

namespace DVC\JobsImporter\DataMap\Getter;

use DataMap\Common\DateUtil;
use DataMap\Getter\GetDate;
use DataMap\Getter\Getter;
use DataMap\Input\Input;
use DateTimeImmutable;
use DateTimeZone;

class GetDateTimestamp implements Getter
{
    public function __construct(
        private string $key,
        private ?DateTimeImmutable $default = null,
        private ?DateTimeZone $timeZone = null,
    ) {
    }

    public function __invoke(Input $input): ?int
    {
        $date = $input->get($this->key);
        $date = DateUtil::toDatetimeOrNull($date, $this->timeZone) ?? $this->default;

        if ($date === null) {
            return null;
        }

        return $date->getTimestamp();
    }
}
