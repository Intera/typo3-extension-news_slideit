<?php
namespace Int\NewsSlideit\Domain\Model;

class SliderNews extends \Tx_News_Domain_Model_News {

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
	protected $txNewsSlideitSliderTeaser;

	/**
	 * @var \TYPO3\CMS\Extbase\Domain\Model\FileReference
	 */
	protected $txNewsSlideitSliderImage;

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
		$teaserContentUids = $this->getContentElementIdListForTeaser();
		$teaserContentUids = explode(',', $teaserContentUids);

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

		if (isset($this->txNewsSlideitSliderImage)) {
			$this->sliderImageFromField = $this->txNewsSlideitSliderImage->getOriginalResource();
		}

		return $this->sliderImageFromField;
	}

	/**
	 * @return string
	 */
	public function getSliderTeaser() {
		return $this->txNewsSlideitSliderTeaser;
	}
}