<?php
namespace Int\NewsSlideit\Domain\Model;
/*                                                                        *
 * This script belongs to the TYPO3 extension "news_slideit".             *
 *                                                                        *
 * It is free software; you can redistribute it and/or modify it under    *
 * the terms of the GNU General Public License as published by the Free   *
 * Software Foundation, either version 3 of the License, or (at your      *
 * option) any later version.                                             *
 *                                                                        *
 * This script is distributed in the hope that it will be useful, but     *
 * WITHOUT ANY WARRANTY; without even the implied warranty of MERCHAN-    *
 * TABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU General      *
 * Public License for more details.                                       *
 *                                                                        *
 * You should have received a copy of the GNU General Public License      *
 * along with the script.                                                 *
 * If not, see http://www.gnu.org/licenses/gpl.html                       *
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
	protected $txNewsSlideitDisplayNews;

	/**
	 * Return the news that should be displays instead of this
	 * news.
	 *
	 * @return \Tx_News_Domain_Model_News
	 */
	public function getTxNewsSlideitDisplayNews() {
		return $this->txNewsSlideitDisplayNews;
	}
}
?>