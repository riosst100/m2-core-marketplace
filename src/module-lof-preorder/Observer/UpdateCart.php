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
 * @package    Lof_PreOrder
 * @copyright  Copyright (c) 2021 Landofcoder (https://www.landofcoder.com/)
 * @license    https://landofcoder.com/terms
 */


namespace Lof\PreOrder\Observer;

use Magento\Framework\Event\ObserverInterface;
use Magento\Framework\App\RequestInterface;
use Lof\PreOrder\Model\ResourceModel\Complete\CollectionFactory;

class UpdateCart implements ObserverInterface
{
    /**
     * @var RequestInterface
     */
    protected $_request;

    /**
     * @var \Magento\Framework\Message\ManagerInterface
     */
    protected $_messageManager;

    /**
     * @var \Magento\Checkout\Model\CartFactory
     */
    protected $_cart;

    /**
     * @var \Lof\PreOrder\Helper\Data
     */
    protected $_preorderHelper;

    /**
     * @var CollectionFactory
     */
    protected $_completeCollection;

    /**
     * @param RequestInterface $request
     * @param \Magento\Framework\Message\ManagerInterface $messageManager
     * @param \Magento\Checkout\Model\CartFactory $cart
     * @param \Lof\PreOrder\Helper\Data $preorderHelper
     * @param CollectionFactory $completeCollection
     */
    public function __construct(
        RequestInterface $request,
        \Magento\Framework\Message\ManagerInterface $messageManager,
        \Magento\Checkout\Model\CartFactory $cart,
        \Lof\PreOrder\Helper\Data $preorderHelper,
        CollectionFactory $completeCollection
    ) {
        $this->_request = $request;
        $this->_messageManager = $messageManager;
        $this->_cart = $cart;
        $this->_preorderHelper = $preorderHelper;
        $this->_completeCollection = $completeCollection;
    }

    public function execute(\Magento\Framework\Event\Observer $observer)
    {
        $msg = 'You can not update the quantity of Complete PreOrder Product.';
        $helper = $this->_preorderHelper;
        $cart = $observer->getCart();
        if ($cart) {
            $quote = $cart->getQuote();
        } else {
            $quote = $this->_cart->create()->getQuote();
        }
        $error = false;
        foreach ($quote->getAllItems() as &$quoteItem) {
            $itemId = $quoteItem->getId();
            $collection = $this->_completeCollection->create();
            $field = 'quote_item_id';
            $item = $helper->getDataByField($itemId, $field, $collection);
            if ($item) {
                $qty = $item->getQty();
                $finalQty = $quoteItem->getQty();
                if ($finalQty != $qty) {
                    $quoteItem->setQty($qty);
                    $error = true;
                }
            }
        }
        if ($error) {
            $this->_messageManager->addNotice(__($msg));
            $quote->save();
        }
    }
}
