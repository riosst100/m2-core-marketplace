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
 * @package    Lof_Auction
 * @copyright  Copyright (c) 2021 Landofcoder (https://www.landofcoder.com/)
 * @license    https://landofcoder.com/terms
 */

namespace Lof\Auction\Model\ResourceModel\Product\Source;

use Lof\Auction\Helper\Data;
use Lof\Auction\Model\ResourceModel\Product\CollectionFactory;
use Magento\Framework\Data\OptionSourceInterface;

/**
 * Class Products
 * @package Lof\Auction\Model\ResourceModel\Product\Source
 */
class Products implements OptionSourceInterface
{
    /**
     * @var Data
     */
    private $helper;
    /**
     * @var CollectionFactory
     */
    private $auctionCollection;

    /**
     * AuctionStatus constructor.
     * @param Data $data
     * @param CollectionFactory $auctionCollection
     */
    public function __construct(
        Data $data,
        CollectionFactory $auctionCollection
    ) {
        $this->helper = $data;
        $this->auctionCollection = $auctionCollection;
    }

    /**
     * Get Auction type labels array for option element.
     *
     * @return array
     */
    public function getOptions()
    {
        $collection = $this->auctionCollection->create();
        $res = [];
        foreach ($collection as $key => $auction) {
            $productId = $auction->getProductId();
            $productName = $this->helper->getProductById($productId)->getName();
            $res[] = ['value' => $productId, 'label' => $productName];
        }

        return $res;
    }

    /**
     * {@inheritdoc}
     */
    public function toOptionArray()
    {
        return $this->getOptions();
    }
}
