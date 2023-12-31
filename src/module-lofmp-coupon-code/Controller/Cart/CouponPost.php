<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Lofmp\CouponCode\Controller\Cart;

/**
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 */
class CouponPost extends \Magento\Checkout\Controller\Cart
{
    /**
     * Sales quote repository
     *
     * @var \Magento\Quote\Api\CartRepositoryInterface
     */
    protected $quoteRepository;

    /**
     * Coupon factory
     *
     * @var \Magento\SalesRule\Model\CouponFactory
     */
    protected $couponFactory;

    /**
     * @var \Lofmp\CouponCode\Model\CouponFactory
     */
    protected $lofCouponFactory;

    /**
     * @var \Lofmp\CouponCode\Helper\Data $helperData
     */
    protected $helperData;

    /**
     * @param \Magento\Framework\App\Action\Context $context
     * @param \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig
     * @param \Magento\Checkout\Model\Session $checkoutSession
     * @param \Magento\Store\Model\StoreManagerInterface $storeManager
     * @param \Magento\Framework\Data\Form\FormKey\Validator $formKeyValidator
     * @param \Magento\Checkout\Model\Cart $cart
     * @param \Magento\SalesRule\Model\CouponFactory $couponFactory
     * @param \Magento\Quote\Api\CartRepositoryInterface $quoteRepository
     * @param \Lofmp\CouponCode\Model\CouponFactory $lofCouponFactory
     * @param \Lofmp\CouponCode\Helper\Data $helperData
     * @codeCoverageIgnore
     */
    public function __construct(
        \Magento\Framework\App\Action\Context $context,
        \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig,
        \Magento\Checkout\Model\Session $checkoutSession,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        \Magento\Framework\Data\Form\FormKey\Validator $formKeyValidator,
        \Magento\Checkout\Model\Cart $cart,
        \Magento\SalesRule\Model\CouponFactory $couponFactory,
        \Magento\Quote\Api\CartRepositoryInterface $quoteRepository,
        \Lofmp\CouponCode\Model\CouponFactory $lofCouponFactory,
        \Lofmp\CouponCode\Helper\Data $helperData
    ) {
        parent::__construct(
            $context,
            $scopeConfig,
            $checkoutSession,
            $storeManager,
            $formKeyValidator,
            $cart
        );
        $this->lofCouponFactory = $lofCouponFactory;
        $this->couponFactory = $couponFactory;
        $this->quoteRepository = $quoteRepository;
        $this->helperData = $helperData;
    }

    /**
     * Initialize coupon
     *
     * @return \Magento\Framework\Controller\Result\Redirect
     * @SuppressWarnings(PHPMD.CyclomaticComplexity)
     * @SuppressWarnings(PHPMD.NPathComplexity)
     */
    public function execute()
    {
        if ($this->helperData->isEnabled()) {
            $flag = false;
            $escaper = $this->_objectManager->get(\Magento\Framework\Escaper::class);
            $coupon = $this->couponFactory->create();
            $couponCode = ($this->getRequest()->getParam('remove') == 1)? '' : trim($this->getRequest()->getParam('coupon_code'));
            // check login to apply coupon code
            //get infor by couponcode
            $coupon_collection = $this->lofCouponFactory->create()->getCollection();
            $data = $coupon_collection->getByCouponCode($this->getRequest()->getParam('coupon_code'));
            if (count($data) > 0 && $couponCode) {
                $rule = $coupon_collection->getRule($data["rule_id"]);
                if ($rule) {
                    //get checkout info
                    $customer_checkout = $this->_checkoutSession->getQuote()->getCustomer();
                    $customer_email = $customer_checkout->getEmail();
                    $customer_id = $customer_checkout->getId();
                    if ($rule["is_check_email"]) {
                        if((isset($data["email"]) && $data["email"] == $customer_email) || (isset($data["customer_id"]) && $data["customer_id"] == $customer_id))
                            $flag = true;
                    } else{
                        $flag = true;
                    }
                } else {
                    $flag = true;
                }
            } elseif (!$couponCode) {
                $flag = true;
            }
            if ($flag) {
                $cartQuote = $this->cart->getQuote();
                $oldCouponCode = $cartQuote->getCouponCode();

                $codeLength = strlen($couponCode);
                if (!$codeLength && !strlen($oldCouponCode)) {
                    return $this->_goBack();
                }
                try {
                    $isCodeLengthValid = $codeLength && $codeLength <= \Magento\Checkout\Helper\Cart::COUPON_CODE_MAX_LENGTH;

                    $itemsCount = $cartQuote->getItemsCount();
                    if ($itemsCount) {
                        $cartQuote->getShippingAddress()->setCollectShippingRates(true);
                        $cartQuote->setCouponCode($isCodeLengthValid ? $couponCode : '')->collectTotals();
                        $this->quoteRepository->save($cartQuote);
                    }

                    if ($codeLength) {
                        $escaper = $this->_objectManager->get(\Magento\Framework\Escaper::class);
                        $coupon = $this->couponFactory->create();
                        $coupon->load($couponCode, 'code');
                        if (!$itemsCount) {
                            if ($isCodeLengthValid && $coupon->getId()) {
                                $this->_checkoutSession->getQuote()->setCouponCode($couponCode)->save();
                                $this->messageManager->addSuccess(
                                    __(
                                        'You used coupon code "%1".',
                                        $escaper->escapeHtml($couponCode)
                                    )
                                );
                            } else {
                                $this->messageManager->addError(
                                    __(
                                        'The coupon code "%1" is not valid.',
                                        $escaper->escapeHtml($couponCode)
                                    )
                                );
                            }
                        } else {
                            if ($isCodeLengthValid && $coupon->getId() && $couponCode == $cartQuote->getCouponCode()) {
                                $this->messageManager->addSuccess(
                                    __(
                                        'You used coupon code "%1".',
                                        $escaper->escapeHtml($couponCode)
                                    )
                                );
                            } else {
                                $this->messageManager->addError(
                                    __(
                                        'The coupon code "%1" is not valid.',
                                        $escaper->escapeHtml($couponCode)
                                    )
                                );
                            }
                        }
                    } else {
                        $this->messageManager->addSuccess(__('You canceled the coupon code.'));
                    }
                } catch (\Magento\Framework\Exception\LocalizedException $e) {
                    $this->messageManager->addError($e->getMessage());
                } catch (\Exception $e) {
                    $this->messageManager->addError(__('We cannot apply the coupon code.'));
                    $this->_objectManager->get(\Psr\Log\LoggerInterface::class)->critical($e);
                }
            } else{
                $this->messageManager->addError(
                    __('The coupon code "%1" is not valid.',
                    $escaper->escapeHtml($couponCode)
                    )
                );
            }

            return $this->_goBack();
        } else {
            return parent::execute();
        }
    }
}
