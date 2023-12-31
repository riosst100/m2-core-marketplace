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
 * @package    Lofmp_LayeredNavigation
 * @copyright  Copyright (c) 2021 Landofcoder (https://www.landofcoder.com/)
 * @license    https://landofcoder.com/terms
 */
namespace Lofmp\LayeredNavigation\Block\Seller\Product;

use Magento\Catalog\Api\CategoryRepositoryInterface;
use Magento\Catalog\Model\Product;

class ListProduct extends \Lof\MarketPlace\Block\Seller\Product\ListProduct{

    /**
     * @var \Magento\Catalog\Model\ResourceModel\Product\CollectionFactory
     */
    protected $_newProductCollectionFactory;

    /**
     * @var \Magento\Eav\Model\Config
     */
    private $_eavConfig;

    /**
     * @var \Lofmp\LayeredNavigation\Helper\Data
     */
    protected $helperData;

    /**
     * ListProduct constructor.
     * @param \Magento\Catalog\Block\Product\Context $context
     * @param \Magento\Framework\Data\Helper\PostHelper $postDataHelper
     * @param \Magento\Catalog\Model\Layer\Resolver $layerResolver
     * @param CategoryRepositoryInterface $categoryRepository
     * @param \Magento\Framework\Url\Helper\Data $urlHelper
     * @param \Magento\Catalog\Model\ResourceModel\Product\CollectionFactory $productCollectionFactory
     * @param \Magento\Catalog\Model\Product\Visibility $catalogProductVisibility
     * @param \Lof\MarketPlace\Model\VacationFactory $vacationFactory
     * @param \Lof\MarketPlace\Helper\DateTime $helperDateTime
     * @param \Magento\Eav\Model\Config $eavConfig
     * @param \Lofmp\LayeredNavigation\Helper\Data $helperData
     * @param array $data
     */
    public function __construct(
        \Magento\Catalog\Block\Product\Context $context,
        \Magento\Framework\Data\Helper\PostHelper $postDataHelper,
        \Magento\Catalog\Model\Layer\Resolver $layerResolver,
        CategoryRepositoryInterface $categoryRepository,
        \Magento\Framework\Url\Helper\Data $urlHelper,
        \Magento\Catalog\Model\ResourceModel\Product\CollectionFactory $productCollectionFactory,
        \Magento\Catalog\Model\Product\Visibility $catalogProductVisibility,
        \Lof\MarketPlace\Model\VacationFactory $vacationFactory,
        \Lof\MarketPlace\Helper\DateTime $helperDateTime,
        \Magento\Eav\Model\Config $eavConfig,
        \Lofmp\LayeredNavigation\Helper\Data $helperData,
        array $data = []
    ) {
        $this->_newProductCollectionFactory = $productCollectionFactory;
        $this->_eavConfig = $eavConfig;
        $this->helperData = $helperData;
        parent::__construct($context, $postDataHelper, $layerResolver, $categoryRepository, $urlHelper, $productCollectionFactory, $catalogProductVisibility,$vacationFactory,$helperDateTime,$data);
    }

    /**
     * {@inheritdoc}
     */
    public function _getProductCollection() {
        $request = $this->getRequest();
        $post = $request->getParams();
        if($post && $this->helperData->isEnabled()){
            if ($this->_productCollection === null) {
                $layer = $this->getLayer();
                $seller = $this->_coreRegistry->registry('current_seller');
                if($seller){
                    $layer->setCurrentSeller($seller);
                }
                $products = $seller->getData('products');
                $productIds = [];
                foreach ($products as $k => $v) {
                    $productIds[] = $v['product_id'];
                }
                $collection = $this->_newProductCollectionFactory->create();
                $collection->setVisibility($this->_catalogProductVisibility->getVisibleInCatalogIds())
                    ->addAttributeToSelect('*')
                    ->addAttributeToFilter('entity_id', ['in'=>$productIds])
                    ->addAttributeToFilter('approval', 2);
                $attributeParam = array();
                if(count($post) >= 2) {
                    $flag = 0;
                    foreach ($post as $key => $value) {
                        if($key != 'seller_id' && $key !='product_list_order' && $key != 'price' && $key != 'product_list_limit' && $key != 'p' && $key != "product_list_mode"){
                            if ($this->isProductAttributeExists($key)) {
                                $attributeParam[] = array('attribute' => $key, 'eq' => $value);
                                $flag = 1;
                            }
                        } else if($key == 'price') {
                            $priceToFilter = explode("-", $value);
                            $attributeParam[] = array('attribute' => $key, 'from' => $priceToFilter[0], 'to' => $priceToFilter[1]);
                            $flag = 1;
                        } else if($key == 'product_list_order'){
                            $collection->setOrder($value,'ASC');
                        } else if ($key == 'product_list_limit'){
                            $collection->setPageSize($value);
                        } else if ($key == 'p'){
                            $collection->setCurPage($value);
                        }
                    }
                    if($flag == 1){
                        $collection->addAttributeToFilter($attributeParam);
                    }
                }

                $this->_productCollection = $collection;
            }
            return $this->_productCollection;
        }else{
            return parent::_getProductCollection(); // TODO: Change the autogenerated stub
        }
    }

    /**
     * Returns true if attribute exists and false if it doesn't exist
     *
     * @param string $field
     * @return bool
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function isProductAttributeExists($field)
    {
        $attr = $this->_eavConfig->getAttribute(Product::ENTITY, $field);
 
        return ($attr && $attr->getId());
    }
}
