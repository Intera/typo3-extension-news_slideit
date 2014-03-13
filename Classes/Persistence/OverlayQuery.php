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

use TYPO3\CMS\Extbase\Persistence\Generic\Query;
use TYPO3\CMS\Extbase\Persistence\QueryResultInterface;

/**
 * Extends the default extbase query. The only difference is that it
 * will return an OverlayQueryResult instead of a normal QueryResult
 * when calling the execute() method.
 */
class OverlayQuery extends Query {

	/**
	 * Executes the parent execute() method and returns an
	 * OverlayQueryResult instead of a normal QueryResult
	 *
	 * @param $returnRawQueryResult boolean avoids the object mapping by the persistence
	 * @return \Int\NewsSlideit\Persistence\OverlayQueryResult
	 */
	public function execute($returnRawQueryResult = FALSE) {

		$result = parent::execute($returnRawQueryResult);

		if ($result instanceof QueryResultInterface) {
			$result = $this->objectManager->get('Int\\NewsSlideit\\Persistence\\OverlayQueryResult', $this);
		}

		return $result;
	}
}