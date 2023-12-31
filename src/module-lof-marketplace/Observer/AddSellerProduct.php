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

namespace Lof\MarketPlace\Observer;

use Magento\Framework\Event\ObserverInterface;
use Magento\Catalog\Model\ProductFactory;

class AddSellerProduct implements ObserverInterface
{
    /**
     * @var ProductFactory
     */
    protected $_productFactory;

    /**
     * AddSellerProduct constructor.
     * @param ProductFactory $productFactory
     */
    public function __construct(
        ProductFactory $productFactory
    ) {
        $this->_productFactory = $productFactory;
    }

    /**
     * Set the vendor id in bulk for product
     *
     * @param \Magento\Framework\Event\Observer $observer
     * @return void
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function execute(\Magento\Framework\Event\Observer $observer)
    {
        $attrData = $observer->getAttributesData();
        if (isset($attrData['seller_id'])) {
            $productIds = $observer->getProductIds();
            $resource = $this->_productFactory->create()->getResource();

            $adapter = $resource->getConnection();
            // TODO: (assigned AndyHoangHuu) convert RawQuery to ORM query.
            // phpcs:disable Magento2.SQL.RawQuery.FoundRawSql
            // phpcs:disable Generic.Files.LineLength.TooLong
            $sql = "UPDATE " . $resource->getTable('catalog_product_entity')
                . ' SET seller_id="' . $attrData['seller_id']
                . '" WHERE entity_id IN(' . implode(",", $productIds) . ')';
            $adapter->query($sql);

            unset($attrData['seller_id']);
            $observer->getEvent()->setData('attributes_data', $attrData);
        }
    }
}
