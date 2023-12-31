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
namespace Lofmp\CouponCode\Controller\Marketplace\Generate;

use Magento\Framework\App\Action\Context;
use Magento\Store\Model\StoreManagerInterface;
use Lofmp\CouponCode\Helper\Data;

class Generate extends \Magento\Customer\Controller\AbstractAccount {

    const FLAG_IS_URLS_CHECKED = 'check_url_settings';
    /**
     * Seller state const
     */
    const STATE_NOT_LOGGED_IN = "not_loggin";
    const STATE_APPROVED = "approved";
    const STATE_NEED_APPROVAL = "need_approval";

    /**
     * @var \Magento\Customer\Model\Session
     */
    protected $customerSession;

    /**
     * @var \Magento\Framework\View\Result\PageFactory
     */
    protected $resultPageFactory;

    /**
     * @var \Lof\MarketPlace\Model\SellerFactory
     */
    protected $sellerFactory;

    /**
     * @var \Magento\Framework\Url
     */
    protected $_frontendUrl;

    /**
     * @var mixed|string|Object
     */
    protected $_actionFlag;

    /**
     * @var \Lofmp\CouponCode\Helper\Data
     */
    protected $helperData;

    /**
     * @var \Lofmp\CouponCode\Helper\Generator
     */
    protected $couponGenerator;

    /**
     * @var \Magento\Customer\Model\CustomerFactory
     */
    protected $customerFactory;

    /**
     * @var \Lofmp\CouponCode\Model\CouponFactory
     */
    protected $couponFactory;

    protected $currentSeller = null;

    /**
     * construct generate action
     *
     * @param Context $context
     * @param \Magento\Customer\Model\Session $customerSession
     * @param \Lof\MarketPlace\Model\SellerFactory $sellerFactory
     * @param \Magento\Framework\Url $frontendUrl
     * @param \Magento\Framework\View\Result\PageFactory $resultPageFactory
     * @param \Magento\Customer\Model\CustomerFactory $customerFactory
     * @param \Lofmp\CouponCode\Helper\Data $helper
     * @param \Lofmp\CouponCode\Helper\Generator $generateHelper
     * @param \Lofmp\CouponCode\Model\CouponFactory $couponFactory
     */
    public function __construct(
        Context $context,
        \Magento\Customer\Model\Session $customerSession,
        \Lof\MarketPlace\Model\SellerFactory $sellerFactory,
        \Magento\Framework\Url $frontendUrl,
        \Magento\Framework\View\Result\PageFactory $resultPageFactory,
        \Magento\Customer\Model\CustomerFactory $customerFactory,
        \Lofmp\CouponCode\Helper\Data $helper,
        \Lofmp\CouponCode\Helper\Generator $generateHelper,
        \Lofmp\CouponCode\Model\CouponFactory $couponFactory
    ) {
        parent::__construct ($context);

        $this->_frontendUrl = $frontendUrl;
        $this->_actionFlag = $context->getActionFlag();
        $this->sellerFactory     = $sellerFactory;
        $this->customerSession           = $customerSession;
        $this->resultPageFactory = $resultPageFactory;
        $this->helperData       = $helper;
        $this->couponGenerator     = $generateHelper;
        $this->customerFactory = $customerFactory;
        $this->couponFactory = $couponFactory;
    }

    /**
     * Get frontend url
     *
     * @param string $route
     * @param array|mixed $params
     * @return string
     */
    public function getFrontendUrl($route = '', $params = [])
    {
        return $this->_frontendUrl->getUrl($route,$params);
    }

     /**
     * Retrieve customer session object.
     *
     * @return Session
     */
    protected function _getSession()
    {
        return $this->customerSession;
    }

    /**
     * Redirect url
     *
     * @param $url
     * @return Object|mixed
     */
    protected function _redirectUrl($url)
    {
        $this->getResponse()->setRedirect($url);
        $this->customerSession->setIsUrlNotice($this->_actionFlag->get('', self::FLAG_IS_URLS_CHECKED));
        return $this->getResponse();
    }

    /**
     * get current seller
     *
     * @return \Lof\MarketPlace\Model\Seller|null
     */
    protected function getCurrentSeller()
    {
        if (!$this->currentSeller && $this->_getSession()->isLoggedIn()) {
            $customerId = $this->_getSession()->getCustomerId();
            $this->currentSeller = $this->sellerFactory->create()->load($customerId, 'customer_id');
        }
        return $this->currentSeller;
    }

    /**
     * get seller state
     *
     * @return string|int
     */
    protected function getSellerState()
    {
        $seller = $this->getCurrentSeller();
        if ($seller && $seller->getId()) {
            return 1 == $seller->getStatus() ? self::STATE_APPROVED:self::STATE_NEED_APPROVAL;
        }
        return self::STATE_NOT_LOGGED_IN;
    }

