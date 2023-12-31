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
 * @author    Landofcoder <info@landofcoder.com>
 * @license    https://landofcoder.com/terms
 */
namespace Lof\MarketPlace\Plugin;

class LayerPlugin
{
    /**
     * Core registry
     *
     * @var \Magento\Framework\Registry
     */
    protected $registry = null;

    /**
     * Constructor.
     *
     * @param \Magento\Framework\Registry $registry
     */
    public function __construct(
        \Magento\Framework\Registry $registry
    ) {
        $this->registry= $registry;
    }

    /**
     * {@inheritDoc}
     */
    public function beforePrepareProductCollection(
        \Magento\Catalog\Model\Layer $layer,
        \Magento\Catalog\Model\ResourceModel\Collection\AbstractCollection $collection
    ) {
        $this->setSellerParams($layer, $collection);
    }

    /**
     * Apply Current Seller Id to the collection.
     *
     * @param \Magento\Catalog\Model\Layer                                       $layer      Catalog / search layer.
     * @param \Magento\Catalog\Model\ResourceModel\Collection\AbstractCollection $collection Product collection.
     *
     * @return $this
     */
    private function setSellerParams(
        \Magento\Catalog\Model\Layer $layer,
        \Magento\Catalog\Model\ResourceModel\Collection\AbstractCollection $collection
    ) {
        $seller = $this->getCurrentSeller($layer);
        if ($seller) {
            $sellerId = is_object($seller)?$seller->getId():(int)$seller;
            $collection->addAttributeToFilter("seller_id", $sellerId);
        }
        return $this;
    }

    /**
     * Retrieve current seller model
     * If no seller found in registry, the root will be taken
     * @param \Magento\Catalog\Model\Layer                                       $layer      Catalog / search layer.
     * @return \Lof\MarketPlace\Model\Seller|int|null
     */
    private function getCurrentSeller($layer)
    {
        $seller = $layer->getData('current_seller');
        if ($seller === null) {
            $seller = $this->registry->registry('current_seller');
            if ($seller) {
                $layer->setData('current_seller', $seller);
            }
        }
        return $seller;
    }
}
