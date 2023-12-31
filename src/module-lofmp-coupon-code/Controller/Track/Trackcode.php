<?php
/**
 * Landofcoder
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
 * @category   Landofcoder
 * @package    Lofmp_CouponCode
 * @copyright  Copyright (c) 2017 Landofcoder (https://www.landofcoder.com/)
 * @license    https://www.landofcoder.com/LICENSE-1.0.html
 */

namespace Lofmp\CouponCode\Controller\Track;

use Magento\Framework\App\Action\Action;
use Magento\Store\Model\StoreManagerInterface;

class Trackcode extends Action
{
    /**
     * @var StoreManagerInterface
     */
    protected $_storeManager;

    /**
     * @var \Magento\Framework\View\Result\PageFactory
     */
    protected $_resultPageFactory;

    /**
     * @var \Magento\Customer\Model\Session
     */
    protected $_customerSession;

    /**
     * @var \Magento\Framework\Registry
     */
    protected $_coreRegistry;

    /**
     * @var \Magento\Framework\Controller\Result\ForwardFactory
     */
    protected $resultForwardFactory;

    /**
     * @var \Lofmp\CouponCode\Helper\Data
     */
    protected $_couponHelper;

    /**
     * @param \Magento\Framework\App\Action\Context $context
     * @param \Magento\Framework\View\Result\PageFactory $resultPageFactory
     * @param StoreManagerInterface $storeManager
     * @param \Lofmp\CouponCode\Helper\Data $helper
     * @param \Magento\Customer\Model\Session $customerSession
     * @param \Magento\Framework\Controller\Result\ForwardFactory $resultForwardFactory
     * @param \Magento\Framework\Registry $coreRegistry
     */
    public function __construct(
        \Magento\Framework\App\Action\Context $context,
        \Magento\Framework\View\Result\PageFactory $resultPageFactory,
        StoreManagerInterface $storeManager,
        \Lofmp\CouponCode\Helper\Data $helper,
        \Magento\Customer\Model\Session $customerSession,
        \Magento\Framework\Controller\Result\ForwardFactory $resultForwardFactory,
        \Magento\Framework\Registry $coreRegistry
    ) {
        $this->_resultPageFactory = $resultPageFactory;
        $this->_storeManager = $storeManager;
        $this->_couponHelper = $helper;
        $this->_customerSession = $customerSession;
        $this->_coreRegistry = $coreRegistry;
        $this->resultForwardFactory = $resultForwardFactory;
        parent::__construct($context);
    }

    /*
    * Example Url:
    */
    public function execute()
    {
        $resultPage = $this->_resultPageFactory->create();
        if (!$this->_couponHelper->getConfig('general_settings/show')
            || !$this->_couponHelper->getConfig('general_settings/allow_track_log')
        ) {
            $resultForward = $this->resultForwardFactory->create();
            return $resultForward->forward('defaultnoroute');
        }

        $resultPage->getConfig()->getTitle()->set(__('Sales Info'));
        $coupon_code = $this->getRequest()->getParam('coupon_code');
        // $customer_email = $this->getRequest()->getParam('email');

        $coupon_code = trim($coupon_code);
        // $customer_email = trim($customer_email);
        if ($coupon_code) {
            $collection = $this->_objectManager->create('Lofmp\CouponCode\Model\Log')->getCollection();
            $collection = $collection->addFieldToFilter("coupon_code", $coupon_code);
            //->addFieldToFilter("email_address", $customer_email);

            if (0 < $collection->getSize()) {
                $this->_coreRegistry->register('lofmpcouponcode_log', $collection->getFirstItem());
            }
        }
        return $resultPage;
    }
}
