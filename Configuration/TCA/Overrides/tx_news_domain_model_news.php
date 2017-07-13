<?php
if (!defined ('TYPO3_MODE')) {
	die ('Access denied.');
}

$sliderColumns = array(

	'tx_news_slideit_slider_teaser' => array(
		'l10n_mode' => 'noCopy',
		'label' => 'Spezieller Text für den Slider Teaser',
		'config' => array(
			'type' => 'text',
			'cols' => 30,
			'rows' => 5,
		)
	),

	'tx_news_slideit_slider_image' => array(
		'label' => 'Spezielles Bild für den Slider Teaser',
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
			), $GLOBALS['TYPO3_CONF_VARS']['GFX']['imagefile_ext']),
        'l10n_mode' => 'exclude',
	),

	'tx_news_slideit_display_news' => array(
		'label' => 'Diese News anzeigen',
		'config' => array(
			'type' => 'group',
			'internal_type' => 'db',
			'allowed' => 'tx_news_domain_model_news',
			'maxitems' => 1,
			'wizards' => array(
				'suggest' => array(
					'type' => 'suggest',
				),
			),
		),
	),
);

\TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addTcaSelectItem('tx_news_domain_model_news', 'type', array('Externe News', 'tx_news_slideit_type_other'));
\TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addToAllTCAtypes('tx_news_domain_model_news', '--div--;Spezieller Slider (optional),tx_news_slideit_slider_teaser,tx_news_slideit_slider_image', '', 'after:content_elements');
\TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addTCAcolumns('tx_news_domain_model_news', $sliderColumns);

$GLOBALS['TCA']['tx_news_domain_model_news']['types']['tx_news_slideit_type_other'] = array(
	'showitem' => 'l10n_parent, l10n_diffsource,
			title;;paletteCoreWithoutTopNews,;;;;2-2-2, tx_news_slideit_display_news;;paletteNavtitle,;;;;3-3-3,

		--div--;LLL:EXT:cms/locallang_ttc.xml:tabs.access,
			--palette--;LLL:EXT:cms/locallang_ttc.xml:palette.access;paletteAccess,

		--div--;LLL:EXT:cms/locallang_tca.xml:pages.tabs.extended,'
);

$GLOBALS['TCA']['tx_news_domain_model_news']['palettes']['paletteCoreWithoutTopNews'] = array(
	'showitem' => 'type, sys_language_uid, hidden,',
	'canNotCollapse' => FALSE
);

unset($sliderColumns);