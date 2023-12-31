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

namespace Lofmp\SellerBadge\Cron;

use Lofmp\SellerBadge\Helper\Data;
use Lofmp\SellerBadge\Model\Indexer\SellerBadgeManagerIndexer;
use Magento\Framework\Exception\LocalizedException;
use Psr\Log\LoggerInterface;

class DailyBadgeUpdate
{
    /**
     * @var SellerBadgeManagerIndexer
     */
    private $sellerBadgeManagerIndexer;

    /**
     * @var LoggerInterface
     */
    private $logger;

    /**
     * @var Data
     */
    private $helperData;

    /**
     * DailyBadgeUpdate constructor.
     * @param Data $helperData
     * @param SellerBadgeManagerIndexer $sellerBadgeManagerIndexer
     * @param LoggerInterface $logger
     */
    public function __construct(
        Data $helperData,
        SellerBadgeManagerIndexer $sellerBadgeManagerIndexer,
        LoggerInterface $logger
    ) {
        $this->helperData = $helperData;
        $this->logger = $logger;
        $this->sellerBadgeManagerIndexer = $sellerBadgeManagerIndexer;
    }

    /**
     * @throws \Throwable
     */
    public function execute()
    {
        try {
            if ($this->sellerBadgeManagerIndexer->isIndexerScheduled()) {
                $this->helperData->reindexSellerBadge();
            } else {
                $this->sellerBadgeManagerIndexer->getIndexer()->invalidate();
            }
        } catch (LocalizedException $e) {
            $this->logger->critical($e);
        }
    }
}
