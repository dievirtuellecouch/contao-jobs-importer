<?php

$GLOBALS['TL_DCA']['tl_plenta_jobs_basic_job_location']['fields']['importDate'] = [
    'sql' => [
        'type' => 'integer',
        'unsigned' => true,
        'default' => 0,
    ],
];

$GLOBALS['TL_DCA']['tl_plenta_jobs_basic_job_location']['fields']['externalSource'] = [
    'sql' => [
        'type' => 'string',
        'length' => 32,
        'default' => '',
    ],
];

$GLOBALS['TL_DCA']['tl_plenta_jobs_basic_job_location']['fields']['externalId'] = [
    'sql' => [
        'type' => 'string',
        'length' => 32,
        'default' => '',
    ],
];
