<?php
/**
 * Copyright © 2016 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Lofmp\LayeredNavigation\Model\Layer\SellerHomePage;

use Magento\Catalog\Model\Layer\CollectionFilterInterface;

class CollectionFilter implements CollectionFilterInterface
{
    /**
     * Catalog product visibility.
     *
     * @var \Magento\Catalog\Model\Product\Visibility
     */
    protected $productVisibility;

    /**
     * Catalog config.
     *
     * @var \Magento\Catalog\Model\Config
     */
    protected $catalogConfig;

    /**
     * @var \Magento\Framework\Registry
     */
    protected $registry;

    /**
     * CollectionFilter constructor.
     *
     * @param \Magento\Catalog\Model\Product\Visibility $productVisibility
     * @param \Magento\Catalog\Model\Config             $catalogConfig
     */
    public function __construct(
        \Magento\Catalog\Model\Product\Visibility $productVisibility,
        \Magento\Catalog\Model\Config $catalogConfig,
        \Magento\Framework\Registry $registry
    ) {
        $this->productVisibility = $productVisibility;
        $this->catalogConfig = $catalogConfig;
        $this->registry = $registry;
    }

    /**
     * Filter product collection.
     *
     * @param \Magento\Catalog\Model\ResourceModel\Product\Collection $collection
     * @param \Magento\Catalog\Model\Category                         $category
     */
    public function filter(
        $collection,
        \Magento\Catalog\Model\Category $category
    ) {
        $seller = $this->registry->registry('current_seller');
        $collection
            ->addAttributeToSelect($this->catalogConfig->getProductAttributes())
            ->addMinimalPrice()
            ->addFinalPrice()
            ->addTaxPercents()
            ->addUrlRewrite($category->getId())
            ->setVisibility($this->productVisibility->getVisibleInCatalogIds());


        // $products = $seller->getData('products');
        // $productIds = [];
        // foreach ($products as $k => $v) {
        //     $productIds[] = $v['product_id'];
        // }

        // $collection->addAttributeToFilter('entity_id',['in'=>$productIds]);
        if ($seller) {
            $collection->addAttributeToFilter('seller_id', $seller->getId());
        }

    }
}
