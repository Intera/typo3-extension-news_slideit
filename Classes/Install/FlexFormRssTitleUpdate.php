<?php
declare(strict_types=1);

namespace Int\NewsSlideit\Install;

/*                                                                        *
 * This script belongs to the TYPO3 Extension "news_slideit".             *
 *                                                                        *
 * It is free software; you can redistribute it and/or modify it under    *
 * the terms of the GNU General Public License, either version 3 of the   *
 * License, or (at your option) any later version.                        *
 *                                                                        *
 * The TYPO3 project - inspiring people to share!                         *
 *                                                                        */

use Doctrine\DBAL\FetchMode;
use PDO;
use Symfony\Component\Console\Output\OutputInterface;
use TYPO3\CMS\Core\Configuration\FlexForm\FlexFormTools;
use TYPO3\CMS\Core\Database\ConnectionPool;
use TYPO3\CMS\Core\Database\Query\QueryBuilder;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Install\Updates\ChattyInterface;
use TYPO3\CMS\Install\Updates\UpgradeWizardInterface;

/**
 * Class FileIdentifierHashUpdate adds IdentifierHashes
 */
class FlexFormRssTitleUpdate implements UpgradeWizardInterface, ChattyInterface
{
    /**
     * @var FlexFormTools
     */
    protected $flexObj;

    /**
     * @var OutputInterface
     */
    private $output;

    /**
     * Execute the update
     *
     * Called when a wizard reports that an update is necessary
     *
     * @return bool
     */
    public function executeUpdate(): bool
    {
        $this->flexObj = GeneralUtility::makeInstance(FlexFormTools::class);

        $builder = $this->getQueryBuilderForTable('tt_content');
        $builder->from('tt_content');
        $this->applyOutdatedFlexFormConstraint($builder);
        $result = $builder->execute();

        $outdatedContentElements = $result->fetchAll(FetchMode::ASSOCIATIVE);
        foreach ($outdatedContentElements as $outdatedContent) {
            $this->updateOutdatedContentFlexForm($outdatedContent);
        }

        return true;
    }

    /**
     * Return the description for this wizard
     *
     * @return string
     */
    public function getDescription(): string
    {
        return 'Rename the settings.list.rss.channel setting to settings.list.rss.channel.title';
    }

    /**
     * Return the identifier for this wizard
     * This should be the same string as used in the ext_localconf class registration
     *
     * @return string
     */
    public function getIdentifier(): string
    {
        return 'tx_news_slideit_flex_form_rss_title';
    }

    /**
     * Returns an array of class names of Prerequisite classes
     *
     * This way a wizard can define dependencies like "database up-to-date" or
     * "reference index updated"
     *
     * @return string[]
     */
    public function getPrerequisites(): array
    {
        return [];
    }

    /**
     * Return the speaking name of this wizard
     *
     * @return string
     */
    public function getTitle(): string
    {
        return 'Update news_slideit RSS channel title setting in the FlexForm';
    }

    /**
     * Setter injection for output into upgrade wizards
     *
     * @param OutputInterface $output
     */
    public function setOutput(OutputInterface $output): void
    {
        $this->output = $output;
    }

    /**
     * Is an update necessary?
     *
     * Is used to determine whether a wizard needs to be run.
     * Check if data for migration exists.
     *
     * @return bool
     */
    public function updateNecessary(): bool
    {
        $builder = $this->getQueryBuilderForTable('tt_content');
        $builder->from('tt_content');
        $this->applyOutdatedFlexFormConstraint($builder);
        $builder->count('uid');
        $newsPluginWithOldRssSettingCount = $builder->execute()->fetchColumn(0);
        return $newsPluginWithOldRssSettingCount > 0;
    }

    /**
     * @param QueryBuilder $builder
     */
    private function applyOutdatedFlexFormConstraint(QueryBuilder $builder): void
    {
        $builder->where(
            $builder->expr()->like('pi_flexform', '%<field index="settings.list.rss.channel">%')
        );
    }

    private function getConnectionPool(): ConnectionPool
    {
        return GeneralUtility::makeInstance(ConnectionPool::class);
    }

    private function getQueryBuilderForTable(string $table): QueryBuilder
    {
        return $this->getConnectionPool()->getQueryBuilderForTable($table);
    }

    /**
     * Updates the FlexForm data in the given outdated content element.
     *
     * @param array $outdatedContent
     */
    private function updateOutdatedContentFlexForm(array $outdatedContent)
    {
        $flexFormArray = GeneralUtility::xml2array($outdatedContent['pi_flexform']);

        if (isset($flexFormArray['data']['rss']['lDEF']['settings.list.rss.channel'])) {
            $title = $flexFormArray['data']['rss']['lDEF']['settings.list.rss.channel'];
            unset($flexFormArray['data']['rss']['lDEF']['settings.list.rss.channel']);
            $flexFormArray['data']['rss']['lDEF']['settings.list.rss.channel.title'] = $title;
        }

        $flexFormData = $this->flexObj->flexArray2Xml($flexFormArray);
        $builder = $this->getQueryBuilderForTable('tt_content');
        $builder->update('tt_content');
        $builder->set('pi_flexform', $flexFormData);
        $builder->where(
            $builder->expr()->eq(
                'uid',
                $builder->createNamedParameter((int)$outdatedContent['uid'], PDO::PARAM_INT)
            )
        );
        $builder->execute();
    }
}
