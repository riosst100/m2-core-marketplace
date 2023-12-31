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

use Magento\Framework\Exception\LocalizedException;

class IndexBuilder
{
    /**
     * @var \Lofmp\SellerBadge\Model\ResourceModel\SellerBadgeManager
     */
    private $sellerBadgeManagerResource;

    /**
     * @var \Lofmp\SellerBadge\Model\ResourceModel\SellerBadge\CollectionFactory
     */
    private $sellerBadgeCollectionFactory;

    /**
     * @var \Lof\MarketPlace\Model\ResourceModel\Seller\CollectionFactory
     */
    private $sellerCollectionFactory;

    /**
     * @var \Psr\Log\LoggerInterface
     */
    private $logger;

    /**
     * @var int
     */
    private $batchCount;

    /**
     * @var int
     */
    private $batchCacheCount;

    /**
     * IndexBuilder constructor.
     * @param \Psr\Log\LoggerInterface $logger
     * @param \Lofmp\SellerBadge\Model\ResourceModel\SellerBadgeManager $sellerBadgeManagerResource
     * @param \Lofmp\SellerBadge\Model\ResourceModel\SellerBadge\CollectionFactory $SellerBadgeCollectionFactory
     * @param \Lof\MarketPlace\Model\ResourceModel\Seller\CollectionFactory $sellerCollectionFactory
     * @param int $batchCount
     * @param int $batchCacheCount
     */
    public function __construct(
        \Psr\Log\LoggerInterface $logger,
        \Lofmp\SellerBadge\Model\ResourceModel\SellerBadgeManager $sellerBadgeManagerResource,
        \Lofmp\SellerBadge\Model\ResourceModel\SellerBadge\CollectionFactory $SellerBadgeCollectionFactory,
        \Lof\MarketPlace\Model\ResourceModel\Seller\CollectionFactory $sellerCollectionFactory,
        $batchCount = 1000,
        $batchCacheCount = 100
    ) {
        $this->logger = $logger;
        $this->batchCount = $batchCount;
        $this->batchCacheCount = $batchCacheCount;
        $this->sellerBadgeManagerResource = $sellerBadgeManagerResource;
        $this->sellerCollectionFactory = $sellerCollectionFactory;
        $this->sellerBadgeCollectionFactory = $SellerBadgeCollectionFactory;
    }

    /**
     * @throws LocalizedException
     */
    public function reindexFull()
    {
        $this->sellerBadgeManagerResource->beginTransaction();
        try {
            $this->doReindexFull();
            $this->sellerBadgeManagerResource->commit();
        } catch (\Exception $e) {
            $this->sellerBadgeManagerResource->rollBack();
            $this->logger->critical($e->getMessage());
            throw new LocalizedException(__($e->getMessage()), $e);
        }
    }

    /**
     * @return $this
     * @throws LocalizedException
     */
    public function doReindexFull()
    {
        $this->sellerBadgeManagerResource->cleanAllIndex();

        $ids = $this->getAllSellerIds();
        foreach ($this->getAllBadge() as $badge) {
            $this->reindexByBadgeAndSellerIds($badge, $ids);
        }

        return $this;
    }

    /**
     * @param $id
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function reindexByBadgeId($id)
    {
        $this->reindexByBadgeIds([$id]);
    }

    /**
     * @param $ids
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    private function cleanByBadgeIds($ids)
    {
        $this->sellerBadgeManagerResource->cleanByBadgeIds($ids);
    }

    /**
     * @param $ids
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function reindexByBadgeIds($ids)
    {
        $this->sellerBadgeManagerResource->beginTransaction();
        try {
            $this->cleanByBadgeIds($ids);
            $this->doReindexByBadgeIds($ids);
            $this->sellerBadgeManagerResource->commit();
        } catch (\Exception $e) {
            $this->sellerBadgeManagerResource->rollBack();
            $this->logger->critical($e);
            throw new \Magento\Framework\Exception\LocalizedException(
                __("Lofmp Seller Badge indexing failed. See details in exception log.")
            );
        }
    }

    /**
     * @param $ids
     * @return IndexBuilder
     * @throws LocalizedException
     */
    private function doReindexByBadgeIds($ids)
    {
        $productIds = $this->getAllSellerIds();
        foreach ($this->getBadgeCollection($ids) as $flashSale) {
            $this->reindexByBadgeAndSellerIds($flashSale, $productIds);
        }

        return $this;
    }

    /**
     * @param $ids
     * @return \Lofmp\SellerBadge\Model\ResourceModel\SellerBadge\Collection
     */
    public function getBadgeCollection($ids)
    {
        return $this->sellerBadgeCollectionFactory->create()
            ->addFieldToFilter('badge_id', $ids)
            ->addFieldToFilter('is_active', 1);
    }

    /**
     * @return \Lofmp\SellerBadge\Model\ResourceModel\SellerBadge\Collection
     */
    public function getAllBadge()
    {
        return $this->sellerBadgeCollectionFactory->create()
            ->addFieldToFilter('is_active', 1);
    }

    /**
     * @return array
     */
    public function getAllSellerIds(): array
    {
        return $this->sellerCollectionFactory->create()->getAllIds();
    }

    /**
     * @param \Lofmp\SellerBadge\Model\SellerBadge $badge
     * @param null $ids
     * @return $this
     * @throws LocalizedException
     */
    public function reindexByBadgeAndSellerIds(\Lofmp\SellerBadge\Model\SellerBadge $badge, $ids = null)
    {
        if (!$ids) {
            return $this;
        }

        $test = $this->prepareData($badge, $ids);

        list($rows, $sellerIds) = $this->prepareData($badge, $ids);

        if (!empty($rows)) {
            $this->sellerBadgeManagerResource->insertIndexData($rows);
        }

        if (!empty($sellerIds)) {

            // TODO: Implement reindexByBadgeAndSellerIds() method.
        }

        return $this;
    }

    /**
     * @param \Lofmp\SellerBadge\Model\SellerBadge $badge
     * @param $ids
     * @return array[]
     * @throws LocalizedException
     */
    public function prepareData(\Lofmp\SellerBadge\Model\SellerBadge $badge, $ids)
    {
        $count = 0;
        $rows = [];
        $sellerIds = [];
        $matchedSellerIds = $badge->getBadgeMatchingSellerIds($ids);

        if ($matchedSellerIds) {
            foreach ($ids as $sellerId) {
                $sellerId = (int)$sellerId;
                if (array_key_exists($sellerId, $matchedSellerIds)) {

                    $rows[] = [
                        \Lofmp\SellerBadge\Api\Data\SellerBadgeManagerInterface::SELLER_ID => $sellerId,
                        \Lofmp\SellerBadge\Api\Data\SellerBadgeManagerInterface::BADGE_ID => $badge->getId(),
                    ];
                    $count++;
                    $sellerIds[] = $sellerId;

                    if ($count >= $this->batchCount) {

                        $this->sellerBadgeManagerResource->insertIndexData($rows);

                        $rows = [];
                        $count = 0;
                    }

                    if (count($sellerIds) > $this->batchCacheCount) {

                        // TODO: Implement prepareData() method.

                        $sellerIds = [];
                    }
                }


            }
        }

        return [$rows, $sellerIds];
    }
}
