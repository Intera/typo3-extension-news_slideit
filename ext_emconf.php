<?php
$EM_CONF[$_EXTKEY] = [
    'title' => 'News slider',
    'description' => 'Renders news for a slider and adjusts some templates.',
    'category' => 'fe',
    'state' => 'stable',
    'uploadfolder' => 0,
    'createDirs' => '',
    'clearCacheOnLoad' => 0,
    'author' => 'Alexander Stehlik',
    'author_email' => 'astehlik@intera.de',
    'author_company' => 'Intera GmbH',
    'version' => '1.0.0',
    '_md5_values_when_last_written' => '',
    'constraints' => [
        'depends' => [
            'typo3' => '6.2.3-6.2.99',
            'news_richteaser' => '',
        ],
        'conflicts' => [],
        'suggests' => [],
    ],
];
