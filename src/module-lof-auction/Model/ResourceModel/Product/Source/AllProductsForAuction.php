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
use Lof\Auction\Model\ResourceModel\Product\Source\AuctionStatus;

class AllProductsForAuction
{
    /**
     * @param \Magento\Catalog\Model\ProductFactory $productFactory
     */
    protected $productFactory;

    /**
     * @param \Lof\Auction\Model\Product $auctionProduct
     */
    protected $auctionProduct;

    public function __construct(
        \Magento\Catalog\Model\ProductFactory $productFactory,
        \Lof\Auction\Model\ProductFactory $auctionProductFactory
    ) {

        $this->productFactory = $productFactory;
        $this->auctionProduct = $auctionProductFactory;
    }

    /**
     * Return options array.
     *
     * @param int $productId
     * @param int $store
     *
     * @return array
     */
    public function productListForAuction($productId, $store = null)
    {
        $productArr[] = ['value' => '','label' => 'Select Product'];
        $auctionProducts = $this->getProductsInAuction();
        $productList = $this->productFactory->create()->getCollection()
                            ->addAttributeToSelect('*')
                            ->addFieldToFilter('type_id', ['neq' => 'bundle'])
                            ->addFieldToFilter('type_id', ['neq' => 'grouped'])
                            ->addFieldToFilter('auction_type', ['like' => '%'.'2'.'%']);

        if ($productId) {
            $productArr = [];
            $productList->addFieldToFilter('entity_id', ['eq' => $productId]);
        } else {
            $productList->addFieldToFilter('entity_id', ['nin' => $auctionProducts]);
        }
        foreach ($productList as $product) {
            $productArr[] = [
                'value' => $product->getEntityId(),
                'label' => $product->getName().' ( '.$product->getSku().' )'
            ];
        }
        return $productArr;
    }

    /**
     * Get options in "key-value" format.
     *
     * @return array
     */
    public function toArray()
    {
        $optionList = $this->toOptionArray();
        $optionArray = [];
        foreach ($optionList as $option) {
            $optionArray[$option['value']] = $option['label'];
        }

        return $optionArray;
    }

    /**
     * Get all products which are not in auction.
     */
    public function getProductsInAuction()
    {
        $auctionProArray = [0];
        $auctionProList = $this->auctionProduct->create()->getCollection()
                                        ->addFieldToFilter('auction_status', 
                                        [
                                            'in' => 
                                                [
                                                    AuctionStatus::STATUS_TIME_END, 
                                                    AuctionStatus::STATUS_PROCESS
                                                ]
                                        ]);
        foreach ($auctionProList as $auctionPro) {
            if ($auctionPro->getProductId()) {
                $auctionProArray[] = $auctionPro->getProductId();
            }
        }
        return $auctionProArray;
    }
}
