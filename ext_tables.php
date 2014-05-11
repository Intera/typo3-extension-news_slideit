<?php
if (!defined ('TYPO3_MODE')) {
	die ('Access denied.');
}

\TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addStaticFile($_EXTKEY, 'Configuration/TypoScript', 'News Slider');

\TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addLLrefForTCAdescr(
	'tx_news_domain_model_news', 'EXT:' . $_EXTKEY . '/Resources/Private/Language/locallang_csh_news.xlf');