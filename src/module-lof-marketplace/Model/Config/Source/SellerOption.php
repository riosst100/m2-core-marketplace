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
 * @package    Lofmp_TableRateShipping
 * @copyright  Copyright (c) 2021 Landofcoder (https://www.landofcoder.com/)
 * @license    https://landofcoder.com/terms
 */

namespace Lof\MarketPlace\Model\Config\Source;

use Lof\MarketPlace\Model\ResourceModel\Seller\CollectionFactory;

class SellerOption implements \Magento\Framework\Option\ArrayInterface
{
    /**
     * @var CollectionFactory
     */
    protected $sellerCollectionFactory;

    /**
     * @param CollectionFactory $sellerCollectionFactory
     */
    public function __construct(
        CollectionFactory $sellerCollectionFactory
    ) {
        $this->sellerCollectionFactory = $sellerCollectionFactory;
    }

    /**
     * Options getter
     *
     * @param bool $addEmpty
     * @return array
     */
    public function toOptionArray($addEmpty = true)
    {
        $collection = $this->sellerCollectionFactory->create();
        $options = [];

        if ($addEmpty) {
            $options[] = ['label' => __('-- Please Select a Seller --'), 'value' => 0];
        }
        foreach ($collection as $_seller) {
            $options[] = ['label' => $_seller->getName(), 'value' => $_seller->getId()];
        }

        return $options;
    }
}
