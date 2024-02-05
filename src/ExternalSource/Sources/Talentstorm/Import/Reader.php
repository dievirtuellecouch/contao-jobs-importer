<?php

namespace DVC\JobsImporterToPlentaBasic\ExternalSource\Sources\Talentstorm\Import;

use Doctrine\Common\Annotations\AnnotationReader;
use DVC\JobsImporterToPlentaBasic\ExternalSource\ReaderInterface;
use DVC\JobsImporterToPlentaBasic\ExternalSource\Sources\Talentstorm\DataTransfer\JobOfferDataTransfer;
use DVC\JobsImporterToPlentaBasic\ExternalSource\Sources\Talentstorm\DataTransfer\OrganizationDataTransfer;
use DVC\JobsImporterToPlentaBasic\ExternalSource\Sources\Talentstorm\Import\Importer;
use DVC\JobsImporterToPlentaBasic\ExternalSource\SupportedModel;
use Symfony\Component\PropertyInfo\Extractor\ReflectionExtractor;
use Symfony\Component\PropertyInfo\PropertyInfoExtractor;
use Symfony\Component\Serializer\Encoder\JsonEncoder;
use Symfony\Component\Serializer\Mapping\Factory\ClassMetadataFactory;
use Symfony\Component\Serializer\Mapping\Loader\AnnotationLoader;
use Symfony\Component\Serializer\Normalizer\ArrayDenormalizer;
use Symfony\Component\Serializer\Normalizer\DateTimeNormalizer;
use Symfony\Component\Serializer\Normalizer\ObjectNormalizer;
use Symfony\Component\Serializer\Normalizer\PropertyNormalizer;
use Symfony\Component\Serializer\Serializer;

class Reader implements ReaderInterface
{
    private $serializer;
    private $jobs = [];
    private $locations = [];

    public function __construct(
        private Importer $importer,
    ) {
        $classMetadataFactory = new ClassMetadataFactory(new AnnotationLoader(new AnnotationReader()));

        $encoders = [new JsonEncoder()];
        $normalizers = [
            new DateTimeNormalizer(),
            new ArrayDenormalizer(),
            new ObjectNormalizer($classMetadataFactory, propertyTypeExtractor: new ReflectionExtractor()),
            new ObjectNormalizer($classMetadataFactory, propertyTypeExtractor: new PropertyInfoExtractor()),
            new PropertyNormalizer(),
        ];

        $this->serializer = new Serializer($normalizers, $encoders);
    }

    public function getAllAvailableJobs(): ?array
    {
        if (!empty($this->jobs)) {
            return $this->jobs;
        }

        $data = $this->importer->importJobsList();

        if (empty($data)) {
            return null;
        }

        $items = \array_key_exists('hydra:member', $data) ? $data['hydra:member'] : [];

        $this->jobs = \array_map(function($item) {
            return $this->serializer->denormalize(
                $item,
                JobOfferDataTransfer::class,
                'json'
            );
        }, $items);

        return $this->jobs;
    }

    public function getAllLocations(): ?array
    {
        if (!empty($this->locations)) {
            return $this->locations;
        }

        $availableJobs = $this->getAllAvailableJobs();

        if (empty($availableJobs)) {
            return null;
        }

        $locationsPerJob = \array_map(function($job) {
            return $job->jobofferLocations;
        }, $availableJobs);

        $this->locations = \array_unique(\array_merge(...$locationsPerJob), SORT_REGULAR);

        return $this->locations;
    }

    public function getAllOrganizations(): ?array
    {
        if (!empty($this->organizations)) {
            return $this->organizations;
        }

        $availableLocations = $this->getAllLocations();

        if (empty($availableLocations)) {
            return null;
        }

        return \array_map(function($location) {
            $organizationDataTransfer = new OrganizationDataTransfer();
            $organizationDataTransfer->label = $location->label;
            return $organizationDataTransfer;
        }, $availableLocations);
    }

    public function getItemsForIdentifier(SupportedModel $identifier): ?array
    {
        switch ($identifier) {
            case SupportedModel::Location:
                return $this->getAllLocations();
                break;

            case SupportedModel::Offer:
                return $this->getAllAvailableJobs();
                break;

            case SupportedModel::Organization:
                return $this->getAllOrganizations();
                break;
        }

        return null;
    }
}
