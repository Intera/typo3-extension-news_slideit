<?php
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

/**
 * Enhances the default news repository and modifieds the createQuery()
 * method. It will return an OverlayQuery instead of a default Query
 * object.
 */
class OverlayNewsRepository extends \Tx_News_Domain_Repository_NewsRepository {

	/**
	 * Calls the parent createQuery() method and replaces the created
	 * query with an OverlayQuery.
	 *
	 * @return \Int\NewsSlideit\Persistence\OverlayQuery
	 */
	public function createQuery() {
		$query = parent::createQuery();
		$overlayQuery = $this->objectManager->get('Int\\NewsSlideit\\Persistence\\OverlayQuery', $this->objectType);
		$overlayQuery->setQuerySettings($query->getQuerySettings());
		return $overlayQuery;
	}
}

?>