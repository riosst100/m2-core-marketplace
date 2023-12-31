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

namespace Lof\MarketPlace\Setup;

use Magento\Framework\Setup\UpgradeDataInterface;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\ModuleDataSetupInterface;
use Magento\Catalog\Api\ProductRepositoryInterface;

/**
 * @codeCoverageIgnore
 */
class UpgradeData implements UpgradeDataInterface
{

    /**
     * @var ProductRepositoryInterface
     */
    protected $productRepository;

    /**
     * @param ProductRepositoryInterface $productRepository
     */
    public function __construct(ProductRepositoryInterface $productRepository)
    {
        $this->productRepository = $productRepository;
    }

    /**
     * Upgrade data for a module
     *
     * @param ModuleDataSetupInterface $setup
     * @param ModuleContextInterface $context
     * @return void
     * @throws Exception
     */
    public function upgrade(ModuleDataSetupInterface $setup, ModuleContextInterface $context)
    {
        if (version_compare($context->getVersion(), '1.0.13', '<')) {
            $this->initSellerGroup($setup, $context);
        }
        if (version_compare($context->getVersion(), '1.2.0', '<')) {
            $this->collectSellerIdToItems($setup, $context);
        }
    }

    /**
     * Collect seller id to quote items and order items
     * 
     * @param ModuleDataSetupInterface $setup
     * @param ModuleContextInterface $context
     */
    protected function collectSellerIdToItems(ModuleDataSetupInterface $setup, ModuleContextInterface $context)
    {
        $select = $setup->getConnection()->select()->from('lof_marketplace_sellerorderitems', ['order_item_id', 'seller_id']);
        $rows = $setup->getConnection()->fetchAll($select);

        //update sales_order_item table
        foreach ($rows as $item) {
            $orderItemId = $item['order_item_id'] ?? 0;
            $setup->getConnection()->update(
                'sales_order_item',
                ['lof_seller_id' => $item['seller_id']],
                "item_id =$orderItemId"
            );
        }

        //update quote_item table
        $select = $setup->getConnection()->select()->from('quote_item', ['item_id', 'product_id', 'store_id']);
        $quoteItems = $setup->getConnection()->fetchAll($select);
        foreach ($quoteItems as $quoteItem) {
            $productId = $quoteItem['product_id'];
            $quoteId = $quoteItem['item_id'];
            $storeId = $quoteItem['store_id'];
            $product = null;
            try {
                $product = $this->productRepository->getById($productId, false, $storeId);
            } catch (\Exception $e) {
                echo $e->getMessage() . "\n";
            };
            if (!$product || !$product->getId()) {
                continue;
            }
            $sellerId = $product->getSellerId();
            if ($sellerId) {
                $setup->getConnection()->update('quote_item', ['lof_seller_id' => $sellerId], "item_id=$quoteId");
            }
        }
    }

    /**
     * {@inheritdoc}
     */
    public function initSellerGroup(ModuleDataSetupInterface $setup, ModuleContextInterface $context)
    {
        /**
         * install group affiliate
         */
        $data = [
            [
                'name' => 'Default',
                'url_key' => 'default',
                'status' => 1,
                'position' => 0,
                'shown_in_sidebar' => 1
            ]
        ];
        foreach ($data as $row) {
            $select = $setup->getConnection()->select()->from(
                ['maintable' => $setup->getTable('lof_marketplace_group')],
                ['group_id', 'name']
            )->where('url_key = ?', "default");
            $rows = $setup->getConnection()->fetchAll($select);
            if (!$rows) {
                $setup->getConnection()->insertForce($setup->getTable('lof_marketplace_group'), $row);
            }
        }
    }
}
