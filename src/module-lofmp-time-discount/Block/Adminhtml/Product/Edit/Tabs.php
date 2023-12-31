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
 * 
 * @copyright  Copyright (c) 2017 Landofcoder (http://www.landofcoder.com/)
 * @license    http://www.landofcoder.com/LICENSE-1.0.html
 */
namespace Lofmp\TimeDiscount\Block\Adminhtml\Product\Edit;

class Tabs extends \Magento\Backend\Block\Widget\Tabs
{
    /**
     * @param \Magento\Backend\Block\Template\Context  $context
     * @param \Magento\Framework\Json\EncoderInterface $jsonEncoder,
     * @param \Magento\Framework\Registry              $registry
     * @param \Magento\Framework\Registry              $coreRegistry
     * @param array                                    $data
     */
    public function __construct(
        \Magento\Backend\Block\Template\Context $context,
        \Magento\Framework\Json\EncoderInterface $jsonEncoder,
        \Magento\Backend\Model\Auth\Session $authSession,
        \Magento\Framework\Registry $coreRegistry,
        array $data = []
    ) {
    
        $this->_coreRegistry = $coreRegistry;
        parent::__construct($context, $jsonEncoder, $authSession, $data);
    }

    protected function _construct()
    {
        parent::_construct(); 
        $this->setId('product_tabs');
        $this->setDestElementId('edit_form');
        $this->setTitle(__('Time Discount Information'));

        $this->addTab(
            'lofmptimediscount_product',
            [
            'label' => __('TimeDiscount Product'),
            'content' => $this->getLayout()->createBlock('Lofmp\TimeDiscount\Block\Adminhtml\Product\Edit\Tab\Main')->toHtml()
            ]
        );
    }

}
