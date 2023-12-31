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

namespace Lofmp\CouponCode\Controller\Adminhtml\Generate;

class Generate extends \Lofmp\CouponCode\Controller\Adminhtml\Generate
{
    /**
     * @var string
     */
    CONST EMAILIDENTIFIER = 'sent_mail_with_customer';

    /**
     * @var \Lofmp\CouponCode\Helper\Data
     */
    protected $helperData;

    /**
     * @var \Magento\SalesRule\Model\CouponFactory
     */
    protected $couponFactory;

    /**
     * @var \Magento\Framework\Stdlib\DateTime
     */
    protected $dateTime;

    /**
     * @var \Magento\Framework\Stdlib\DateTime\DateTime
     */
    protected $date;

    /**
     * @var \Magento\Customer\Helper\View
     */
    protected $customerHelper;

    /**
     * @var \Lofmp\CouponCode\Model\CouponFactory
     */
    protected $lofCouponFactory;

    /**
     * @var \Magento\Framework\UrlInterface
     */
    protected $urlInterface;

    /**
     * @var \Magento\Store\Model\StoreManagerInterface
     */
    protected $storeManager;

    /**
     * construct Generate Coupon
     *
     * @param \Magento\Backend\App\Action\Context $context
     * @param \Magento\Framework\Registry $coreRegistry
     * @param \Lofmp\CouponCode\Helper\Data $helper
     * @param \Magento\SalesRule\Model\CouponFactory $couponFactory
     * @param \Magento\Framework\Stdlib\DateTime $dateTime
     * @param \Magento\Framework\Stdlib\DateTime\DateTime $date
     * @param \Magento\Customer\Helper\View $customerHelper
     * @param \Lofmp\CouponCode\Model\CouponFactory $lofCouponFactory
     * @param \Magento\Framework\UrlInterface $urlInterface
     * @param \Magento\Store\Model\StoreManagerInterface $storeManager
     * @return void
     */
    public function __construct(
        \Magento\Backend\App\Action\Context $context,
        \Magento\Framework\Registry $coreRegistry,
        \Lofmp\CouponCode\Helper\Data $helper,
        \Magento\SalesRule\Model\CouponFactory $couponFactory,
        \Magento\Framework\Stdlib\DateTime $dateTime,
        \Magento\Framework\Stdlib\DateTime\DateTime $date,
        \Magento\Customer\Helper\View $customerHelper,
        \Lofmp\CouponCode\Model\CouponFactory $lofCouponFactory,
        \Magento\Framework\UrlInterface $urlInterface,
        \Magento\Store\Model\StoreManagerInterface $storeManager
    )
    {
        $this->helperData = $helper;
        $this->couponFactory = $couponFactory;
        $this->dateTime = $dateTime;
        $this->date = $date;
        $this->customerHelper = $customerHelper;
        $this->lofCouponFactory = $lofCouponFactory;
        $this->urlInterface = $urlInterface;
        $this->storeManager   = $storeManager;
        parent::__construct($context, $coreRegistry);
    }

