<?php
namespace Int\NewsSlideit\Persistence;

/*                                                                        *
 * This script belongs to the TYPO3 Extension "news_slideit".             *
 *                                                                        *
 * It is free software; you can redistribute it and/or modify it under    *
 * the terms of the GNU General Public License, either version 3 of the   *
 * License, or (at your option) any later version.                        *
 *                                                                        *
 * The TYPO3 project - inspiring people to share!                         *
 *                                                                        */

use TYPO3\CMS\Extbase\Persistence\Generic\QueryResult;

/**
 * Enhances the default Extbase QueryResult. When the initialize()
 * method is called all NewsOther records will be replaced with their
 * referenced news record.
 */
class OverlayQueryResult extends QueryResult {

	/**
	 * @inject
	 * @var \Int\NewsSlideit\Domain\Repository\SliderNewsRepository
	 */
	protected $sliderNewsRepository;

	/**
	 * Disables the given news and appends a error message to the news
	 * title.
	 *
	 * @param \Int\NewsSlideit\Domain\Model\SliderNews $news
	 * @return void
	 */
	protected function disableInvalidNews($news) {
		$title = $news->getTitle();
		$appendToTitle = '[Automatisch verborgen, referenzierte News nicht verfügbar]';
		if (strpos($title, $appendToTitle) === FALSE) {
			$title .= ' ' . $appendToTitle;
			$news->setTitle($title);
		}
		$news->setHidden(1);
		$this->sliderNewsRepository->updateAndPersist($news);
	}

	/**
	 * Walks through the current query result and replaced all news
	 * of type "NewsOther" with the actual news record.
	 *
	 * @return array
	 */
	protected function getOverlayedNews() {

		$overlayedResult = array();

		/** @var \Int\NewsSlideit\Domain\Model\SliderNews $news */
		foreach ($this->queryResult as $news) {

			if ($news->getType() === 'tx_news_slideit_type_other') {
				$displayNews = $news->getDisplayNews();
				if (isset($displayNews)) {
					$displayNews = clone($displayNews);
					$displayNews->setOriginalUid($news->getUid());
					$overlayedResult[] = $displayNews;
				} else {
					$this->disableInvalidNews($news);
				}
			} else {
				$overlayedResult[] = $news;
			}
		}

		return $overlayedResult;
	}

	/**
	 * Enhances the default initialize function and overlays the found
	 * "NewsOther" records with the actual news
	 */
	protected function initialize() {

		if (!is_array($this->queryResult)) {
			parent::initialize();
			$this->queryResult = $this->getOverlayedNews();
		}
	}
}