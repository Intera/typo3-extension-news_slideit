<?php
/** @noinspection PhpMissingStrictTypesDeclarationInspection */
/** @noinspection PhpFullyQualifiedNameUsageInspection */

defined('TYPO3_MODE') or die();

$GLOBALS['TYPO3_CONF_VARS']['EXT']['news']['switchableControllerActions']['newItems']['News->slider'] =
    'LLL:EXT:news_slideit/Resources/Private/Language/locallang.xlf:news_slider_action';
$GLOBALS['TYPO3_CONF_VARS']['EXT']['news']['switchableControllerActions']['newItems']['News->simpleList'] =
    'LLL:EXT:news_slideit/Resources/Private/Language/locallang.xlf:news_simple_list_action';

$extbaseObjectContainer = \TYPO3\CMS\Core\Utility\GeneralUtility::makeInstance(
    \TYPO3\CMS\Extbase\Object\Container\Container::class
);
$extbaseObjectContainer->registerImplementation(
    \GeorgRinger\News\Controller\NewsController::class,
    \Int\NewsSlideit\Controller\NewsController::class
);

$GLOBALS['TYPO3_CONF_VARS']['SC_OPTIONS']['ext/install']['update']['ext_news_slideit_flexformrsstitle'] =
    \Int\NewsSlideit\Install\FlexFormRssTitleUpdate::class;

unset($extbaseObjectContainer);
