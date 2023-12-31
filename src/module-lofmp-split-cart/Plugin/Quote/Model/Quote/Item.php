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
 * @package    Lofmp_SplitCart
 * @copyright  Copyright (c) 2021 Landofcoder (https://www.landofcoder.com/)
 * @license    https://landofcoder.com/terms
 */

namespace Lofmp\SplitCart\Plugin\Quote\Model\Quote;

use Lof\MarketPlace\Model\ResourceModel\SellerProduct\CollectionFactory;
use Lofmp\SplitCart\Helper\ConfigData;

class Item
{
    /**
     * @var CollectionFactory
     */
    private $sellerProductCollectionFactory;

    /**
     * @var ConfigData
     */
    private $moduleConfig;

    /**
     * @param CollectionFactory $sellerProductCollectionFactory
     * @param ConfigData $configData
     */
    public function __construct(
        CollectionFactory $sellerProductCollectionFactory,
        ConfigData $configData
    ) {
        $this->sellerProductCollectionFactory = $sellerProductCollectionFactory;
        $this->moduleConfig = $configData;
    }

    /**
     * @param \Magento\Quote\Model\Quote\Item $subject
     * @return void|null
     */
    public function beforeSave(\Magento\Quote\Model\Quote\Item $subject)
    {
        if ($this->moduleConfig->isEnabled()) {
            $sellerId = 0;
            $productId = $subject->getProductId();
            $collection = $this->sellerProductCollectionFactory->create();
            $collection->addFieldToFilter('product_id', $productId);
            if (!count($collection)) {
                $subject->setSellerId(0);
                return null;
            } else {
                foreach ($collection as $item) {
                    $sellerId = $item->getSellerId();
                }
                $subject->setSellerId($sellerId);
                return null;
            }
        }
    }
}
