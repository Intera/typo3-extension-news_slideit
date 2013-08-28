<?php
namespace Int\NewsSlideit\Domain\Model;

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
 * Model for a news that is a reference to another news
 */
class NewsOther extends \Int\NewsRichteaser\Domain\Model\NewsRichteaser {

	/**
	 * News that should be displays instead of this news.
	 *
	 * @var \Tx_News_Domain_Model_News
	 */
	protected $displayNews;

	/**
	 * Return the news that should be displays instead of this
	 * news.
	 *
	 * @return \Tx_News_Domain_Model_News
	 */
	public function getDisplayNews() {
		return $this->displayNews;
	}
}
?>