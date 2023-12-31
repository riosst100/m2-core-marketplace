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
 * @package    Lofmp_TimeDiscount
 * @copyright  Copyright (c) 2017 Landofcoder (http://www.landofcoder.com/)
 * @license    http://www.landofcoder.com/LICENSE-1.0.html
 */
namespace Lofmp\TimeDiscount\Block\Adminhtml\Product;

class ProductSetAttribute extends \Magento\Backend\Block\Template
{

    /**
     * @param \Magento\Backend\Block\Template\Context   $context
     * @param \Magento\Framework\Registry               $coreRegistry
     * @param array                                     $data = []
     */
    public function __construct(
        \Magento\Backend\Block\Template\Context $context,
        \Magento\Framework\Registry $coreRegistry,
        array $data = []
    ) {
        $this->coreRegistry = $coreRegistry;
        parent::__construct($context);

    }
      /**
     * Define block template
     *
     * @return void
     */
    protected function _construct()
    {
        $this->setTemplate('attribute.phtml');
        parent::_construct();
    }
}
