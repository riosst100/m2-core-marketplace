<?php declare(strict_types=1);

namespace Lofmp\SplitOrder\Model\Quote\Payment;
use Magento\Framework\Exception\LocalizedException;
/**
 * Interface ProcessPaymentInterface
 */
interface ProcessPaymentInterface
{
    /**
     * set cart id
     * @param int $cartId
     * @return $this
     */
    public function setCartId($cartId);

    /**
     * set payment
     * @param string $payment
     * @return $this
     */
    public function setPayment($payment);

    /**
     * set subject object
     * @param \Magento\Quote\Model\QuoteManagement|mixed|object|null $subject
     * @return $this
     */
    public function setSubjectObject($subject);

    /**
     * get subject object
     * @return \Magento\Quote\Model\QuoteManagement|mixed|object||null
     */
    public function getSubjectObject();

    /**
     * get current quote
     * @param int $cartId
     * @return \Magento\Quote\Api\Data\CartInterface|null
     */
    public function getCurrentQuote($cartId = 0);


    /**
     * set current quote
     * @param \Magento\Quote\Api\Data\CartInterface|null $currentQuote
     * @param int $cartId
     * @return $this
     */
    public function setCurrentQuote($currentQuote, $cartId = 0);

    /**
     * process current cart
     * @param \Magento\Quote\Api\Data\CartInterface|null $currentQuote
     * @param int $cartId
     * @param string|null $payment
     * @return void|mixed
     * @throws LocalizedException
     */
    public function process($currentQuote, $cartId = 0, $payment = null);

    /**
     * normalizeQuotes
     * @param \Magento\Quote\Api\Data\CartInterface|null $currentQuote
     * @param int $cartId
     * @param string|null $payment
     * @return array|mixed
     * @throws LocalizedException
     */
    public function normalizeQuotes($currentQuote, $cartId = 0, $payment = null);

    /**
     * check is allow process currrent payment
     * @param int $cartId
     * @return bool
     */
    public function checkProcessPayment($cartId = 0);

     /**
     * check is allow process arround plugin for currrent payment
     * @param int $cartId
     * @return bool
     */
    public function checkProcessArroundPayment($cartId = 0);

    /**
     * check is allow process function before call payment process
     * @param int $cartId
     * @return bool
     */
    public function allowProcessBefore($cartId = 0);

    /**
     * get quote address Data
     * @return array
     */
    public function getQuoteAddressData();

    /**
     * set quote address Data
     * @param array $addresses
     * @return $this
     */
    public function setQuoteAddressData($addresses);

    /**
     * set quote Data
     * @param array $quotes
     * @param int $cartId
     * @return $this
     */
    public function setQuotes($quotes, $cartId = 0);

    /**
     * set plugin proceed
     * @param callable|mixed|object|null $proceed
     * @return $this
     */
    public function setProceed($proceed);

    /**
     * get plugin proceed
     * @return callable|mixed|object|null
     */
    public function getProceed();
}
