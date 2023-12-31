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
namespace Lof\Auction\Block\Plugin;

use Lof\Auction\Helper\Data;
use Magento\Catalog\Block\Product\ListProduct;
use Magento\Framework\Exception\NoSuchEntityException;

/**
 * Class ProductListUpdateForAuction
 * @package Lof\Auction\Block\Plugin
 */
class ProductListUpdateForAuction
{
    /**
     * @var Data
     */
    protected $helperData;

    /**
     * ProductListUpdateForAuction constructor.
     * @param Data $helperData
     */
    public function __construct(Data $helperData)
    {
        $this->helperData = $helperData;
    }

    /**
     * @param ListProduct $list
     * @param $proceed
     * @param $product
     * @return string
     * @throws NoSuchEntityException
     */
    public function aroundGetProductPrice(ListProduct $list, $proceed, $product)
    {
        $auctionDetail = $this->helperData->getProductAuctionDetail($product);
        return $proceed($product) . $auctionDetail;
    }
}
