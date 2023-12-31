<?php
/**
 * Copyright © 2013-2017 Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Lofmp\CouponCode\Model\Rewrite\Newsletter;
/**
 * Subscriber model
 *
 * @method \Magento\Newsletter\Model\ResourceModel\Subscriber _getResource()
 * @method \Magento\Newsletter\Model\ResourceModel\Subscriber getResource()
 * @method int getStoreId()
 * @method $this setStoreId(int $value)
 * @method string getChangeStatusAt()
 * @method $this setChangeStatusAt(string $value)
 * @method int getCustomerId()
 * @method $this setCustomerId(int $value)
 * @method string getSubscriberEmail()
 * @method $this setSubscriberEmail(string $value)
 * @method int getSubscriberStatus()
 * @method $this setSubscriberStatus(int $value)
 * @method string getSubscriberConfirmCode()
 * @method $this setSubscriberConfirmCode(string $value)
 * @method int getSubscriberId()
 * @method Subscriber setSubscriberId(int $value)
 *
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 * @SuppressWarnings(PHPMD.CyclomaticComplexity)
 */
class Subscriber extends \Magento\Newsletter\Model\Subscriber
{
    /**
     * @var bool
     */
    protected $addedFlag = false;

    /**
     * @var \Lofmp\CouponCode\Helper\Generator|null
     */
    protected $_generatorCoupon = null;

    /**
     * init coupon generator
     *
     * @return $this
     */
    public function initCouponGenerator()
    {
        if (!$this->addedFlag) {
            $this->_generatorCoupon = $this->getData("generatorCoupon");
            if (!$this->_generatorCoupon && isset($this->_data['generatorCoupon'])) {
                $this->_generatorCoupon = $this->_data['generatorCoupon'];
            }
            if (!$this->_generatorCoupon) {
                $objectManager = \Magento\Framework\App\ObjectManager::getInstance();
                $this->_generatorCoupon = $objectManager->create('\Lofmp\CouponCode\Helper\Generator');
            }
            if ($this->_generatorCoupon) {
                $this->_generatorCoupon->initRequireModels();
                $customer_email = $this->getSubscriberEmail();
                $customer_name = $this->getSubscriberFullName();

                if ($customer_email) {
                    /** @var \Magento\Customer\Api\Data\CustomerInterface $custome */
                    try {
                        $customer = $this->customerRepository->get($customer_email);
                        if($customer_id = $customer->getId()) {
                            $this->_generatorCoupon->setCustomerId($customer_id);
                        }
                    } catch (\Exception $e) {
                        //Do nothing
                    }
                    $this->_generatorCoupon->setCustomerEmail($customer_email);
                    $this->_generatorCoupon->setCustomerName($customer_name);
                    $this->addedFlag = true;
                }
            }
        }
        return $this;
    }

    /**
     * get couponcode by alias
     *
     * @param string $coupon_alias
     */
    public function getCouponCode($coupon_alias)
    {
        $this->initCouponGenerator();
        if ($this->addedFlag && $coupon_alias) {
            return $this->_generatorCoupon->getCouponCode($coupon_alias);
        }
        return "";
    }

    /**
     * get coupon expiration date
     *
     * @param string $coupon_alias
     * @return string
     */
    public function getCouponExpirationDate($coupon_alias)
    {
        $this->initCouponGenerator();
        if ($this->addedFlag && $coupon_alias) {
            return $this->_generatorCoupon->getCouponExpirationDate($coupon_alias);
        }
        return "";
    }

    /**
     * get coupon discount
     *
     * @param string $coupon_alias
     * @return string|int|float
     */
    public function getCouponDiscount($coupon_alias)
    {
        $this->initCouponGenerator();
        if ($this->addedFlag && $coupon_alias) {
            return $this->_generatorCoupon->getCouponDiscount($coupon_alias);
        }
        return "";
    }

    /**
     * get uses per coupon
     *
     * @param string $coupon_alias
     * @return string|int
     */
    public function getUsesPerCoupon($coupon_alias)
    {
        $this->initCouponGenerator();
        if ($this->addedFlag && $coupon_alias) {
            return $this->_generatorCoupon->getUsesPerCoupon($coupon_alias);
        }
        return "";
    }

    /**
     * generate coupon
     *
     * @param int $ruleId
     * @param string $coupon_alias
     * @return string
     */
    public function generateCoupon($ruleId, $coupon_alias = "")
    {
        $this->initCouponGenerator();
        if ($this->addedFlag && $ruleId) {
            return $this->_generatorCoupon->generateCoupon($ruleId, $coupon_alias);
        }
        return "";
    }
}
