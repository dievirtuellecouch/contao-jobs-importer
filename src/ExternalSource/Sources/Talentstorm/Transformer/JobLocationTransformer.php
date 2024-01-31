<?php

namespace DVC\JobsImporter\ExternalSource\Sources\Talentstorm\Transformer;

use Contao\Model;
use DataMap\Getter\GetFiltered;
use DataMap\Mapper;
use DateTime;
use DVC\JobsImporter\DataMap\Getter\GetDateTimestamp;
use DVC\JobsImporter\DataMap\Getter\GetDefault;
use DVC\JobsImporter\ExternalSource\DataTransferInterface;
use DVC\JobsImporter\ExternalSource\DefaultValues\JobLocationDefaultValues;
use DVC\JobsImporter\ExternalSource\Sources\Talentstorm\TalentstormSource;
use DVC\JobsImporter\ExternalSource\TransformerInterface;
use Symfony\Component\PropertyAccess\PropertyAccess;
use Symfony\Component\PropertyAccess\PropertyAccessor;

class JobLocationTransformer implements TransformerInterface
{
    private PropertyAccessor $propertyAccessor;

    public function __construct(
        private JobLocationDefaultValues $jobLocationDefaultValues,
    ) {
        $this->propertyAccessor = PropertyAccess::createPropertyAccessor();
    }

    public function getMapper(): Mapper
    {
        return new Mapper([
            'streetAddress' => 'street',
            'postalCode' => 'zip',
            'addressLocality' => 'city',
            'addressRegion' => 'region',
            'addressCountry' => GetFiltered::from('country')->lower(),
            'externalSource' => new GetDefault(TalentstormSource::NAME),
            'externalId' => 'id',
        ]);
    }

    public function getDefaultMapper(): Mapper
    {
        return new Mapper([
            'tstamp' => new GetDateTimestamp('tstamp'),
            'pid' => 'pid',
            'jobTypeLocation' => 'jobTypeLocation',
            'importDate' => new GetDateTimestamp('importDate'),
        ]);
    }

    public function transform(DataTransferInterface $dataTransfer, Model &$model): void
    {
        $modelValues = \array_merge(
            $this->getDefaultMapper()->map($this->jobLocationDefaultValues->getAll()),
            $this->getMapper()->map($dataTransfer)
        );

        foreach ($modelValues as $key => $value) {
            $this->propertyAccessor->setValue($model, $key, $value);
        }
    }
}
