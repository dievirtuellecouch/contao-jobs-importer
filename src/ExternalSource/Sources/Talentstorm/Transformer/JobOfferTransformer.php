<?php

namespace DVC\JobsImporterToPlentaBasic\ExternalSource\Sources\Talentstorm\Transformer;

use Contao\Model;
use DataMap\Getter\GetBoolean;
use DataMap\Getter\GetDate;
use DataMap\Getter\GetFiltered;
use DataMap\Getter\GetInteger;
use DataMap\Getter\GetString;
use DataMap\Getter\GetTranslated;
use DataMap\Input\Input;
use DataMap\Mapper;
use DVC\JobsImporterToPlentaBasic\DataMap\Getter\GetDateTimestamp;
use DVC\JobsImporterToPlentaBasic\DataMap\Getter\GetDefault;
use DVC\JobsImporterToPlentaBasic\ExternalSource\DataTransferInterface;
use DVC\JobsImporterToPlentaBasic\ExternalSource\DefaultValues\JobOfferDefaultValues;
use DVC\JobsImporterToPlentaBasic\ExternalSource\Sources\Talentstorm\TalentstormSource;
use DVC\JobsImporterToPlentaBasic\ExternalSource\TransformerInterface;
use DVC\JobsImporterToPlentaBasic\Repository\JobLocationRepository;
use DVC\JobsImporterToPlentaBasic\Repository\JobTypeRepository;
use DVC\JobsImporterToPlentaBasic\Utility\TextCleaner;
use Symfony\Component\PropertyAccess\PropertyAccess;
use Symfony\Component\PropertyAccess\PropertyAccessor;

class JobOfferTransformer implements TransformerInterface
{
    private PropertyAccessor $propertyAccessor;

    public function __construct(
        private JobLocationRepository $jobLocationRepository,
        private JobOfferDefaultValues $jobOfferDefaultValues,
        private JobTypeRepository $jobTypeRepository,
    ) {
        $this->propertyAccessor = PropertyAccess::createPropertyAccessor();
    }

    public function getMapper(): Mapper
    {
        return new Mapper([
            'tstamp' => new GetDateTimestamp('lastModificationDate'),
            'title' => 'label',
            'alias' => 'slug',
            'description' => fn(Input $input) => $this->getDescription($input),
            'jobLocation' => fn(Input $input) => $this->getJobLocation($input),
            'published' => new GetBoolean('isPublished', false),
            'datePosted' => new GetDateTimestamp('creationDate'),
            'employmentType' => fn(Input $input) => \json_encode($this->getEmploymentType($input)),
            'externalSource' => new GetDefault(TalentstormSource::NAME),
            'externalId' => 'id',
            'externalApplicationUrl' => 'applicationFormUrl',
        ]);
    }

    public function getDefaultMapper(): Mapper
    {
        return new Mapper([
            'validThrough' => GetFiltered::from('validThrough')->ifNull(''),
            'directApply' => new GetInteger('directApply'),
            'translations' => 'translations',
            'robots' => 'robots',
            'author' => 'author',
            'importDate' => new GetDateTimestamp('importDate'),
        ]);
    }

    public function transform(DataTransferInterface $dataTransfer, Model &$model): void
    {
        $modelValues = \array_merge(
            $this->getDefaultMapper()->map($this->jobOfferDefaultValues->getAll()),
            $this->getMapper()->map($dataTransfer),
        );

        foreach ($modelValues as $key => $value) {
            $this->propertyAccessor->setValue($model, $key, $value);
        }
    }

    private function getDescription(Input $input): string
    {
        $parts = [
            [
                'type' => 'headline',
                'content' => $input->get('descIntroductionTitle'),
            ],
            [
                'type' => 'text',
                'content' => $input->get('descIntroduction'),
            ],
            [
                'type' => 'headline',
                'content' => $input->get('descJobProfileTitle'),
            ],
            [
                'type' => 'text',
                'content' => $input->get('descJobProfile'),
            ],
            [
                'type' => 'headline',
                'content' => $input->get('descApplicantProfileTitle'),
            ],
            [
                'type' => 'text',
                'content' => $input->get('descApplicantProfile'),
            ],
            [
                'type' => 'headline',
                'content' => $input->get('descOfferTitle'),
            ],
            [
                'type' => 'text',
                'content' => $input->get('descOffer'),
            ],
        ];

        $parts = \array_filter($parts, function($part) {
            return !empty($part['content']);
        });

        $parts = \array_map(function($part) {
            if ($part['type'] == 'headline') {
                return \sprintf('<h2>%s</h2>', $part['content']);
            }

            return TextCleaner::cleanHtml($part['content']);
        }, $parts);

        return \join('', $parts);
    }

    private function getJobLocation(Input $input): array
    {
        $jobLocations = $input->get('jobofferLocations');

        $result = \array_map(function($location) {
            $model = $this->jobLocationRepository->findOneByExternalId(
                externalId: $location->id,
                externalSource: TalentstormSource::NAME
            );

            return $model !== null ? (string)$model->id : null;
        }, $jobLocations);

        return \array_filter($result, fn($item) => $item !== null);
    }

    private function getEmploymentType(Input $input): array
    {
        // Try to get and set custom job type as employment type.
        // Note: TalentStorm differentiates between job type and
        // employment type. We can not map this in Plenta Jobs and
        // therefore use employment type for both attributes.
        $jobTypeName = $input->get('jobtype.label');
        $jobType = $this->jobTypeRepository->findOneByTitle(
            title: $jobTypeName,
        );

        if (!empty($jobType)) {
            return [\sprintf('CUSTOM_%s', $jobType->id)];
        }

        // Use default job type if no custom one has been set
        $translator = new GetTranslated('employment.name', [
            'Vollzeit' => 'FULL_TIME',
        ], 'OTHER');

        return [$translator->__invoke($input)];
    }
}
