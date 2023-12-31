<?php
/**
 * LandofCoder
 * 
 * NOTICE OF LICENSE
 * 
 * This source file is subject to the landofcoder.com license that is
 * available through the world-wide-web at this URL:
 * https://landofcoder.com/license
 * 
 * DISCLAIMER
 * 
 * Do not edit or add to this file if you wish to upgrade this extension to newer
 * version in the future.
 * 
 * @category   LandofCoder
 * @package    Lofmp_CouponCode
 * @copyright  Copyright (c) 2016 Landofcoder (https://landofcoder.com/)
 * @license    https://landofcoder.com/LICENSE-1.0.html
 */
namespace Lofmp\CouponCode\Block\Adminhtml\Report\Sales;

/**
 * Adminhtml sales report page content block
 *
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Coupon extends \Magento\Backend\Block\Widget\Grid\Container
{
    /**
     * Template file
     *
     * @var string
     */
    protected $_template = 'report/grid/container.phtml';

 
    /**
     * {@inheritdoc}
     */
    protected function _construct()
    {   
        $this->_blockGroup = 'Lofmp_CouponCode';
        $this->_controller = 'adminhtml_report_sales_coupon';
        $this->_headerText = __('Report'); 
        parent::_construct(); 
        $this->buttonList->remove('add');
        $this->addButton(
            'filter_form_submit',
            ['label' => __('Show Report'), 'onclick' => 'filterFormSubmit()', 'class' => 'primary']
        ); 
    }

    /**
     * Get filter URL
     * 
     * @return string
     */
    public function getFilterUrl()
    {
        $this->getRequest()->setParam('loffilter', null);
        return $this->getUrl('*/*/coupon', ['_current' => true]);
    }
    public function setReportType($type = "") {
        if($type) {
            $this->_report_type = $type;
        }
    }
}
