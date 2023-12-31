<?php
/**
 * Landofcoder
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Landofcoder.com license that is
 * available through the world-wide-web at this URL:
 * https://landofcoder.com/terms
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade this extension to newer
 * version in the future.
 *
 * @category   Landofcoder
 * @package    Lofmp_SellerBadge
 * @copyright  Copyright (c) 2021 Landofcoder (https://www.landofcoder.com/)
 * @license    https://landofcoder.com/terms
 */

namespace Lofmp\SellerBadge\Model\Indexer;

use Magento\Framework\DataObject\IdentityInterface;
use Magento\Framework\Indexer\ActionInterface as IndexerActionInterface;
use Magento\Framework\Mview\ActionInterface as MviewActionInterface;

class SellerBadgeManagerIndexer implements IndexerActionInterface, MviewActionInterface, IdentityInterface
{

    /**
     * Indexer id
     */
    const INDEXER_ID = 'lofmp_sellerbadge_manager';

    /**
     * @var \Lofmp\SellerBadge\Model\Indexer\IndexBuilder
     */
    protected $indexBuilder;

    /**
     * @var \Magento\Framework\Indexer\IndexerRegistry
     */
    protected $indexerRegistry;

    /**
     * SellerBadgeIndexer constructor.
     * @param \Lofmp\SellerBadge\Model\Indexer\IndexBuilder $indexBuilder
     * @param \Magento\Framework\Indexer\IndexerRegistry $indexerRegistry
     */
    public function __construct(
        \Lofmp\SellerBadge\Model\Indexer\IndexBuilder $indexBuilder,
        \Magento\Framework\Indexer\IndexerRegistry $indexerRegistry
    ) {
        $this->indexBuilder = $indexBuilder;
        $this->indexerRegistry = $indexerRegistry;
    }

    /**
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function executeFull()
    {
        $this->indexBuilder->reindexFull();
    }

    /**
     * @param array $ids
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function executeList(array $ids)
    {
        $this->indexBuilder->reindexByBadgeIds($ids);
    }

    /**
     * @param int $id
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function executeRow($id)
    {
        $this->indexBuilder->reindexByBadgeId($id);
    }

    /**
     * @param int[] $ids
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function execute($ids)
    {
        $this->indexBuilder->reindexByBadgeId($ids);
    }

    /**
     * Get affected cache tags
     *
     * @return array
     * @codeCoverageIgnore
     */
    public function getIdentities(): array
    {
        return [
            \Magento\Framework\App\Cache\Type\Block::CACHE_TAG
        ];
    }

    /**
     * Get indexer
     *
     * @return \Magento\Framework\Indexer\IndexerInterface
     */
    public function getIndexer()
    {
        return $this->indexerRegistry->get(static::INDEXER_ID);
    }

    /**
     * Check if indexer is on scheduled
     *
     * @return bool
     */
    public function isIndexerScheduled()
    {
        return $this->getIndexer()->isScheduled();
    }
}
