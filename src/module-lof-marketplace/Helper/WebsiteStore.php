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
 * @package    Lof_MarketPlace
 * @copyright  Copyright (c) 2021 Landofcoder (https://www.landofcoder.com/)
 * @license    https://landofcoder.com/terms
 */

namespace Lof\MarketPlace\Helper;

use Magento\Store\Api\StoreWebsiteRelationInterface;
use Magento\Framework\App\Helper\Context;
use Magento\Store\Model\StoreManagerInterface;

class WebsiteStore extends \Magento\Framework\App\Helper\AbstractHelper
{
    /**
     * @var StoreWebsiteRelationInterface
     */
    protected $storeWebsiteRelation;

    /**
     * @var StoreManagerInterface
     */
    protected $storeManager;

    /**
     * @var int[]
     */
    protected $storeIds = [];

    /**
     * WebsiteStore constructor.
     * @param Context $context
     * @param StoreWebsiteRelationInterface $storeWebsiteRelation
     * @param StoreManagerInterface $storeManager
     */
    public function __construct(
        Context $context,
        StoreWebsiteRelationInterface $storeWebsiteRelation,
        StoreManagerInterface $storeManager
    ) {
        $this->storeWebsiteRelation = $storeWebsiteRelation;
        $this->storeManager = $storeManager;
        parent::__construct($context);
    }

    /**
     * @return \Magento\Store\Api\Data\StoreInterface
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function getStore()
    {
        return $this->storeManager->getStore();
    }

    /**
     * @return mixed
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function getCurrentStoreId()
    {
        // give the current store id
        return $this->storeManager->getStore()->getStoreId();
    }

    /**
     * @return mixed
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function getWebsiteId()
    {
        return $this->storeManager->getStore(true)->getWebsite()->getId();
    }

    /**
     * Get store ids from the website
     * @param int $websiteId
     * @return array|int
     */
    public function getWebsteStoreIds($websiteId = 0)
    {
        $websiteId = $websiteId ? (int)$websiteId : $this->getWebsiteId();
        if (!isset($this->storeIds[$websiteId])) {
            try {
                $this->storeIds[$websiteId] = $this->storeWebsiteRelation->getStoreByWebsiteId($websiteId);
                // phpcs:disable Magento2.CodeAnalysis.EmptyBlock.DetectedCatch
            } catch (\Exception $e) {
                //
            }
        }
        return $this->storeIds[$websiteId];
    }
}
