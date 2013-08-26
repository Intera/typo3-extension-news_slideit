<?php
namespace Int\NewsSlideit\Controller;

/**
 * Controller of news records
 */
class NewsController extends \Tx_News_Controller_NewsController {

	/**
	 * @var \Int\NewsSlideit\Domain\Repository\SliderNewsRepository
	 * @inject
	 */
	protected $sliderNewsRepository;

	public function injectSliderNewsRepository(\Int\NewsSlideit\Domain\Repository\SliderNewsRepository $sliderNewsRepository) {
		$this->sliderNewsRepository = $sliderNewsRepository;
	}

	/**
	 * @param \TYPO3\CMS\Extbase\Mvc\View\ViewInterface $view
	 * @return void
	 */
	protected function setViewConfiguration(\TYPO3\CMS\Extbase\Mvc\View\ViewInterface $view) {

		/** @var \TYPO3\CMS\Fluid\View\TemplateView $view */

		parent::setViewConfiguration($view);

		if ($this->actionMethodName === 'sliderAction' || $this->actionMethodName === 'simpleListAction' || $this->actionMethodName === 'listAction' || $this->actionMethodName === 'detailAction') {
			$view->setTemplateRootPath(\TYPO3\CMS\Core\Utility\GeneralUtility::getFileAbsFileName('EXT:news_slideit/Resources/Private/Templates'));
		}

		if ($this->actionMethodName === 'sliderAction' || $this->actionMethodName === 'listAction') {
			$view->setPartialRootPath(\TYPO3\CMS\Core\Utility\GeneralUtility::getFileAbsFileName('EXT:news_slideit/Resources/Private/Partials'));
		}
	}

	/**
	 * Renders a simple news list
	 */
	public function simpleListAction() {

		$demand = $this->createDemandObjectFromSettings($this->settings);
		$demand->setLimit(3);

		$sliderNewsRecords = $this->sliderNewsRepository->findDemanded($demand);

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

		$sliderNewsRecords = $this->sliderNewsRepository->findDemanded($demand);
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
?>