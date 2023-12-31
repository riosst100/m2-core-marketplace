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

class UpdatePost
{
    /**
     * @var \Lofmp\SplitCart\Helper\ConfigData
     */
    private $moduleConfig;

    /**
     * @var \Magento\Framework\App\Request\Http
     */
    protected $_request;

    /**
     * @var \Magento\Checkout\Model\Cart
     */
    protected $_cart;

    /**
     * @var \Lof\MarketPlace\Model\SellerFactory
     */
    protected $sellerFactory;

    /**
     * @var \Magento\Framework\ObjectManagerInterface
     */
    protected $_objectManager;

    /**
     * @var \Magento\Framework\Message\ManagerInterface
     */
    protected $_messageManager;

    /**
     * @param \Lofmp\SplitCart\Helper\ConfigData $moduleConfig
     * @param \Magento\Framework\App\Request\Http $request
     * @param \Magento\Checkout\Model\Cart $cart
     * @param \Lof\MarketPlace\Model\SellerFactory $sellerFactory
     * @param \Magento\Framework\ObjectManagerInterface $objectManager
     * @param \Magento\Framework\Message\ManagerInterface $messageManager
     */
    public function __construct(
        \Lofmp\SplitCart\Helper\ConfigData $moduleConfig,
        \Magento\Framework\App\Request\Http $request,
        \Magento\Checkout\Model\Cart $cart,
        \Lof\MarketPlace\Model\SellerFactory $sellerFactory,
        \Magento\Framework\ObjectManagerInterface $objectManager,
        \Magento\Framework\Message\ManagerInterface $messageManager
    ) {
        $this->moduleConfig = $moduleConfig;
        $this->_request = $request;
        $this->_cart = $cart;
        $this->sellerFactory = $sellerFactory;
        $this->_objectManager = $objectManager;
        $this->_messageManager = $messageManager;
    }

    /**
     * @param \Magento\Checkout\Controller\Cart\UpdatePost $subject
     * @return null
     * @SuppressWarnings(PHPMD.CyclomaticComplexity)
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function beforeExecute(\Magento\Checkout\Controller\Cart\UpdatePost $subject)
    {
        if (!$this->moduleConfig->isEnabled()) {
            return null;
        }

        try {
            $updateAction = (string)$this->_request->getParam('update_cart_action');
            if ($updateAction !== 'empty_cart_split') {
                return null;
            }

            $quote = $this->_cart->getQuote();
            if (!$quote || !$quote->hasItems()) {
                return null;
            }

            $items = $this->_cart->getQuote()->getItems();
            $sellerId = (int)$this->_request->getParam('sid');
            foreach ($items as $item) {
                $sellerIdItem = $item->getLofSellerId() ? $item->getLofSellerId() : $item->getSellerId();
                if ($sellerIdItem == $sellerId) {
                    $this->_cart->removeItem($item->getItemId());
                }
            }
            $this->_cart->save();

            if ($sellerId == 0) {
                $this->_messageManager->addSuccessMessage(__('Items of Admin have been removed.'));
            } else {
                $seller = $this->sellerFactory->create()->load($sellerId);
                if ($seller && $seller->getName()) {
                    $this->_messageManager->addSuccessMessage(__('Items of %1 have been removed.', $seller->getName()));
                }
            }
        } catch (\Exception $e) {
            $this->_messageManager->addErrorMessage($e->getMessage());
            $this->_objectManager->get(\Psr\Log\LoggerInterface::class)->critical($e);
        }
        return null;
    }
}
