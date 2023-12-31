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
 * @package    Lof_MarketPlace
 * @copyright  Copyright (c) 2016 Landofcoder (https://landofcoder.com/)
 * @license    https://landofcoder.com/LICENSE-1.0.html
 */

namespace Lofmp\CouponCode\Block\MarketPlace\Coupon;

class Coupon extends \Magento\Framework\View\Element\Template {

    protected $_coreRegistry = null;

    protected $_couponFactory;

    protected $_sellerhelper;

    protected $coupon;

    protected $_resource;

    protected $_rule;

    public function __construct(
        \Magento\Framework\View\Element\Template\Context $context,
        \Magento\Framework\Registry $registry,
        \Lofmp\CouponCode\Model\CouponFactory $CouponFactory,
        \Lof\MarketPlace\Helper\Data $seller_helper,
        \Lofmp\CouponCode\Model\Coupon $coupon,
        \Lofmp\CouponCode\Model\Rule $rule,
        \Magento\Customer\Model\Session $customerSession,
        \Magento\Framework\App\ResourceConnection $resource,
        array $data = []
        ) {
        $this->coupon         = $coupon;
        $this->_rule          = $rule;
        $this->_sellerhelper  = $seller_helper;
        $this->_coreRegistry  = $registry;
        $this->_couponFactory = $CouponFactory;
        $this->_resource      = $resource;
        $this->session        = $customerSession;
        parent::__construct($context);
    }

    public function getCouponCollection(){
        $store            = $this->_storeManager->getStore();
        $CouponCollection = $this->_couponFactory->getCollection();
        return $CouponCollection;
    }

    public function getCouponCode() {

        if($this->getCurrentCoupon()) {
            $coupon_id = $this->getCurrentCoupon()->getData('coupon_id');
        } else {
            $coupon_id = $this->getCouponId();
        }
        $coupon = $this->coupon->getCollection()->getCouponCodeByConditions(["seller_id" => $this->_sellerhelper->getSellerId()]);
        return $coupon;
    }

    public function _prepareLayout() {
        return parent::_prepareLayout ();
    }
}