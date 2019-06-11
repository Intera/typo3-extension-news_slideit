<?php
declare(strict_types=1);

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

use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3Fluid\Fluid\Core\ViewHelper\AbstractViewHelper;

/**
 * This view helper parses the given path through GeneralUtility::locationHeaderUrl().
 */
class LocationHeaderUrlViewHelper extends AbstractViewHelper
{
    public function initializeArguments()
    {
        $this->registerArgument('path', 'string', 'The URL path', true);
    }

    /**
     * Parses the given path through GeneralUtility::locationHeaderUrl().
     *
     * @return string
     */
    public function render()
    {
        return GeneralUtility::locationHeaderUrl($this->arguments['path']);
    }
}
