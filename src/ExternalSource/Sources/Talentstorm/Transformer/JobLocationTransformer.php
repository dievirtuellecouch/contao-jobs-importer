<?php

namespace DVC\JobsImporterToPlentaBasic\ExternalSource\Sources\Talentstorm\Transformer;

use Contao\Model;
use DataMap\Getter\GetFiltered;
use DataMap\Input\Input;
use DataMap\Mapper;
use DateTime;
use DVC\JobsImporterToPlentaBasic\DataMap\Getter\GetDateTimestamp;
use DVC\JobsImporterToPlentaBasic\DataMap\Getter\GetDefault;
use DVC\JobsImporterToPlentaBasic\ExternalSource\DataTransferInterface;
use DVC\JobsImporterToPlentaBasic\ExternalSource\DefaultValues\JobLocationDefaultValues;
use DVC\JobsImporterToPlentaBasic\ExternalSource\Sources\Talentstorm\TalentstormSource;
use DVC\JobsImporterToPlentaBasic\ExternalSource\TransformerInterface;
use DVC\JobsImporterToPlentaBasic\Repository\OrganizationRepository;
use Symfony\Component\PropertyAccess\PropertyAccess;
use Symfony\Component\PropertyAccess\PropertyAccessor;

class JobLocationTransformer implements TransformerInterface
{
    private PropertyAccessor $propertyAccessor;

    public function __construct(
        private OrganizationRepository $organizationRepository,
        private JobLocationDefaultValues $jobLocationDefaultValues,
    ) {
        $this->propertyAccessor = PropertyAccess::createPropertyAccessor();
    }

    public function getMapper(): Mapper
    {
        return new Mapper([
            'pid' => fn(Input $input) => $this->getOrganization($input),
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

    private function getOrganization(Input $input): int
    {
        $label = $input->get('label');

        $id = $this->organizationRepository->getIdByLabel($label);

        return $id ?? 1;
    }
}
