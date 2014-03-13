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

/**
 * Controller of news records
 */
class NewsController extends \Tx_News_Controller_NewsController {

	const DEFAULT_LIMIT = 3;

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
	 * If no backPid was configured we set the backPid to the current
	 * page so that the back link will always be displayed.
	 */
	public function initializeDetailAction() {
		if (!intval($this->settings['backPid'])) {
			$this->settings['backPid'] = $GLOBALS['TSFE']->id;
		}
	}

	/**
	 * Overrides the news detail action so that we get a slider
	 * news domain model instead of a normal one.
	 *
	 * This is required because we remove the configuration for all
	 * other news classes and otherwise we would get a default news
	 * model which does not have the enhanced teaser handling.
	 *
	 * TODO: We can not use the slider news domain model here since it will make problems with the slider image property. More investigaion needed.
	 *
	 * @param \Int\NewsSlideit\Domain\Model\SliderNews $news
	 * @param integer $currentPage
	 * @ignorevalidation $news
	 */
	public function detailAction(\Int\NewsSlideit\Domain\Model\SliderNews $news = NULL, $currentPage = 1) {
		parent::detailAction($news, $currentPage);
	}

	/**
	 * Renders a simple news list
	 */
	public function simpleListAction() {

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

		$inSideColumn = !in_array($contentObject->data['colPos'], \TYPO3\CMS\Core\Utility\GeneralUtility::trimExplode(',', $mainContentColumns, TRUE));

		return $inSideColumn;
	}
}