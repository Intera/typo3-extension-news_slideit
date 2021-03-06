<?php
declare(strict_types=1);

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

use GeorgRinger\News\Domain\Model\Dto\NewsDemand;
use GeorgRinger\News\Domain\Model\News;
use GeorgRinger\News\Domain\Repository\NewsRepository;
use GeorgRinger\News\Utility\Cache;
use Int\NewsSlideit\Domain\Repository\SliderNewsRepository;
use TYPO3\CMS\Core\Page\PageRenderer;
use TYPO3\CMS\Core\TypoScript\TypoScriptService;
use TYPO3\CMS\Core\Utility\ArrayUtility;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Extbase\Mvc\Controller\Argument;
use TYPO3\CMS\Extbase\Mvc\Exception\NoSuchArgumentException;
use TYPO3\CMS\Extbase\Property\Exception;
use TYPO3\CMS\Extbase\Property\Exception\InvalidSourceException;
use TYPO3\CMS\Extbase\Property\Exception\TargetNotFoundException;
use TYPO3\CMS\Frontend\Controller\TypoScriptFrontendController;

/**
 * Controller of news records
 */
class NewsController extends \GeorgRinger\News\Controller\NewsController
{
    const DEFAULT_LIMIT = 3;

    /**
     * If no backPid was configured we set the backPid to the current
     * page so that the back link will always be displayed.
     */
    public function initializeDetailAction()
    {
        if (empty($this->settings['backPid'])) {
            $this->settings['backPid'] = $GLOBALS['TSFE']->id;
        }
    }

    /**
     * We do not set the newsRepository property here to make sure the injector for the
     * slider news repository is working.
     *
     * @param NewsRepository $newsRepository
     */
    public function injectNewsRepository(NewsRepository $newsRepository)
    {
        // Intentionally left blank!
    }

    /**
     * @param SliderNewsRepository $newsRepository
     */
    public function injectNewsRepositorySlider(SliderNewsRepository $newsRepository)
    {
        $this->newsRepository = $newsRepository;
    }

    /**
     * Overrides the news detail action so that we get a slider
     * news domain model instead of a normal one.
     *
     * This is required because we remove the configuration for all
     * other news classes and otherwise we would get a default news
     * model which does not have the enhanced teaser handling.
     *
     * @param News $news
     * @param integer $currentPage
     * @ignorevalidation $news
     */
    public function detailAction(News $news = null, $currentPage = 1)
    {
        if ($news !== null) {
            $news = $this->newsRepository->findByUid($news->getUid());
        }
        parent::detailAction($news, $currentPage);
    }

    /**
     * Output a list view of news
     *
     * @param array $overwriteDemand
     * @return void
     */
    public function listAction(array $overwriteDemand = null)
    {
        // Override demand settings and initialize required view variables for RSS feeds.
        if (!$this->isHtmlFormat()) {
            $this->view->assign('language', $this->getPageRenderer()->getLanguage());
            $this->view->assign('currentUrl', GeneralUtility::getIndpEnv('TYPO3_REQUEST_URL'));

            $this->settings['disableOverrideDemand'] = 0;
            $overwriteDemand = [
                'limit' => $this->settings['defaultLimit']['rss'],
                'offset' => 0,
                'order' => 'datetime desc',
            ];
        }

        $this->addRssLink();
        parent::listAction($overwriteDemand);
    }

    /**
     * Renders a simple news list
     */
    public function simpleListAction()
    {
        if (!$this->isHtmlFormat()) {
            $this->forward('list');
        }

        $this->addRssLink();

        $demand = $this->createDemandObjectFromSettings($this->settings);
        $this->initializeDefaultLimitForCurrentActionIfNotSet($demand);

        $sliderNewsRecords = $this->newsRepository->findDemanded($demand);

        $this->view->assignMultiple(
            [
                'news' => $sliderNewsRecords,
                'demand' => $demand,
            ]
        );

        Cache::addPageCacheTagsByDemandObject($demand);
    }

    /**
     * Renders the slider
     */
    public function sliderAction()
    {
        if (!$this->isHtmlFormat()) {
            $this->forward('list');
        }

        $this->addRssLink();

        $demand = $this->createDemandObjectFromSettings($this->settings);
        $this->initializeDefaultLimitForCurrentActionIfNotSet($demand);

        $sliderNewsRecords = $this->newsRepository->findDemanded($demand);
        $inSideColumn = $this->inSideColumn();

        $columnSettings = $this->settings;
        if ($inSideColumn) {
            ArrayUtility::mergeRecursiveWithOverrule(
                $columnSettings,
                $this->settings['sideColumn']
            );
        }

        $this->view->assignMultiple(
            [
                'news' => $sliderNewsRecords,
                'demand' => $demand,
                'inSideColumn' => $inSideColumn,
                'columnSettings' => $columnSettings,
            ]
        );

        Cache::addPageCacheTagsByDemandObject($demand);
    }

