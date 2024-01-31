<?php

namespace DVC\JobsImporter\ExternalSource\Sources\Talentstorm\Transformer;

use Contao\Model;
use DataMap\Getter\GetBoolean;
use DataMap\Getter\GetDate;
use DataMap\Getter\GetFiltered;
use DataMap\Getter\GetInteger;
use DataMap\Getter\GetString;
use DataMap\Getter\GetTranslated;
use DataMap\Input\Input;
use DataMap\Mapper;
use DVC\JobsImporter\DataMap\Getter\GetDateTimestamp;
use DVC\JobsImporter\DataMap\Getter\GetDefault;
use DVC\JobsImporter\ExternalSource\DataTransferInterface;
use DVC\JobsImporter\ExternalSource\DefaultValues\JobOfferDefaultValues;
use DVC\JobsImporter\ExternalSource\Sources\Talentstorm\TalentstormSource;
use DVC\JobsImporter\ExternalSource\TransformerInterface;
use DVC\JobsImporter\Repository\JobLocationRepository;
use Symfony\Component\PropertyAccess\PropertyAccess;
use Symfony\Component\PropertyAccess\PropertyAccessor;

class JobOfferTransformer implements TransformerInterface
{
    private PropertyAccessor $propertyAccessor;

    public function __construct(
        private JobLocationRepository $jobLocationRepository,
        private JobOfferDefaultValues $jobOfferDefaultValues,
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
            'teaser' => 'teaser',
            'pageTitle' => new GetString('pageTitle', ''),
            'robots' => 'robots',
            'author' => 'author',
            'pageDescription' => 'pageDescription',
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
                return \sprintf('<h3>%s</h3>', $part['content']);
            }

            return $part['content'];
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
        $translator = new GetTranslated('employment.name', [
            'Vollzeit' => 'FULL_TIME',
        ], 'OTHER');

        return [$translator->__invoke($input)];
    }
}
