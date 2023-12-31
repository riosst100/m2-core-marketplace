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

namespace Lofmp\SplitCart\Plugin\Checkout\Controller\Index;

class Index
{
    /**
     * @var \Magento\Checkout\Model\Session
     */
    private $_checkoutSession;

    /**
     * @var \Lofmp\SplitCart\Helper\ConfigData
     */
    private $moduleConfig;

    /**
     * @var \Magento\Framework\Controller\Result\RedirectFactory
     */
    private $resultRedirectFactory;

    /**
     * @param \Lofmp\SplitCart\Helper\ConfigData $moduleConfig
     * @param \Magento\Framework\Controller\Result\RedirectFactory $redirectFactory
     * @param \Magento\Checkout\Model\Session $checkoutSession
     */
    public function __construct(
        \Lofmp\SplitCart\Helper\ConfigData $moduleConfig,
        \Magento\Framework\Controller\Result\RedirectFactory $redirectFactory,
        \Magento\Checkout\Model\Session $checkoutSession
    ) {
        $this->_checkoutSession = $checkoutSession;
        $this->moduleConfig = $moduleConfig;
        $this->resultRedirectFactory = $redirectFactory;
    }

    /**
     * @param \Magento\Checkout\Controller\Index\Index $subject
     * @param $result
     * @return \Magento\Framework\Controller\Result\Redirect|mixed
     * @throws \Magento\Framework\Exception\LocalizedException
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function afterExecute(\Magento\Checkout\Controller\Index\Index $subject, $result)
    {
        if (!$this->moduleConfig->isEnabled()) {
            return $result;
        }

        $quote = $this->_checkoutSession->getQuote();

        if (!$quote || !$quote->hasItems()) {
            return $result;
        }

        $items = $quote->getAllItems();
        $sellerIds = [];
        foreach ($items as $item) {
            $sellerIdItem = $item->getLofSellerId() ? $item->getLofSellerId() : $item->getSellerId();
            $sellerIds[] =  $sellerIdItem ?: 0;
        }
        $sellerIds = array_unique($sellerIds);
        if (count($sellerIds) > 1) {
            return $this->resultRedirectFactory->create()->setPath('checkout/cart');
        }

        return $result;
    }
}
