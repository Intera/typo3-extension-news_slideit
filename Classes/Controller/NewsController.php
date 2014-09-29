<?php
namespace Int\NewsSlideit\Controller;

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

/**
 * Controller of news records
 */
class NewsController extends \Tx_News_Controller_NewsController {

	const DEFAULT_LIMIT = 3;

	/**
	 * If no backPid was configured we set the backPid to the current
	 * page so that the back link will always be displayed.
	 */
	public function initializeDetailAction() {
		if (!intval($this->settings['backPid'])) {
			$this->settings['backPid'] = $GLOBALS['TSFE']->id;
		}
	}

	/**
	 * Initialize common view variables.
	 * Currently only the RSS title is initialized.
	 *
	 * @param \Tx_Extbase_MVC_View_ViewInterface $view
	 */
	public function initializeView(\Tx_Extbase_MVC_View_ViewInterface $view) {
		parent::initializeView($view);
		$this->initializeRssTitle($view);
	}

	/**
	 * We override the default injector so that the news controller
	 * always uses the slider news repository.
	 *
	 * @param \Int\NewsSlideit\Domain\Repository\SliderNewsRepository $newsRepository
	 */
	public function injectNewsRepository(\Int\NewsSlideit\Domain\Repository\SliderNewsRepository $newsRepository) {
		$this->newsRepository = $newsRepository;
	}

	/**
	 * We disable the PID check of the news for the detail view so that it is possible
	 * to display news from other areas.
	 *
	 * @param \Tx_News_Domain_Model_News $news
	 * @return \Tx_News_Domain_Model_News
	 */
	protected function checkPidOfNewsRecord(\Tx_News_Domain_Model_News $news) {
		return $news;
	}

	/**
	 * Overrides the news detail action so that we get a slider
	 * news domain model instead of a normal one.
	 *
	 * This is required because we remove the configuration for all
	 * other news classes and otherwise we would get a default news
	 * model which does not have the enhanced teaser handling.
	 *
	 * @param \Int\NewsSlideit\Domain\Model\SliderNews $news
	 * @param integer $currentPage
	 * @ignorevalidation $news
	 */
	public function detailAction(\Int\NewsSlideit\Domain\Model\SliderNews $news = NULL, $currentPage = 1) {
		parent::detailAction($news, $currentPage);
	}

	/**
	 * Output a list view of news
	 *
	 * @param array $overwriteDemand
	 * @return void
	 */
	public function listAction(array $overwriteDemand = NULL) {

		// Override demand settings and initialize required view variables for RSS feeds.
		if (!$this->isHtmlFormat()) {

			$this->view->assign('language', $this->getTypoScriptFrontendController()->lang);
			$this->view->assign('currentUrl', GeneralUtility::getIndpEnv('TYPO3_REQUEST_URL'));

			$this->settings['disableOverrideDemand'] = 0;
			$overwriteDemand = array(
				'limit' => $this->settings['defaultLimit']['rss'],
				'offset' => 0,
				'order' => 'datetime desc'
			);
		}

		parent::listAction($overwriteDemand);
	}

	/**
	 * Renders a simple news list
	 */
	public function simpleListAction() {

		if (!$this->isHtmlFormat()) {
			$this->forward('list');
		}

		$demand = $this->createDemandObjectFromSettings($this->settings);
		$this->initializeDefaultLimitForCurrentActionIfNotSet($demand);

		$sliderNewsRecords = $this->newsRepository->findDemanded($demand);

		$this->view->assignMultiple(array(
			'news' => $sliderNewsRecords,
			'demand' => $demand,
		));
	}

	/**
	 * Renders the slider
	 */
	public function sliderAction() {

		if (!$this->isHtmlFormat()) {
			$this->forward('list');
		}

		$demand = $this->createDemandObjectFromSettings($this->settings);
		$this->initializeDefaultLimitForCurrentActionIfNotSet($demand);

		$sliderNewsRecords = $this->newsRepository->findDemanded($demand);
		$inSideColumn = $this->inSideColumn();

		$columnSettings = $this->settings;
		if ($inSideColumn) {
			\TYPO3\CMS\Core\Utility\ArrayUtility::mergeRecursiveWithOverrule($columnSettings, $this->settings['sideColumn']);
		}

		$this->view->assignMultiple(array(
			'news' => $sliderNewsRecords,
			'demand' => $demand,
			'inSideColumn' => $inSideColumn,
			'columnSettings' => $columnSettings
		));
	}

	/**
	 * @return \TYPO3\CMS\Frontend\Controller\TypoScriptFrontendController
	 */
	protected function getTypoScriptFrontendController() {
		return $GLOBALS['TSFE'];
	}

	/**
	 * Checks if the current content element was placed in a side column
	 *
	 * @return bool TRUE if content element is in side column
	 */
	protected function inSideColumn() {

		$contentObject = $this->configurationManager->getContentObject();

		/** @var \TYPO3\CMS\Extbase\Service\TypoScriptService $typoScriptService */
		$typoScriptService = $this->objectManager->get('TYPO3\\CMS\\Extbase\\Service\\TypoScriptService');
		$settingsAsTypoScriptArray = $typoScriptService->convertPlainArrayToTypoScriptArray($this->settings);
		$mainContentColumns = $contentObject->stdWrap($settingsAsTypoScriptArray['mainContentColumns'], $settingsAsTypoScriptArray['mainContentColumns.']);

		$inSideColumn = !in_array($contentObject->data['colPos'], GeneralUtility::trimExplode(',', $mainContentColumns, TRUE));

		return $inSideColumn;
	}

	/**
	 * If no limit is set in the settings we fallback to the defaultLimit
	 * setting and if this is empty for the current action we use the
	 * default limit defined in the DEFAULT_LIMIT constant.
	 *
	 * @param \Tx_News_Domain_Model_Dto_NewsDemand $demand
	 */
	protected function initializeDefaultLimitForCurrentActionIfNotSet($demand) {

		if ($this->settings['limit'] !== '') {
			return;
		}

		$controllerActionName = $this->request->getControllerActionName();
		if (isset($this->settings['defaultLimit'][$controllerActionName])) {
			$demand->setLimit($this->settings['defaultLimit'][$controllerActionName]);
		} else {
			$demand->setLimit(static::DEFAULT_LIMIT);
		}
	}

	/**
	 * Initializes the title for the RSS feed of the current action consisting
	 * of the page title and the content element header.
	 *
	 * @param \TYPO3\CMS\Extbase\Mvc\View\ViewInterface $view
	 */
	protected function initializeRssTitle(\TYPO3\CMS\Extbase\Mvc\View\ViewInterface $view) {

		if (!empty($this->settings['list']['rss']['channel']['title'])) {
			$view->assign('rssTitle', $this->settings['list']['rss']['channel']['title']);
			return;
		}

		$rssTitle = $this->configurationManager->getContentObject()->data['header'];

		\TYPO3\CMS\Frontend\Page\PageGenerator::generatePageTitle();
		$rssTitle .= ' - ' . $this->getTypoScriptFrontendController()->getPageRenderer()->getTitle();

		$view->assign('rssTitle', $rssTitle);
	}

	/**
	 * Returns TRUE if the current request format is "html" or not set.
	 *
	 * @return bool
	 */
	protected function isHtmlFormat() {
		return !(isset($this->settings['format']) && $this->settings['format'] !== 'html');
	}
}