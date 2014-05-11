<?php
if (!defined ('TYPO3_MODE')) {
	die ('Access denied.');
}

// hide the header type when the news plugin was selected so that
// heading 1 will be used always
$GLOBALS['TCA']['tt_content']['types']['list']['subtypes_excludelist']['news_pi1'] .= ',header_layout';

// Override the default FlexForm
\TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addPiFlexFormValue('news_pi1', 'FILE:EXT:news_slideit/Configuration/FlexForms/flexform_news.xml');