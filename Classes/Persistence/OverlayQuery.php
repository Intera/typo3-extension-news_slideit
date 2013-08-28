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

/**
 * Extends the default extbase query. The only difference is that it
 * will return an OverlayQueryResult instead of a normal QueryResult
 * when calling the execute() method.
 */
class OverlayQuery extends \TYPO3\CMS\Extbase\Persistence\Generic\Query {

	/**
	 * Executes the parent execute() method and returns an
	 * OverlayQueryResult instead of a normal QueryResult
	 *
	 * @return \Int\NewsSlideit\Persistence\OverlayQueryResult
	 */
	public function execute() {

		$result = parent::execute();

		if ($result instanceof \TYPO3\CMS\Extbase\Persistence\QueryResultInterface) {
			$result = $this->objectManager->get('Int\\NewsSlideit\\Persistence\\OverlayQueryResult', $this);
		}

		return $result;
	}
}

?>