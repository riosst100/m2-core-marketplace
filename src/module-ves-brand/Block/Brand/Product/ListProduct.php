<?php
/**
 * Venustheme
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Venustheme.com license that is
 * available through the world-wide-web at this URL:
 * http://www.venustheme.com/license-agreement.html
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade this extension to newer
 * version in the future.
 *
 * @category   Venustheme
 * @package    Ves_Brand
 * @copyright  Copyright (c) 2014 Venustheme (http://www.venustheme.com/)
 * @license    http://www.venustheme.com/LICENSE-1.0.html
 */
namespace Ves\Brand\Block\Brand\Product;

use Magento\Catalog\Api\CategoryRepositoryInterface;
use Magento\Catalog\Model\Category;
use Magento\Catalog\Model\Product;
use Magento\Eav\Model\Entity\Collection\AbstractCollection;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\DataObject\IdentityInterface;

class ListProduct extends \Magento\Catalog\Block\Product\ListProduct
{
    /**
     * Catalog product visibility
     *
     * @var \Magento\Catalog\Model\Product\Visibility
     */
    protected $_catalogProductVisibility;
    /**
     * @var \Magento\Catalog\Model\ResourceModel\Product\CollectionFactory
     */
    protected $_productCollectionFactory;

    public function __construct(
        \Magento\Catalog\Block\Product\Context $context,
        \Magento\Framework\Data\Helper\PostHelper $postDataHelper,
        \Magento\Catalog\Model\Layer\Resolver $layerResolver,
        CategoryRepositoryInterface $categoryRepository,
        \Magento\Framework\Url\Helper\Data $urlHelper,
        \Magento\Catalog\Model\ResourceModel\Product\CollectionFactory $productCollectionFactory,
        \Magento\Catalog\Model\Product\Visibility $catalogProductVisibility,
        array $data = []
    ) {
        $this->_productCollectionFactory = $productCollectionFactory;
        $this->_catalogProductVisibility = $catalogProductVisibility;
        $this->_catalogLayer = $layerResolver->get();
        $this->_postDataHelper = $postDataHelper;
        $this->categoryRepository = $categoryRepository;
        $this->urlHelper = $urlHelper;
        parent::__construct(
                $context,
                $postDataHelper,
                $layerResolver,
                $categoryRepository,
                $urlHelper,
                $data
            );
    }
    /**
     * {@inheritdoc}
     */
    protected function _getProductCollection()
    {
        if ($this->_productCollection === null) {
            $layer = $this->getLayer();
            $brand = $this->_coreRegistry->registry('current_brand');
            if($brand){
                $layer->setCurrentBrand($brand);
            }
            //$productIds = $brand->getData('productIds');
            $collection = $this->_productCollectionFactory->create();
            $resource = $collection->getResource();
            $collection->setVisibility($this->_catalogProductVisibility->getVisibleInCatalogIds())
                        ->addAttributeToSelect('*')
                        ->joinTable(
                            ['brand_products' => $resource->getTable('ves_brand_product')],
                            'product_id = entity_id',
                            [
                                'brand_id' => 'brand_id'
                            ]
                        )
                        ->addFieldToFilter('brand_id', ['eq' => $brand->getId()]);
                        //->addAttributeToFilter('product_brand',['like'=>"%".$brand->getId()."%"]);
            $this->_productCollection = $collection;
        }
        return parent::_getProductCollection();
    }
}
