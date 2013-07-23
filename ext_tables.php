<?php

if (!defined ('TYPO3_MODE')) {
	die ('Access denied.');
}

\TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addStaticFile($_EXTKEY, 'Configuration/TypoScript', 'News Slider');

$sliderColumns = array(

	'tx_news_slideit_slider_teaser' => array(
		'exclude' => 1,
		'l10n_mode' => 'noCopy',
		'label' => $ll . 'Slider Teaser',
		'config' => array(
			'type' => 'text',
			'cols' => 30,
			'rows' => 5,
		)
	),

	'tx_news_slideit_slider_image' => array(
		'label' => 'Slider Bild',
		'config' => \TYPO3\CMS\Core\Utility\ExtensionManagementUtility::getFileFieldTCAConfig('tx_news_slideit_slider_image', array(
			'appearance' => array(
				'createNewRelationLinkTitle' => 'LLL:EXT:cms/locallang_ttc.xlf:images.addFileReference'
			),
			'maxitems' => 1,
			'foreign_types' => array(
				'0' => array(
					'showitem' => '
								--palette--;LLL:EXT:lang/locallang_tca.xlf:sys_file_reference.imageoverlayPalette;txMicroportalsImageOverlayPalette,
								--palette--;;filePalette'
				),
				\TYPO3\CMS\Core\Resource\File::FILETYPE_IMAGE => array(
					'showitem' => '
								--palette--;LLL:EXT:lang/locallang_tca.xlf:sys_file_reference.imageoverlayPalette;txMicroportalsImageOverlayPalette,
								--palette--;;filePalette'
				),
			),
		), $GLOBALS['TYPO3_CONF_VARS']['GFX']['imagefile_ext'])
	)
);

\TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addTCAcolumns('tx_news_domain_model_news', $sliderColumns);
\TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addToAllTCAtypes('tx_news_domain_model_news', '--div--;Slider,tx_news_slideit_slider_teaser,tx_news_slideit_slider_image');

\TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addLLrefForTCAdescr(
	'tx_news_domain_model_news', 'EXT:' . $_EXTKEY . '/Resources/Private/Language/locallang_csh_news.xlf');