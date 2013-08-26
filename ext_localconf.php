<?php

if (!defined ('TYPO3_MODE')) {
	die ('Access denied.');
}

$GLOBALS['TYPO3_CONF_VARS']['EXT']['news']['switchableControllerActions']['newItems']['News->slider'] = 'LLL:EXT:news_slideit/Resources/Private/Language/locallang.xlf:news_slider_action';
$GLOBALS['TYPO3_CONF_VARS']['EXT']['news']['switchableControllerActions']['newItems']['News->simpleList'] = 'LLL:EXT:news_slideit/Resources/Private/Language/locallang.xlf:news_simple_list_action';

/** @var \TYPO3\CMS\Extbase\Object\Container\Container $extbaseObjectContainer */
$extbaseObjectContainer = \TYPO3\CMS\Core\Utility\GeneralUtility::makeInstance('TYPO3\\CMS\\Extbase\\Object\\Container\\Container');
$extbaseObjectContainer->registerImplementation('Tx_News_Controller_NewsController', 'Int\\NewsSlideit\\Controller\\NewsController');
