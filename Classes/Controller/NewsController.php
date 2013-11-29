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
	 * @param \Int\NewsRichteaser\Domain\Model\NewsRichteaser $news
	 * @param integer $currentPage
	 */
	public function detailAction(\Int\NewsRichteaser\Domain\Model\NewsRichteaser $news = NULL, $currentPage = 1) {
		parent::detailAction($news, $currentPage);
	}

	/**
	 * Renders a simple news list
	 */
	public function simpleListAction() {

		$demand = $this->createDemandObjectFromSettings($this->settings);
		$demand->setLimit(3);

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
		$demand->setLimit(5);

		$sliderNewsRecords = $this->newsRepository->findDemanded($demand);
		$inSideColumn = $this->inSideColumn();

		$this->view->assignMultiple(array(
			'news' => $sliderNewsRecords,
			'demand' => $demand,
			'inSideColumn' => $inSideColumn,
			'columnSettings' => $inSideColumn ? \TYPO3\CMS\Core\Utility\GeneralUtility::array_merge_recursive_overrule($this->settings, $this->settings['sideColumn']) : $this->settings
		));
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