    /**
     * Check is active seller
     *
     * @param bool $checkPermission
     * @return bool
     */
    public function isActiveSeller($checkPermission = false)
    {
        if ($checkPermission && $this->helperData) {
            if (!$this->helperData->allowSellerManage()) {
                $this->messageManager->addErrorMessage('You dont have permission to access the feature.');
                $this->_redirectUrl($this->getFrontendUrl('marketplace/catalog/dashboard'));
                return false;
            }
        }
        $sellerState = $this->getSellerState();
        switch ($sellerState) {
            case "approved":
                return true;
                break;
            case "not_loggin":
                $this->messageManager->addNotice(__('You must have a seller account to access'));
                $this->_redirectUrl($this->getFrontendUrl('lofmarketplace/seller/login'));
                return false;
                break;
            case "need_approval":
            default:
                $this->_redirectUrl($this->getFrontendUrl('lofmarketplace/seller/becomeseller'));
                return false;
                break;
        }
    }

    /**
     * {@inheritdoc}
     */
    public function execute()
    {
        $isActived = $this->isActiveSeller(true);
        if ($isActived) {
            $seller = $this->getCurrentSeller();
            $coupon_code = '';
            $seller_id = $seller->getId();
            //$isAjax = $this->getRequest()->getParam('isAjax');
            $data = $this->getRequest()->getParams();

            if (!$data) {
                $customer_email = $this->getRequest()->getParam('email_visitor');
                $rule_id = (int)$this->getRequest()->getParam('coupon_rule_id');
            } else {
                $customer_email = isset($data['email_visitor'])?$data['email_visitor']:'';
                $rule_id = isset($data['coupon_rule_id'])?(int)$data['coupon_rule_id']:'';
            }

            if ($rule_id) {
                $couponRuleData = $this->helperData->getCouponRuleData($rule_id);
                $ruleId = (int)$couponRuleData->getRuleId();
                $is_check_email = $couponRuleData ? (int)$couponRuleData->getIsCheckEmail() : 0;
                $isValidEmail = $this->isValidEmailAddress($is_check_email, $customer_email);

                if ($ruleId && $seller_id == $couponRuleData->getSellerId() && $isValidEmail) {
                    $limit_time_generated_coupon = (int)$couponRuleData->getLimitGenerated();
                    $coupon_collection = $this->couponFactory->create()->getCollection();
                    $number_generated_coupon = $customer_email?(int)$coupon_collection->getTotalByEmail($customer_email, $rule_id):0;
                    if ($limit_time_generated_coupon <= 0 || ($number_generated_coupon < $limit_time_generated_coupon)) { //check number coupons was generated for same email address
                        $coupon_alias = Data::REDEEM_PREFIX.md5($rule_id).rand().time();
                        $customerId = 0;
                        if ($customer_email) {
                            $coupon_alias = Data::REDEEM_PREFIX.md5($customer_email).rand().time();
                            $this->couponGenerator->setCustomerEmail($customer_email);
                            if ($customerId = $this->getCustomerByEmail($customer_email)) {
                                $this->couponGenerator->setCustomerId($customerId);
                            }
                        }

                        $isPublic = (!$is_check_email && !$customerId) ? true : false;
                        $this->couponGenerator->setSellerId($seller_id);
                        $this->couponGenerator->setCouponAlias($coupon_alias);
                        $this->couponGenerator->setIsPublic($isPublic);

                        $coupon_exists = false;
                        $coupon_model = $this->couponFactory->create()->getCouponByAlias($coupon_alias);
                        if ($coupon_model->getId()) {
                            $coupon_exists = true;
                        }
                        if (!$coupon_exists) {
                            $coupon_code = $this->couponGenerator->generateCoupon($rule_id);
                            $this->messageManager->addSuccess(__('The coupon code "%1" has been generated.', $coupon_code));
                        }
                    }
                } else {
                    $this->messageManager->addError(__('The rule is not exists, or the rule require check email address or not available for your account.'));
                    $this->_redirect ('lofmpcouponcode/generate/index' );
                    return;
                }
            } else {
                $this->messageManager->addError(__('Something went wrong while saving the coupon. Missing Rule ID or Email address.'));
                $this->_redirect ('lofmpcouponcode/generate/index' );
                return;
            }
            $this->_redirect ('lofmpcouponcode/coupon/index' );
        }
    }

    /**
     * Check email address is valid
     *
     * @param int $is_check_email
     * @param string $customer_email
     * @return bool|int
     */
    protected function isValidEmailAddress($is_check_email, $customer_email = "")
    {
        $validEmail = false;
        if ($customer_email) {
            $validEmail = $this->helperData->validateEmailAddress($customer_email);
        }
        return (!$is_check_email || ($is_check_email && $validEmail)) ? true : false;
    }

    /**
     * Get customer by email
     *
     * @param string $email
     * @return int|string|null
     */
    public function getCustomerByEmail($email)
    {
        $customerFactory = $this->customerFactory->create();
        $customerData    = $customerFactory->getCollection()->addFieldToFilter("email", $email)->getFirstItem();
        if ($customerData) {
            return $customerData->getId();
        }
        return null;
    }
}
