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
 * This news type contains an image and a teaser that should be used
 * when the news is displayed in the slider
 */
class SliderNews extends NewsOther {

	/**
	 * @var \TYPO3\CMS\Core\Resource\FileReference
	 */
	protected $sliderImageFromContent;

	/**
	 * @var \TYPO3\CMS\Core\Resource\FileReference
	 */
	protected $sliderImageFromField;

	/**
	 * @var string
	 */
	protected $sliderTeaser;

	/**
	 * @var \TYPO3\CMS\Extbase\Domain\Model\FileReference
	 */
	protected $sliderImage;

	/**
	 * If the user has set a slider image it will be returned.
	 *
	 * Otherwise a slider image from the related content element will be
	 * returned, if it exists.
	 *
	 * If none of them exists, NULL will be returned.
	 *
	 * @return \TYPO3\CMS\Core\Resource\FileReference
	 */
	public function getSliderImage() {

		$sliderImage = $this->getSliderImageFromField();

		if ($sliderImage === FALSE) {
			$sliderImage = $this->getSliderImageFromContent();
		}

		return $sliderImage;
	}

	/**
	 * Tries to read the image that should be used for the slider
	 * from the related teaser content element.
	 *
	 * Returns FALSE if no image was found
	 *
	 * @return \TYPO3\CMS\Core\Resource\FileReference|boolean
	 */
	public function getSliderImageFromContent() {

		if (isset($this->sliderImageFromContent)) {
			return $this->sliderImageFromContent;
		}

		$this->sliderImageFromContent = FALSE;

		/** @var \TYPO3\CMS\Core\Resource\FileRepository $fileRepository */
		$fileRepository = \TYPO3\CMS\Core\Utility\GeneralUtility::makeInstance('TYPO3\\CMS\\Core\\Resource\\FileRepository');
		$teaserContentUids = $this->getTeaserContentElementIdList();
		$teaserContentUids = \TYPO3\CMS\Core\Utility\GeneralUtility::trimExplode(',', $teaserContentUids, TRUE);

		foreach ($teaserContentUids as $contentUid)  {

			$imageFiles = $fileRepository->findByRelation('tt_content', 'image', $contentUid);

			if (!count($imageFiles)) {
				continue;
			}

			$this->sliderImageFromContent = $imageFiles[0];
			break;
		}

		return $this->sliderImageFromContent;
	}

	/**
	 * @return \TYPO3\CMS\Core\Resource\FileReference
	 */
	public function getSliderImageFromField() {

		if (isset($this->sliderImageFromField)) {
			return $this->sliderImageFromField;
		}

		$this->sliderImageFromField = FALSE;

		if (isset($this->sliderImage)) {
			$this->sliderImageFromField = $this->sliderImage->getOriginalResource();
		}

		return $this->sliderImageFromField;
	}

	/**
	 * @return string
	 */
	public function getSliderTeaser() {
		return $this->sliderTeaser;
	}
}