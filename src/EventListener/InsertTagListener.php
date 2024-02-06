<?php

namespace DVC\JobsImporterToPlentaBasic\EventListener;

use Contao\CoreBundle\DependencyInjection\Attribute\AsHook;
use Contao\Input;
use Plenta\ContaoJobsBasic\Contao\Model\PlentaJobsBasicOfferModel as JobOfferModel;

#[AsHook('replaceInsertTags')]
class InsertTagListener
{
    public const TAG = 'job';

    public function __invoke(string $tag)
    {
        $chunks = explode('::', $tag);

        if ($chunks[0] !== self::TAG) {
            return false;
        }

        $alias = Input::get('items');
        $offer = JobOfferModel::findPublishedByIdOrAlias($alias);

        if ($offer === null)
        {
            return false;
        }

        switch ($chunks[1]) {
            case 'externalApplicationUrl':
                return $this->getExternalApplciationUrl($offer);
        }

        return false;
    }

    private function getExternalApplciationUrl(JobOfferModel $offer): string
    {
        return $offer->externalApplicationUrl ?? '';
    }
}
