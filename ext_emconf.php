<?php
$EM_CONF[$_EXTKEY] = array(
	'title' => 'News slider',
	'description' => 'Renders news for the news slider',
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
	'constraints' => array(
		'depends' => array(
			'typo3' => '6.2.3-6.2.99',
			'news_richteaser' => '',
		),
		'conflicts' => array(),
		'suggests' => array(),
	),
);
