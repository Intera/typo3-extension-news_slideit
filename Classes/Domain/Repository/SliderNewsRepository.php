<?php
declare(strict_types=1);

namespace Int\NewsSlideit\Domain\Repository;

/*                                                                        *
 * This script belongs to the TYPO3 Extension "news_slideit".             *
 *                                                                        *
 * It is free software; you can redistribute it and/or modify it under    *
 * the terms of the GNU General Public License, either version 3 of the   *
 * License, or (at your option) any later version.                        *
 *                                                                        *
 * The TYPO3 project - inspiring people to share!                         *
 *                                                                        */

use GeorgRinger\News\Domain\Repository\NewsRepository;
use Int\NewsSlideit\Domain\Model\SliderNews;
use Int\NewsSlideit\Persistence\OverlayQuery;

/**
 * Respository for slider news
 */
class SliderNewsRepository extends NewsRepository
{
    /**
     * Calls the parent createQuery() method and replaces the created
     * query with an OverlayQuery.
     *
     * @return OverlayQuery
     */
    public function createQuery()
    {
        $query = parent::createQuery();
        /** @noinspection PhpMethodParametersCountMismatchInspection */
        $overlayQuery = $this->objectManager->get(OverlayQuery::class, $this->objectType);
        $overlayQuery->setQuerySettings($query->getQuerySettings());
        return $overlayQuery;
    }

    /**
     * Updates the given slider news in the repository and persists the changes directly.
     *
     * @param SliderNews $sliderNews
     */
    public function updateAndPersist($sliderNews)
    {
        $this->update($sliderNews);
        $this->persistenceManager->persistAll();
    }
}
