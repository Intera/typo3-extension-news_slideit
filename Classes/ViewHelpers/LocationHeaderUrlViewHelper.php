<?php
namespace Int\NewsSlideit\ViewHelpers;

/*                                                                        *
 * This script belongs to the TYPO3 Extension "news_slideit".             *
 *                                                                        *
 * It is free software; you can redistribute it and/or modify it under    *
 * the terms of the GNU General Public License, either version 3 of the   *
 * License, or (at your option) any later version.                        *
 *                                                                        *
 * The TYPO3 project - inspiring people to share!                         *
 *                                                                        */

use TYPO3\CMS\Fluid\Core\ViewHelper\AbstractViewHelper;

/**
 * This view helper parses the given path through GeneralUtility::locationHeaderUrl().
 */
class LocationHeaderUrlViewHelper extends AbstractViewHelper {

	/**
	 * Parses the given path through GeneralUtility::locationHeaderUrl().
	 *
	 * @param string $path
	 * @return string
	 */
	public function render($path) {
		return \TYPO3\CMS\Core\Utility\GeneralUtility::locationHeaderUrl($path);
	}
}