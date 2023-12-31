<?php
/**
 * Landofcoder
 * 
 * NOTICE OF LICENSE
 * 
 * This source file is subject to the Landofcoder.com license that is
 * available through the world-wide-web at this URL:
 * http://www.landofcoder.com/license-agreement.html
 * 
 * DISCLAIMER
 * 
 * Do not edit or add to this file if you wish to upgrade this extension to newer
 * version in the future.
 * 
 * @category   Landofcoder
 * @package    Lofmp_TimeDiscount
 * @copyright  Copyright (c) 2017 Landofcoder (http://www.landofcoder.com/)
 * @license    http://www.landofcoder.com/LICENSE-1.0.html
 */

namespace Lofmp\TimeDiscount\Block\Seller;

class Product extends  \Magento\Backend\Block\Widget\Container
{
    
    /**
     * @var \Magento\Catalog\Model\Product\TypeFactory
     */
    protected $_typeFactory;
    
    /**
     * @var \Magento\Catalog\Model\ProductFactory
     */
    protected $_productFactory;
    
    /**
     * @var \Lofmp\TimeDiscount\Helper\Data
     */
    protected $_productHelper;

    /**
     * @return void
     */
    protected function _construct()
    {
        $this->_blockGroup = 'Lofmp_TimeDiscount';
        $this->_controller = 'seller_product';
        $this->_headerText = __('Manage Action Products');
        $this->_addButtonLabel = __('Add Action Product');
        
        parent::_construct();
        //$this->removeButton('add');
    }
    
    
    /**
     * @param \Magento\Backend\Block\Widget\Context $context
     * @param \Magento\Catalog\Model\Product\TypeFactory $typeFactory
     * @param \Magento\Catalog\Model\ProductFactory $productFactory
     * @param array $data
     */
    public function __construct(
        \Magento\Backend\Block\Widget\Context $context,
        \Magento\Catalog\Model\Product\TypeFactory $typeFactory,
        \Magento\Catalog\Model\ProductFactory $productFactory,
        \Lofmp\TimeDiscount\Helper\Data $productHelper,
        array $data = []
    ) {
        $this->_productFactory  = $productFactory;
        $this->_typeFactory     = $typeFactory;
        $this->_productHelper   = $productHelper;
        
        parent::__construct($context, $data);
        $this->removeButton('add');
    }
    
    /**
     * Prepare button and grid
     *
     * @return \Magento\Catalog\Block\Adminhtml\Product
     */
    protected function _prepareLayout()
    {
        $this->pageConfig->getTitle()->set(__("Manage Time Discount Products"));   
        return parent::_prepareLayout();
    }
}