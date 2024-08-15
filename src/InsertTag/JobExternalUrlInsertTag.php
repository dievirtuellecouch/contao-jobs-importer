<?php

namespace DVC\JobsImporterToPlentaBasic\InsertTag;

use Contao\CoreBundle\DependencyInjection\Attribute\AsInsertTag;
use Contao\CoreBundle\InsertTag\InsertTagResult;
use Contao\CoreBundle\InsertTag\ResolvedInsertTag;
use Contao\CoreBundle\InsertTag\Resolver\InsertTagResolverNestedResolvedInterface;
use Contao\Input;
use Plenta\ContaoJobsBasic\Contao\Model\PlentaJobsBasicOfferModel as JobOfferModel;

#[AsInsertTag(self::TAG)]
class JobExternalUrlInsertTag implements InsertTagResolverNestedResolvedInterface
{
    public const TAG = 'job_external_url';

    public function __invoke(ResolvedInsertTag $insertTag): InsertTagResult
    {
        $alias = Input::get('auto_item', false, true);
        $offer = JobOfferModel::findPublishedByIdOrAlias($alias);

        if ($offer === null) {
            return new InsertTagResult('');
        }

        return new InsertTagResult($this->getExternalApplicationUrl($offer));
    }

    private function getExternalApplicationUrl(JobOfferModel $offer): string
    {
        return $offer->externalApplicationUrl ?? '';
    }
}
