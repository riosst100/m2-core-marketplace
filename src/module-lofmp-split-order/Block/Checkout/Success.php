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

namespace Lofmp\SplitOrder\Block\Checkout;

use Magento\Framework\View\Element\Template\Context;
use Magento\Checkout\Model\Session;
use Magento\Sales\Model\Order\Config;
use Magento\Framework\App\Http\Context as HttpContext;

class Success extends \Magento\Checkout\Block\Onepage\Success
{
    /**
     * @var Session
     */
    private $checkoutSession;

    /**
     * @param Context $context
     * @param Session $checkoutSession
     * @param Config $orderConfig
     * @param HttpContext $httpContext
     * @param array $data
     */
    public function __construct(
        Context $context,
        Session $checkoutSession,
        Config $orderConfig,
        HttpContext $httpContext,
        array $data = []
    ) {
        parent::__construct(
            $context,
            $checkoutSession,
            $orderConfig,
            $httpContext,
            $data
        );
        $this->checkoutSession = $checkoutSession;
    }

    /**
     * @return bool|array
     */
    public function getOrderArray()
    {
        $splitOrders = $this->checkoutSession->getOrderIds();
        $this->checkoutSession->unsOrderIds();

        if (empty($splitOrders)) {
            return false;
        }
        return $splitOrders;
    }
}
