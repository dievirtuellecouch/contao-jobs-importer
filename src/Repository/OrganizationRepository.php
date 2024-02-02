<?php

namespace DVC\JobsImporter\Repository;

use Plenta\ContaoJobsBasic\Contao\Model\PlentaJobsBasicOrganizationModel as OrganizationModel;

class OrganizationRepository
{
    public function __construct(
        private array $mappings = [],
    ) {
    }

    public function getIdByLabel(?string $label): ?int
    {   
        if (empty($label)) {
            return null;
        }

        $element = OrganizationModel::findOneBy(['name = ?'], [$label]);

        return $element->id ?? null;
    }
}
