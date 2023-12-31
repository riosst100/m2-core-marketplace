<?php
/**
 * Landofcoder
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Landofcoder.com license that is
 * available through the world-wide-web at this URL:
 * https://landofcoder.com/terms
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade this extension to newer
 * version in the future.
 *
 * @category   Landofcoder
 * @package    Lofmp_SplitOrder
 * @copyright  Copyright (c) 2021 Landofcoder (https://www.landofcoder.com/)
 * @license    https://landofcoder.com/terms
 */

namespace Lofmp\SplitOrder\Plugin;

use Magento\Quote\Model\QuoteManagement;
use Magento\Framework\Exception\LocalizedException;
use Lofmp\SplitOrder\Model\Quote\ProcessQuoteFactory;
use Magento\Customer\Api\Data\GroupInterface;

class SplitQuote
{
    /**
     * @var ProcessQuoteFactory
     */
    protected $processQuoteFactory;

    /**
     * @param ProcessQuoteFactory $processQuoteFactory
     */
    public function __construct(
        ProcessQuoteFactory $processQuoteFactory
    ) {
        $this->processQuoteFactory = $processQuoteFactory;
    }

    /**
     * Places an order for a specified cart.
     *
     * @param QuoteManagement $subject
     * @param callable $proceed
     * @param int $cartId
     * @param string|null $payment
     * @return mixed
     * @throws LocalizedException
     * @throws \Exception
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     * @see \Magento\Quote\Api\CartManagementInterface
     */
    public function aroundPlaceOrder(QuoteManagement $subject, callable $proceed, $cartId, $payment = null)
    {
        try {
            // Separate all items in quote into new quotes.
            $processQuote = $this->processQuoteFactory->create();
            $processQuote->setSubjectObject($subject);
            $processQuote->setProceed($proceed);
            $currentQuote = $processQuote->getCurrentQuote($cartId, $payment);
            if (!is_object($currentQuote)) {
                return $proceed($cartId, $payment);
            }
            $paymentMethodName = $currentQuote->getPayment()->getMethod();
            if ($processQuote->checkProcessArround($cartId, $paymentMethodName)) {
                //$currentQuote = $processQuote->getCurrentQuote($cartId, $payment);
                if ($currentQuote->getCheckoutMethod() === \Magento\Quote\Api\CartManagementInterface::METHOD_GUEST) {
                    $currentQuote->setCustomerId(null);
                    $currentQuote->setCustomerEmail($currentQuote->getBillingAddress()->getEmail());
                    if ($currentQuote->getCustomerFirstname() === null && $currentQuote->getCustomerLastname() === null) {
                        $currentQuote->setCustomerFirstname($currentQuote->getBillingAddress()->getFirstname());
                        $currentQuote->setCustomerLastname($currentQuote->getBillingAddress()->getLastname());
                        if ($currentQuote->getBillingAddress()->getMiddlename() === null) {
                            $currentQuote->setCustomerMiddlename($currentQuote->getBillingAddress()->getMiddlename());
                        }
                    }
                    $currentQuote->setCustomerIsGuest(true);
                    $groupId = $currentQuote->getCustomer()->getGroupId() ?: GroupInterface::NOT_LOGGED_IN_ID;
                    $currentQuote->setCustomerGroupId($groupId);
                }

                /** Normalize Quotes */
                $quotes = $processQuote->normalizeQuotes($cartId, $paymentMethodName, $currentQuote);
                if (empty($quotes) || count($quotes) < 1) {
                    return $proceed($cartId, $payment);
                }
                /** Check allow process payment or not */
                $checkProcessPayment = $processQuote->checkProcessPayment($cartId, $paymentMethodName);
                if (!$checkProcessPayment) {
                    return $proceed($cartId, $payment);
                }
                /** Check allow run process quote before */
                $allowProcessBefore = $processQuote->allowProcessBefore($cartId, $paymentMethodName);
                if ($allowProcessBefore) {
                   $proceed($cartId, $payment);
                }
                $processQuote->setProceed($proceed);
                $processQuote->setSubjectObject($subject);
                /** Process split quotes */
                return $processQuote->process($cartId, $paymentMethodName, $currentQuote, $payment);
            } else {
                return $proceed($cartId, $payment);
            }
        } catch (\Exception $e) {
            throw $e;
        }
    }
}
