<?php

namespace DVC\JobsImporterToPlentaBasic\EventSubscriber;

use Contao\Model;
use DateTime;
use DVC\JobsImporterToPlentaBasic\Event\PreModelPersistentEvent;
use Plenta\ContaoJobsBasic\Contao\Model\PlentaJobsBasicOfferModel as JobOfferModel;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

class AdjustJobOfferDatePostedEventSubscriber implements EventSubscriberInterface
{
    public function __construct(
        private ?int $overrideThreshold,
    ) {
    }

    public static function getSubscribedEvents(): array
    {
        return [
            PreModelPersistentEvent::class => 'onModelPersistentPre',
        ]; 
    }

    public function onModelPersistentPre(PreModelPersistentEvent $event): void
    {
        if ($this->overrideThreshold === null) {
            return;
        }

        $model = $event->getModel();
        $oldModel = $event->getOldModel();

        if (!is_a($model, JobOfferModel::class)) {
            return;
        }

        if ($oldModel === null || !$oldModel->datePosted) {
            $datePosted = new DateTime();
        }
        else {
            $datePosted = DateTime::createFromFormat('U', $oldModel->datePosted);
        }
        
        $currentDate = new DateTime();
        $diffInDays = $datePosted->diff($currentDate)->days;

        if ($diffInDays >= $this->overrideThreshold) {
            $newDatePosted = $currentDate;
        }
        else {
            $newDatePosted = $datePosted;
        }
        
        $model->datePosted = $newDatePosted->format('U');
    }
}
