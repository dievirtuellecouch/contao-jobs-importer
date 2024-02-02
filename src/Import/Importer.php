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
                $searchParameters = $source->getSearchParamterForIdentifier(SupportedModel::from($modelKey));
                $findOneBy = new \ReflectionMethod($modelClassName, 'findOneBy');

                if (empty($items)) {
                    continue;
                }

                $importedIdsPerModel = [];

                foreach ($items as $item) {
                    $model = $findOneBy->invoke(
                        null,
                        $searchParameters->getColumns(),
                        $searchParameters->getValuesForItem($item),
                    );

                    if (empty($model)) {
                        $model = new $modelClassName;
                    }

                    $source->getTransformer($modelKey)->transform($item, $model);

                    $model->save();

                    $importedIdsPerModel[] = $model->id;
                }

                foreach ($this->getCallbacks($modelKey) as $callback) {
                    \call_user_func($callback, $importedIdsPerModel);
                }
            }
        }
    }

    private function getCallbacks(string $modelKey): array
    {
        $callbacksPerModel = [
            SupportedModel::Offer->value => [
                [self::class, 'disableOffers']
            ],
        ];

        if (!\array_key_exists($modelKey, $callbacksPerModel)) {
            return [];
        }

        return $callbacksPerModel[$modelKey];
    }

    private function disableOffers(array $importedIds): void
    {
        if (empty($importedIds)) {
            return;
        }

        $itemsToDisable = JobOfferModel::findBy(
            [
                'externalSource != ?',
                \sprintf('id NOT IN (%s)', \join(',', \array_map(fn($id) => \intval($id), $importedIds))),
            ],
            ['']
        );

        if (empty($itemsToDisable)) {
            return;
        }
        
        foreach ($itemsToDisable as $item) {
            $item->published = 0;
            $item->save();
        }
    }
}
