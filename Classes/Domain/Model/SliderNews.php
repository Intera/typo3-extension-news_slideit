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

use Int\NewsRichteaser\Domain\Model\NewsRichteaser;

/**
 * This news type contains an image and a teaser that should be used
 * when the news is displayed in the slider
 */
class SliderNews extends NewsRichteaser {

	/**
	 * News that should be displays instead of this news.
	 *
	 * @var \Int\NewsSlideit\Domain\Model\SliderNews
	 */
	protected $displayNews;

	/**
	 * If this news is overlayed this field contains the UID of the
	 * original news record.
	 *
	 * @var int
	 */
	protected $originalUid = NULL;

	/**
	 * The slider image that was set in the news properties
	 *
	 * @var \TYPO3\CMS\Extbase\Domain\Model\FileReference
	 */
	protected $sliderImage;

	/**
	 * The image for the slider that was found in the referenced
	 * content elements.
	 *
	 * @var \TYPO3\CMS\Core\Resource\FileReference
	 */
	protected $sliderImageFromContent;

	/**
	 * The image for the slider that was set in the news properties
	 *
	 * @var \TYPO3\CMS\Core\Resource\FileReference
	 */
	protected $sliderImageFromField;

	/**
	 * The slider teaser that was set in the news properties
	 *
	 * @var string
	 */
	protected $sliderTeaser;

	/**
	 * Return the news that should be displays instead of this
	 * news.
	 *
	 * @return \Int\NewsSlideit\Domain\Model\SliderNews
	 */
	public function getDisplayNews() {
		return $this->displayNews;
	}

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

		foreach ($teaserContentUids as $contentUid) {

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
	 * Checks if the news author has set a slider image and returns
	 * it if it was set.
	 *
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
	 * Returns the slider teaser.
	 *
	 * @return string
	 */
	public function getSliderTeaser() {
		return $this->sliderTeaser;
	}

	/**
	 * Returns a unique ID of the news.
	 *
	 * When overlayed news are used it can happen that a UID appears multiple times.
	 * This is why this method prepends the original UID if the news was overlayed.
	 */
	public function getUniqueId() {
		$uniqueId = $this->getUid();
		if (isset($this->originalUid)) {
			$uniqueId = $this->originalUid . '-' . $uniqueId;
		}
		return $uniqueId;
	}

	/**
	 * This setter can be used by the overlay mechanism to set the originally requested news ID
	 * that was overlayed by this one.
	 *
	 * @param int $originalUid
	 */
	public function setOriginalUid($originalUid) {
		$this->originalUid = $originalUid;
	}
}