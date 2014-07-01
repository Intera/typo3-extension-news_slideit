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
 * View helper that temporary disables RealURL.
 */
class DisableRealUrlViewHelper extends AbstractViewHelper {

	/**
	 * Disables RealURL using the tx_realurl_disable register, renders
	 * the children and resets the register stack afterwards.
	 *
	 * @return string
	 */
	public function render() {

		$tsfe = $this->getTypoScriptFrontendController();
		if (isset($tsfe)) {
			array_push($tsfe->registerStack, $tsfe->register);
			$tsfe->register['tx_realurl_disable'] = TRUE;
		}

		$result = $this->renderChildren();

		if (isset($tsfe)) {
			$tsfe->register = array_pop($tsfe->registerStack);
		}

		return $result;
	}

	/**
	 * @return \TYPO3\CMS\Frontend\Controller\TypoScriptFrontendController
	 */
	protected function getTypoScriptFrontendController() {
		return $GLOBALS['TSFE'];
	}
}