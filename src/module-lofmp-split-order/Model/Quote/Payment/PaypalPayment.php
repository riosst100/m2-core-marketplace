<?php declare(strict_types=1);

namespace Lofmp\SplitOrder\Model\Quote\Payment;

/**
 * Class PaypalPayment
 */
class PaypalPayment extends AbstractProcessPayment
{
    /**
     * @var string
     */
    protected $paymentType = "paypal";

}
