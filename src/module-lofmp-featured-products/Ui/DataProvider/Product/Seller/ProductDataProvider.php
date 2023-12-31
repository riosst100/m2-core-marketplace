<?php
/**
 * Landofcoder
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Landofcoder.com license that is
 * available through the world-wide-web at this URL:
 * http://landofcoder.com/license
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade this extension to newer
 * version in the future.
 *
 * @category   Landofcoder
 * @package    Lofmp_FeaturedProducts
 * @copyright  Copyright (c) 2018 Landofcoder (http://www.landofcoder.com/)
 * @license    http://www.landofcoder.com/LICENSE-1.0.html
 */

namespace Lofmp\FeaturedProducts\Ui\DataProvider\Product\Seller;

use Magento\Catalog\Model\ResourceModel\Product\CollectionFactory;

class ProductDataProvider extends \Magento\Catalog\Ui\DataProvider\Product\ProductDataProvider
{
    /**
     * @param string $name
     * @param string $primaryFieldName
     * @param string $requestFieldName
     * @param CollectionFactory $collectionFactory
     * @param \Lof\MarketPlace\Helper\Data $helper
     * @param array $addFieldStrategies
     * @param array $addFilterStrategies
     * @param array $meta
     * @param array $data
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function __construct(
        $name,
        $primaryFieldName,
        $requestFieldName,
        CollectionFactory $collectionFactory,
        \Lof\MarketPlace\Helper\Data $helper,
        array $addFieldStrategies = [],
        array $addFilterStrategies = [],
        array $meta = [],
        array $data = []
    ) {
        parent::__construct($name, $primaryFieldName, $requestFieldName, $collectionFactory,$addFieldStrategies,$addFilterStrategies,$meta,$data);
        $seller_id = $helper->getSellerId();
        $resource = $this->collection->getResource();

        $this->collection->addAttributeToSelect('*')
            ->joinTable(
                ['featured_product' => $resource->getTable('lofmp_featuredproducts_product')],
                'product_id = entity_id',
                [
                    'id' => 'id',
                    'seller_id' => 'seller_id',
                    'featured_from' => 'featured_from',
                    'featured_to' => 'featured_to',
                    'sort_order' => 'sort_order'
                ]
            )
            ->addFieldToFilter('seller_id', ['eq' => $seller_id]);
    }
}
