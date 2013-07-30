<?php
namespace Int\NewsSlideit\ViewHelpers;
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
 * ViewHelper to overlay the given news with possible referenced news
 *
 * # Example: Basic Example
 * # Description: Overlay the given newsItem with possible referenced news
 * <code>
 *	<n:overlayReferencedNews news="{newsItem}" name="overlayedNewsItem">{overlayedNewsItem.title}</n:overlayReferencedNews>
 * </code>
 *
 */
class OverlayReferencedNewsViewHelper extends \TYPO3\CMS\Fluid\Core\ViewHelper\AbstractViewHelper {

	/**
	 * Overrlay the given news
	 *
	 * @param \Tx_News_Domain_Model_News $news
	 * @param string $name
	 * @return string
	 */
	public function render($news, $name) {

		if ($news->getType() === 'tx_news_slideit_type_other') {
			$displayNews = $news->getTxNewsSlideitDisplayNews();
			if (isset($displayNews)) {
				$news = $displayNews;
			} else {
				$news = NULL;
			}
		}

		$this->templateVariableContainer->add($name, $news);

		$output = $this->renderChildren();

		$this->templateVariableContainer->remove($name);

		return $output;
	}
}

?>