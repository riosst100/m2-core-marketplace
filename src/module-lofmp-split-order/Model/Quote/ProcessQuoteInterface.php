<?php declare(strict_types=1);

namespace Lofmp\SplitOrder\Model\Quote;
use Magento\Framework\Exception\LocalizedException;
/**
 * Interface ProcessQuoteInterface
 */
interface ProcessQuoteInterface
{
    /**
     * get payment object
     * @param string|null $payment
     * @return mixed|object|null
     */
    public function getPayment($payment);

    /**
     * set available Payment
     * @param string $payment
     * @param string $paymentClass
     * @return $this
     */
    public function setAvailablePayment($payment, $paymentClass);

    /**
     * set subject object
     * @param \Magento\Quote\Model\QuoteManagement|mixed|object|null $subject
     * @return $this
     */
    public function setSubjectObject($subject);

    /**
     * getCurrentQuote
     *
     * @param int $cartId
     * @param string|mixed $payment
     * @return \Magento\Quote\Api\Data\CartInterface|null
     * @throws LocalizedException
     */
    public function getCurrentQuote($cartId, $payment);

    /**
     * process quote
     *
     * @param int $cartId
     * @param string|mixed $payment
     * @param \Magento\Quote\Api\Data\CartInterface|null $currentQuote
     * @param mixed|null $paymentMethod
     * @return void|mixed
     * @throws LocalizedException
     */
    public function process($cartId, $payment, $currentQuote, $paymentMethod = null);

    /**
     * normalizeQuotes
     *
     * @param int $cartId
     * @param string|mixed $payment
     * @param \Magento\Quote\Api\Data\CartInterface|null $currentQuote
     * @return array|mixed
     * @throws LocalizedException
     */
    public function normalizeQuotes($cartId, $payment, $currentQuote);

    /**
     * check is allow process currrent payment
     * @param int $cartId
     * @param string|null $payment
     * @return bool
     */
    public function checkProcessPayment($cartId, $payment);

    /**
     * check is allow process function before call payment process
     * @param int $cartId
     * @param string|null $payment
     * @return bool
     */
    public function allowProcessBefore($cartId, $payment);

    /**
     * check is allow process arround plugin for current payment
     * @param int $cartId
     * @param string|null $payment
     * @return bool
     */
    public function checkProcessArround($cartId, $payment);

    /**
     * set plugin proceed
     * @param callable|mixed|object|null $proceed
     * @return $this
     */
    public function setProceed($proceed);
}
