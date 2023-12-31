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
 * @package    Lofmp_SplitOrderPaypal
 * @copyright  Copyright (c) 2021 Landofcoder (https://www.landofcoder.com/)
 * @license    https://landofcoder.com/terms
 */

namespace Lofmp\SplitOrderPaypal\Helper;

use Magento\Framework\App\Helper\AbstractHelper;
use Magento\Framework\App\Helper\Context;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Sales\Model\Order;
use Magento\Sales\Model\OrderFactory;
use Magento\Store\Model\ScopeInterface;

class Data extends AbstractHelper
{
    /**
     * @var OrderFactory
     */
    protected $orderFactory;

    /**
     * @param Context $context
     * @param OrderFactory $orderFactory
     */
    public function __construct(
        Context $context,
        OrderFactory $orderFactory
    ) {
        parent::__construct($context);
        $this->orderFactory = $orderFactory;
    }

    /**
     * @param  $orderId
     * @return Order
     */
    public function getPaypalMainOrder($orderId)
    {
        try {
            return $this->orderFactory->create()->load($orderId);
        } catch (NoSuchEntityException $exception) {
            return  null;
        }
    }

    /**
     * @param  $quoteId
     * @return Order
     */
    public function getPaypalMainOrderByQuoteId($quoteId)
    {
        try {
            return $this->orderFactory->create()->load($quoteId, 'quote_id');
        } catch (NoSuchEntityException $exception) {
            return  null;
        }
    }

    /**
     * Is hide main order?
     *
     * @param int $storeId
     * @return bool
     */
    public function isHideMainOrder($storeId = null)
    {
        return $this->scopeConfig->isSetFlag(
            'lofmp_split_order/options/hide_main_order',
            ScopeInterface::SCOPE_STORE,
            $storeId
        );
    }
}
