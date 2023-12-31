<?php declare(strict_types=1);

namespace Lofmp\SplitOrder\Model\Quote\Payment;

/**
 * Class RazorpayPayment
 */
class RazorpayPayment extends AbstractProcessPayment
{
    /**
     * @var string
     */
    protected $paymentType = "razorpay";

    /**
     * @var string
     */
    const FAKE_PAYMENT_METHOD = 'marketplace_razorpay';

    /**
     * @var string
     */
    const MAIN_ORDER_FIELD = 'rp_is_main_order';

    /**
     * @var string
     */
    const PARENT_ORDER_FIELD = 'rp_parent_order_id';

    /**
     * @inheritdoc
     */
    public function process($currentQuote, $cartId = 0, $payment = null)
    {
        if ($cartId) {
            $this->setCartId($cartId);
        }
        return [];
    }

    /**
     * @inheritdoc
     */
    public function checkProcessPayment($cartId = 0)
    {
        parent::checkProcessPayment($cartId);
        return true;
    }

    /**
     * @inheritdoc
     */
    public function allowProcessBefore($cartId = 0)
    {
        parent::allowProcessBefore($cartId);
        return true;
    }

    /**
     * @inheritdoc
     */
    public function checkProcessArroundPayment($cartId = 0)
    {
        if ($cartId) {
            $this->setCartId($cartId);
        }
        return false;
    }
}
