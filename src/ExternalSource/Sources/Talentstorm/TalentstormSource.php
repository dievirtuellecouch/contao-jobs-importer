<?php

namespace DVC\JobsImporter\ExternalSource\Sources\Talentstorm;

use DVC\JobsImporter\ExternalSource\ExternalSourceInterface;
use DVC\JobsImporter\ExternalSource\Model\ModelSearchParameter;
use DVC\JobsImporter\ExternalSource\Sources\Talentstorm\Import\Reader;
use DVC\JobsImporter\ExternalSource\Sources\Talentstorm\Transformer\JobLocationTransformer;
use DVC\JobsImporter\ExternalSource\Sources\Talentstorm\Transformer\JobOfferTransformer;
use DVC\JobsImporter\ExternalSource\TransformerInterface;

class TalentstormSource implements ExternalSourceInterface
{
    public const NAME = 'talentstorm';

    private array $transformers = [];

    public function __construct(
        private JobLocationTransformer $jobLocationTransformer,
        private JobOfferTransformer $jobOfferTransformer,
        private Reader $reader,
    ) {
        $this->transformers['location'] = $jobLocationTransformer;
        $this->transformers['offer'] = $jobOfferTransformer;
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
        }

        return null;
    }

    public function getName(): string
    {
        return self::NAME;
    }
}
