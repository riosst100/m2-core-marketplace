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
 * @package    Lofmp_SplitCart
 * @copyright  Copyright (c) 2021 Landofcoder (https://www.landofcoder.com/)
 * @license    https://landofcoder.com/terms
 */

namespace Lofmp\SplitCart\Plugin\Checkout\Controller\Cart;

class Index
{
    /**
     * @var \Magento\Framework\Message\ManagerInterface
     */
    protected $messageManager;

    /**
     * @var \Lofmp\SplitCart\Helper\ConfigData
     */
    private $moduleConfig;

    /**
     * @var \Magento\Checkout\Model\Cart
     */
    private $cart;

    /**
     * @param \Magento\Framework\Message\ManagerInterface $messageManager
     * @param \Lofmp\SplitCart\Helper\ConfigData $configData
     * @param \Magento\Checkout\Model\Cart $cart
     */
    public function __construct(
        \Magento\Framework\Message\ManagerInterface $messageManager,
        \Lofmp\SplitCart\Helper\ConfigData $configData,
        \Magento\Checkout\Model\Cart $cart
    ) {
        $this->messageManager = $messageManager;
        $this->moduleConfig = $configData;
        $this->cart = $cart;
    }

    /**
     * @param \Magento\Checkout\Controller\Cart\Index $subject
     * @param $result
     * @return mixed
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function afterExecute(\Magento\Checkout\Controller\Cart\Index $subject, $result)
    {
        if (!$this->moduleConfig->isEnabled()) {
            return $result;
        }

        $quote = $this->cart->getQuote();
        if (!$quote || !$quote->hasItems()) {
            return $result;
        }

        $this->messageManager->addNoticeMessage(__('Please checkout by a group of items of each seller.'));
        return $result;
    }
}
