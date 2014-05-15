<?php
namespace Int\NewsSlideit\Install;

/*                                                                        *
 * This script belongs to the TYPO3 Extension "news_slideit".             *
 *                                                                        *
 * It is free software; you can redistribute it and/or modify it under    *
 * the terms of the GNU General Public License, either version 3 of the   *
 * License, or (at your option) any later version.                        *
 *                                                                        *
 * The TYPO3 project - inspiring people to share!                         *
 *                                                                        */

use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Install\Updates\AbstractUpdate;

/**
 * Class FileIdentifierHashUpdate adds IdentifierHashes
 */
class FlexFormRssTitleUpdate extends AbstractUpdate {

	/**
	 * @var \TYPO3\CMS\Core\Database\DatabaseConnection
	 */
	protected $db;

	/**
	 * @var string
	 */
	protected $flexFormWhere = "pi_flexform LIKE '%<field index=\"settings.list.rss.channel\">%'";

	/**
	 * @var \TYPO3\CMS\Core\Configuration\FlexForm\FlexFormTools
	 */
	protected $flexObj;

	/**
	 * @var array
	 */
	protected $sqlQueries = array();

	/**
	 * @var string
	 */
	protected $title = 'Updates the news_slideit RSS channel title setting in the FlexForm.';

	/**
	 * Creates this object
	 */
	public function __construct() {
		$this->db = $GLOBALS['TYPO3_DB'];
	}

	/**
	 * Checks if an update is needed.
	 *
	 * @param string &$description The description for the update
	 * @return boolean TRUE if an update is needed, FALSE otherwise
	 */
	public function checkForUpdate(&$description) {
		$description = 'Rename the settings.list.rss.channel setting to settings.list.rss.channel.title';
		$newsPluginWithOldRssSettingCount = $this->db->exec_SELECTcountRows(
			'uid',
			'tt_content',
			$this->flexFormWhere
		);

		return $newsPluginWithOldRssSettingCount > 0;
	}

	/**
	 * Performs the database update.
	 *
	 * @param array &$dbQueries Queries done in this update
	 * @param mixed &$customMessages Custom messages
	 * @return boolean TRUE on success, FALSE on error
	 */
	public function performUpdate(array &$dbQueries, &$customMessages) {
		$this->flexObj = GeneralUtility::makeInstance('TYPO3\\CMS\\Core\\Configuration\\FlexForm\\FlexFormTools');

		$outdatedContentElements = $this->db->exec_SELECTgetRows(
			'*',
			'tt_content',
			$this->flexFormWhere
		);
		foreach ($outdatedContentElements as $outdatedContent) {
			$this->updateOutdatedContentFlexForm($outdatedContent, $dbQueries);
		}
		return TRUE;
	}

	/**
	 * Updates the FlexForm data in the given outdated content element.
	 *
	 * @param array $outdatedContent
	 * @param array &$dbQueries Queries done in this update
	 */
	protected function updateOutdatedContentFlexForm($outdatedContent, array &$dbQueries) {

		$flexFormArray = GeneralUtility::xml2array($outdatedContent['pi_flexform']);

		if (isset($flexFormArray['data']['rss']['lDEF']['settings.list.rss.channel'])) {
			$title = $flexFormArray['data']['rss']['lDEF']['settings.list.rss.channel'];
			unset($flexFormArray['data']['rss']['lDEF']['settings.list.rss.channel']);
			$flexFormArray['data']['rss']['lDEF']['settings.list.rss.channel.title'] = $title;
		}

		$flexFormData = $this->flexObj->flexArray2Xml($flexFormArray);
		$query = $this->db->UPDATEquery('tt_content', 'uid=' . (int)$outdatedContent['uid'], array('pi_flexform' => $flexFormData));
		$this->db->sql_query($query);
		$dbQueries[] = $query;
	}
}
