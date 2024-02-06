<?php

$GLOBALS['TL_DCA']['tl_plenta_jobs_basic_offer']['fields']['importDate'] = [
    'sql' => [
        'type' => 'integer',
        'unsigned' => true,
        'default' => 0,
    ],
];

$GLOBALS['TL_DCA']['tl_plenta_jobs_basic_offer']['fields']['externalSource'] = [
    'sql' => [
        'type' => 'string',
        'length' => 32,
        'default' => '',
    ],
];

$GLOBALS['TL_DCA']['tl_plenta_jobs_basic_offer']['fields']['externalId'] = [
    'sql' => [
        'type' => 'string',
        'length' => 32,
        'default' => '',
    ],
];

$GLOBALS['TL_DCA']['tl_plenta_jobs_basic_offer']['fields']['externalApplicationUrl'] = [
    'sql' => [
        'type' => 'string',
        'length' => 255,
        'default' => '',
    ],
];
