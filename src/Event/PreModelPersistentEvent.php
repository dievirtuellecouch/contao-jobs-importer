<?php

namespace DVC\JobsImporterToPlentaBasic\Event;

use Contao\Model;
use Symfony\Contracts\EventDispatcher\Event;

class PreModelPersistentEvent extends Event
{
    public function __construct(
        private Model &$model,
        private Model &$oldModel,
    ) {
    }

    public function getModel(): Model
    {
        return $this->model;
    }

    public function getOldModel(): Model
    {
        return $this->oldModel;
    }
}
