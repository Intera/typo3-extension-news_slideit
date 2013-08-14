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

		if ($this->actionMethodName === 'sliderAction' || $this->actionMethodName === 'simpleListAction' || $this->actionMethodName === 'listAction') {
			$view->setTemplateRootPath(\TYPO3\CMS\Core\Utility\GeneralUtility::getFileAbsFileName('EXT:news_slideit/Resources/Private/Templates'));
		}

		if ($this->actionMethodName === 'sliderAction' || $this->actionMethodName === 'listAction') {
			$view->setPartialRootPath(\TYPO3\CMS\Core\Utility\GeneralUtility::getFileAbsFileName('EXT:news_slideit/Resources/Private/Partials'));
		}
	}

	public function simpleListAction() {

		$demand = $this->createDemandObjectFromSettings($this->settings);
		$demand->setLimit(3);

		$sliderNewsRecords = $this->sliderNewsRepository->findDemanded($demand);

		$this->view->assignMultiple(array(
			'news' => $sliderNewsRecords,
			'demand' => $demand,
		));
	}

	public function sliderAction() {

		$demand = $this->createDemandObjectFromSettings($this->settings);
		$demand->setLimit(5);

		$sliderNewsRecords = $this->sliderNewsRepository->findDemanded($demand);

		$this->view->assignMultiple(array(
			'news' => $sliderNewsRecords,
			'demand' => $demand,
		));
	}
}
?>