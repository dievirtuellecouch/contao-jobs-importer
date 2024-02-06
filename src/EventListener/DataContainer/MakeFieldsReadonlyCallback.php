<?php

namespace DVC\JobsImporterToPlentaBasic\EventListener\DataContainer;

use Contao\CoreBundle\DependencyInjection\Attribute\AsCallback;
use Contao\DataContainer;
use Plenta\ContaoJobsBasic\Contao\Model\PlentaJobsBasicJobLocationModel as JobLocationModel;
use Plenta\ContaoJobsBasic\Contao\Model\PlentaJobsBasicOfferModel as JobOfferModel;
use Symfony\Component\HttpFoundation\RequestStack;

class MakeFieldsReadonlyCallback
{
    public function __construct(
        private RequestStack $requestStack
    ) {
    }

    #[AsCallback(
        table: 'tl_plenta_jobs_basic_offer',
        target: 'config.onload',
        priority: 100
    )]
    public function onOfferLoad(DataContainer $dc = null): void
    {
        if (!$dc->id || $this->requestStack->getCurrentRequest()->query->get('act') !== 'edit') {
            return;
        }

        $element = JobOfferModel::findById($dc->id);

        if ($element === null) {
            return;
        }

        if (empty($element->externalSource)) {
            return;
        }

        $readonlyFields = [
            'title', 'alias', 'description', 'robots', 'employmentType', 'jobLocation',
        ];
        
        foreach ($readonlyFields as $fieldName) {
            $GLOBALS['TL_DCA']['tl_plenta_jobs_basic_offer']['fields'][$fieldName]['eval']['readonly'] = true;
        }
    }

    #[AsCallback(
        table: 'tl_plenta_jobs_basic_job_location',
        target: 'config.onload',
        priority: 100
    )]
    public function onLocationLoad(DataContainer $dc = null): void
    {
        if (!$dc->id || $this->requestStack->getCurrentRequest()->query->get('act') !== 'edit') {
            return;
        }

        $element = JobLocationModel::findById($dc->id);

        if ($element === null) {
            return;
        }

        if (empty($element->externalSource)) {
            return;
        }

        $readonlyFields = [
            'jobTypeLocation', 'streetAddress', 'postalCode', 'addressLocality', 'addressRegion', 'addressCountry',
        ];
        
        foreach ($readonlyFields as $fieldName) {
            $GLOBALS['TL_DCA']['tl_plenta_jobs_basic_job_location']['fields'][$fieldName]['eval']['readonly'] = true;
        }
    }
}