    /**
     * We disable the PID check of the news for the detail view so that it is possible
     * to display news from other areas.
     *
     * @param News $news
     * @return News
     */
    protected function checkPidOfNewsRecord(News $news)
    {
        return $news;
    }

    /**
     * @return TypoScriptFrontendController
     */
    protected function getTypoScriptFrontendController()
    {
        return $GLOBALS['TSFE'];
    }

    /**
     * Checks if the current content element was placed in a side column
     *
     * @return bool TRUE if content element is in side column
     */
    protected function inSideColumn()
    {
        $contentObject = $this->configurationManager->getContentObject();

        $typoScriptService = $this->objectManager->get(TypoScriptService::class);
        $settingsAsTypoScriptArray = $typoScriptService->convertPlainArrayToTypoScriptArray($this->settings);
        $mainContentColumns = $contentObject->stdWrap(
            $settingsAsTypoScriptArray['mainContentColumns'],
            $settingsAsTypoScriptArray['mainContentColumns.']
        );

        if (empty($mainContentColumns)) {
            return false;
        }

        $inSideColumn = !in_array(
            $contentObject->data['colPos'],
            GeneralUtility::trimExplode(',', $mainContentColumns, true)
        );

        return $inSideColumn;
    }

    /**
     * If no limit is set in the settings we fallback to the defaultLimit
     * setting and if this is empty for the current action we use the
     * default limit defined in the DEFAULT_LIMIT constant.
     *
     * @param NewsDemand $demand
     */
    protected function initializeDefaultLimitForCurrentActionIfNotSet($demand)
    {
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
     * Returns TRUE if the current request format is "html" or not set.
     *
     * @return bool
     */
    protected function isHtmlFormat()
    {
        return !(isset($this->settings['format']) && $this->settings['format'] !== 'html');
    }

    /**
     * Sets the value for the given argument.
     *
     * @param Argument $argument
     * @param string $argumentName
     * @throws \Exception
     * @throws NoSuchArgumentException
     * @throws Exception
     */
    protected function setArgumentValue($argument, $argumentName)
    {
        try {
            $argument->setValue($this->request->getArgument($argumentName));
        } catch (Exception  $e) {
            if ($argumentName !== 'news') {
                throw $e;
            }

            if (!$e->getPrevious() instanceof TargetNotFoundException
                && !$e->getPrevious() instanceof InvalidSourceException) {
                throw $e;
            }
        }
    }

    private function addRssLink()
    {
        if (!empty($this->settings['list']['rss']['disable'])) {
            return;
        }

        $contentObject = $this->configurationManager->getContentObject();
        $contentObjectData = $contentObject->data;
        $contentObjectUid = $contentObjectData['uid'];
        $contentObjectPid = $contentObjectData['pid'];
        $rssTitle = $this->getRssTitle();
        $rssLink = $this->configurationManager->getContentObject()->typoLink_URL(
            [
                'parameter' => $contentObjectPid . ',2457',
                'additionalParams' => '&content=' . $contentObjectUid,
            ]
        );

        $this->getPageRenderer()->addHeaderData(
            '<link rel="alternate" type="application/rss+xml" title="' . htmlspecialchars($rssTitle)
            . '" href="' . htmlspecialchars($rssLink) . '" />'
        );
    }

    private function getPageRenderer(): PageRenderer
    {
        $pageRenderer = GeneralUtility::makeInstance(PageRenderer::class);
        return $pageRenderer;
    }

    /**
     * Returns the title for the RSS feed of the current action consisting
     * of the page title and the content element header.
     *
     * @return string
     */
    private function getRssTitle()
    {
        if (!empty($this->settings['list']['rss']['channel']['title'])) {
            return $this->settings['list']['rss']['channel']['title'];
        }

        $rssTitle = $this->configurationManager->getContentObject()->data['header'];
        $tsfe = $this->getTypoScriptFrontendController();
        $tsfe->generatePageTitle();
        return $rssTitle . ' - ' . $this->getPageRenderer()->getTitle();
    }
}
