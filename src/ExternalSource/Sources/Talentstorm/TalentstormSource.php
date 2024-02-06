<?php

namespace DVC\JobsImporterToPlentaBasic\ExternalSource\Sources\Talentstorm;

use DVC\JobsImporterToPlentaBasic\ExternalSource\ExternalSourceInterface;
use DVC\JobsImporterToPlentaBasic\ExternalSource\Model\ModelSearchParameter;
use DVC\JobsImporterToPlentaBasic\ExternalSource\Sources\Talentstorm\Import\Reader;
use DVC\JobsImporterToPlentaBasic\ExternalSource\Sources\Talentstorm\Transformer\JobLocationTransformer;
use DVC\JobsImporterToPlentaBasic\ExternalSource\Sources\Talentstorm\Transformer\JobOfferTransformer;
use DVC\JobsImporterToPlentaBasic\ExternalSource\Sources\Talentstorm\Transformer\OrganizationTransformer;
use DVC\JobsImporterToPlentaBasic\ExternalSource\SupportedModel;
use DVC\JobsImporterToPlentaBasic\ExternalSource\TransformerInterface;

class TalentstormSource implements ExternalSourceInterface
{
    public const NAME = 'talentstorm';

    private array $transformers = [];

    public function __construct(
        private JobLocationTransformer $jobLocationTransformer,
        private JobOfferTransformer $jobOfferTransformer,
        private OrganizationTransformer $organizationTransformer,
        private Reader $reader,
    ) {
        $this->transformers['location'] = $jobLocationTransformer;
        $this->transformers['offer'] = $jobOfferTransformer;
        $this->transformers['organization'] = $organizationTransformer;
    }

    public function getReader(): Reader
    {
        return $this->reader;
    }

    public function getTransformer(string $name): ?TransformerInterface
    {
        if (!\array_key_exists($name, $this->transformers)) {
            return null;
        }

        return $this->transformers[$name];
    }

    public function getSearchParamterForIdentifier(SupportedModel $identifier): ?ModelSearchParameter
    {
        switch ($identifier) {
            case SupportedModel::Location:
                return new ModelSearchParameter(
                    columns: ['externalId = ?', \sprintf('externalSource = "%s"', $this->getName())],
                    values: ['id']
                );
            case SupportedModel::Offer:
                return new ModelSearchParameter(
                    columns: ['externalId = ?', \sprintf('externalSource = "%s"', $this->getName())],
                    values: ['id']
                );
            case SupportedModel::Organization:
                return new ModelSearchParameter(
                    columns: ['name = ?'],
                    values: ['label']
                );
        }

        return null;
    }

    public function getName(): string
    {
        return self::NAME;
    }
}