    /**
     * @inheritdoc
     */
    public function execute()
    {
        // $data = $this->getRequest()->getPostValue();
        $requestData = $this->_objectManager->get(
            'Magento\Backend\Helper\Data'
        )->prepareFilterString(
            $this->getRequest()->getParam('filter')
        );

        /** @var \Magento\Backend\Model\View\Result\Redirect $resultRedirect */
        $resultRedirect = $this->resultRedirectFactory->create();
        if ($requestData && isset($requestData['coupon_rule_id'])) {
            try {
                /** @var $model \Magento\SalesRule\Model\Rule */
                // $ruleModel = $this->_objectManager->create('Lofmp\CouponCode\Model\Rule');
                // $couponModel = $this->_objectManager->create('Lofmp\CouponCode\Model\Coupon');
                $couponRuleId = $requestData['coupon_rule_id'];
                $couponRuleData = $this->helperData->getCouponRuleData($couponRuleId);
                $ruleId = (int)$couponRuleData->getRuleId();
                if (!$couponRuleData->getSellerId() || $couponRuleData->getSellerId() == null){
                    $this->messageManager->addError(
                        __('There are not any assigned sellers with this rule.')
                    );
                    $this->_redirect('*/*/');
                    return;
                } else{
                    $sellerId = (int)$couponRuleData->getSellerId();
                }
                $is_check_email = $couponRuleData ? (int)$couponRuleData->getIsCheckEmail() : 0;
                if($ruleId) {
                    $limit_time_generated_coupon = (int)$couponRuleData->getLimitGenerated();
                    $emailVisitor = isset($requestData["email_visitor"]) ? $requestData["email_visitor"] : "";
                    if ($is_check_email) {
                        $coupon_collection = $this->_objectManager->create('Lofmp\CouponCode\Model\Coupon')->getCollection();
                        $number_generated_coupon = (int)$coupon_collection->getTotalByEmail($emailVisitor, $ruleId);
                    } else {
                        $number_generated_coupon = 0;
                    }

                    $isValidEmail = $this->isValidEmailAddress($is_check_email, $emailVisitor);

                    /** check number coupons was generated for same email address */
                    if($isValidEmail && ($limit_time_generated_coupon <= 0 || ($number_generated_coupon < $limit_time_generated_coupon))) {
                        $coupon = $this->couponFactory->create();
                        $emailFrom = $this->helperData->getConfig('general_settings/sender_email_identity');
                        $emailidentifier = $this->helperData->getConfig("general_settings/email_template");
                        $emailidentifier = $emailidentifier ? $emailidentifier : self::EMAILIDENTIFIER;

                        $nowTimestamp = $this->dateTime->formatDate($this->date->gmtTimestamp());
                        $expirationDate = $couponRuleData->getToDate();
                        if ($expirationDate && !($expirationDate instanceof \DateTime)) {
                            $expirationDate = \DateTime::createFromFormat('Y-m-d', $expirationDate);
                        }
                        if($expirationDate instanceof \DateTime) {
                            $expirationDate = $expirationDate->format('Y-m-d H:i:s');
                        }
                        $coupon_code = $this->helperData->generateCode($couponRuleId);

                        $coupon->setId(null)
                            ->setRuleId($ruleId)
                            ->setExpriationDate($expirationDate)
                            ->setCreatedAt($nowTimestamp)
                            ->setType(1)
                            ->setCode($coupon_code)
                            ->save();
                        if ($coupon->getId()) {
                            $_lofCoupon = $this->lofCouponFactory->create();
                            $_lofCoupon->setRuleId($ruleId)
                                ->setCouponId($coupon->getId())
                                ->setCode($coupon_code)
                                ->setEmail($emailVisitor)
                                ->setSellerId((int)$sellerId)
                                ->save();

                            $simple_action = $couponRuleData->getSimpleAction();
                            $discount_amount_formatted = $couponRuleData->getDiscountAmount();
                            if($simple_action == 'by_percent') {
                                $discount_amount_formatted .='%';
                            }elseif($simple_action == 'fixed'){
                                $discount_amount_formatted ='$'.$discount_amount_formatted;
                            }

                            $templateVar = array(
                                'coupon_code' => $coupon_code,
                                'rule_title' => $couponRuleData->getName(),
                                'from_date' => $couponRuleData->getFromDate(),
                                'to_date' => $couponRuleData->getToDate(),
                                'simple_action' => $couponRuleData->getSimpleAction(),
                                'discount_amount' => $couponRuleData->getDiscountAmount(),
                                'discount_amount_formatted' => $discount_amount_formatted,
                                'link_website' => $this->storeManager->getStore()->getBaseUrl()
                            );

                            $couponsGeneratedOld =$couponRuleData->getCouponsGenerated();
                            $couponGenerateNew = $couponsGeneratedOld + 1;
                            $couponRuleData->setData('coupons_generated', $couponGenerateNew)->save();

                            $allow_send_email = $this->helperData->getConfig('general_settings/send_email_coupon');
                            if($allow_send_email && $emailVisitor && $isValidEmail) {
                                $this->helperData->sendMail($emailFrom, $emailVisitor, $emailidentifier,$templateVar);
                                $this->messageManager->addSuccess(__('A coupon code has been sent to %1.', $emailVisitor));
                            } else {
                                $this->messageManager->addSuccess(__('A coupon code has been generated.'));
                            }
                        } else {
                            $this->messageManager->addError(
                             __('Something went wrong while send coupon code. Please review the error log.')
                             );
                        }
                    } else {
                        $this->messageManager->addError(
                             __('The rule limit number coupons were generated for email %1.', $emailVisitor)
                             );
                    }
                } else {
                    $this->messageManager->addError(
                         __('Did not found the coupon rule.')
                         );
                }
                $this->_redirect('*/*/');
                return;

            } catch (\Magento\Framework\Exception\LocalizedException $e) {
                $this->messageManager->addError($e->getMessage());
                $this->_redirect('*/*/');
                return;
            } catch (\Exception $e) {
                $this->messageManager->addError(
                 __('Something went wrong while send coupon code. Please review the error log.')
                 );
                $this->_objectManager->get('Psr\Log\LoggerInterface')->critical($e);
                $this->_objectManager->get('Magento\Backend\Model\Session')->setPageData($requestData);
                $this->_redirect('*/*/');
            }
        }else {
            $this->messageManager->addError(
                __('Please choose a coupon rule to generate.')
                );
        }
        return $resultRedirect->setPath('*/*/');
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
}
