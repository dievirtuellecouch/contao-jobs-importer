<?php

namespace DVC\JobsImporter\Import;

use DVC\JobsImporter\ExternalSource\ExternalSourceRegistry;
use DVC\JobsImporter\ExternalSource\Sources\Talentstorm\TalentstormSource;
use DVC\JobsImporter\ExternalSource\SupportedModel;
use Plenta\ContaoJobsBasic\Contao\Model\PlentaJobsBasicJobLocationModel as JobLocationModel;
use Plenta\ContaoJobsBasic\Contao\Model\PlentaJobsBasicOfferModel as JobOfferModel;

class Importer
{
    public function __construct(
        private ExternalSourceRegistry $externalSourceRegistry,
    ) {
    }

    public function importAll(): void
    {
        $modelClasses = [
            SupportedModel::Location->value => JobLocationModel::class,
            SupportedModel::Offer->value => JobOfferModel::class,
        ];

        foreach ($this->externalSourceRegistry->getAll() as $source) {

            foreach ($modelClasses as $modelKey => $modelClassName) {
                $items = $source->getReader()->getItemsForIdentifier(SupportedModel::from($modelKey));
                $findOneBy = new \ReflectionMethod($modelClassName, 'findOneBy');

                if (empty($items)) {
                    continue;
                }

                foreach ($items as $item) {
                    $model = $findOneBy->invoke(
                        null,
                        ['externalId = ?', 'externalSource = ?'],
                        [$item->id, $source->getName()],
                    );

                    if (empty($model)) {
                        $model = new $modelClassName;
                    }

                    $source->getTransformer($modelKey)->transform($item, $model);

                    $model->save();
                }
            }
        }
    }
}
