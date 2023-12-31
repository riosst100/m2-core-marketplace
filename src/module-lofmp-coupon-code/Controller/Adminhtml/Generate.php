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
namespace Lofmp\CouponCode\Controller\Adminhtml;
use Magento\Backend\App\Action\Context;
use Magento\Framework\Registry;

/**
 * Cms manage blocks controller
 *
 * @author      Magento Core Team <core@magentocommerce.com>
 */
abstract class Generate extends \Magento\Backend\App\Action
{ 
 

    const ADMIN_RESOURCE = 'Lofmp_CouponCode::generate';


      /**
     * Core registry
     *
     * @var \Magento\Framework\Registry
     */
      protected $_coreRegistry;
    /**
     * @param \Magento\Backend\App\Action\Context              $context             
     * @param \Magento\Framework\Registry                      $coreRegistry        

     */
    public function __construct(
        \Magento\Backend\App\Action\Context $context,
        \Magento\Framework\Registry $coreRegistry
        ) {
        $this->_coreRegistry = $coreRegistry;
        parent::__construct($context);


    } 
    /**
     * Init page
     *
     * @param \Magento\Backend\Model\View\Result\Page $resultPage
     * @return \Magento\Backend\Model\View\Result\Page
     */
    protected function initPage($resultPage)
    {
        $resultPage->setActiveMenu('Lofmp_CouponCode::generate')
        ->addBreadcrumb(__('Generate Coupon Code'), __('Generate Coupon Code'))
        ->addBreadcrumb(__('Generate Coupon Code'), __('Generate Coupon Code'));
        return $resultPage;
    }
    /**
     * Check the permission to run it
     *
     * @return boolean
     */
    protected function _isAllowed()
    {
        return $this->_authorization->isAllowed('Lofmp_CouponCode::generate');
    }

}
