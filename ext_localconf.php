<?php

if (!defined ('TYPO3_MODE')) {
	die ('Access denied.');
}

$GLOBALS['TYPO3_CONF_VARS']['EXT']['news']['switchableControllerActions']['newItems']['News->slider'] = 'LLL:EXT:news_slideit/Resources/Private/Language/locallang.xlf:news_slider_action';
$GLOBALS['TYPO3_CONF_VARS']['EXT']['news']['switchableControllerActions']['newItems']['News->simpleList'] = 'LLL:EXT:news_slideit/Resources/Private/Language/locallang.xlf:news_simple_list_action';

/** @var \TYPO3\CMS\Extbase\Object\Container\Container $extbaseObjectContainer */
$extbaseObjectContainer = \TYPO3\CMS\Core\Utility\GeneralUtility::makeInstance('TYPO3\\CMS\\Extbase\\Object\\Container\\Container');
$extbaseObjectContainer->registerImplementation('Tx_News_Controller_NewsController', 'Int\\NewsSlideit\\Controller\\NewsController');

$GLOBALS['TYPO3_CONF_VARS']['SC_OPTIONS']['ext/install']['update']['ext_news_slideit_flexformrsstitle'] = 'Int\\NewsSlideit\\Install\\FlexFormRssTitleUpdate';

// Register our tables in the cacheopt Extension.
if (\TYPO3\CMS\Core\Utility\ExtensionManagementUtility::isLoaded('cacheopt')) {
	\Tx\Cacheopt\CacheOptimizerRegistry::getInstance()->registerPluginForTables(
		array(
			'tx_news_domain_model_news',
			'tx_news_domain_model_tag',
			'sys_category',
		),
		'news_pi1'
	);
}