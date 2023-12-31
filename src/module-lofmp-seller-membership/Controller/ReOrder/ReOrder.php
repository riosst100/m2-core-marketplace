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
 * @package    Lofmp_SellerMembership
 * @copyright  Copyright (c) 2021 Landofcoder (https://www.landofcoder.com/)
 * @license    https://landofcoder.com/terms
 */

namespace Lofmp\SellerMembership\Controller\ReOrder;

use Magento\Framework\App\Action\Action;
use Magento\Framework\App\Action\Context;
use Magento\Framework\Exception\LocalizedException;

class ReOrder extends Action
{
    /**
     * @var \Magento\Checkout\Model\Session
     */
    private $checkoutSession;

    /**
     * @var \Magento\Sales\Api\OrderRepositoryInterface
     */
    private $_orderRepository;

    /**
     * reOrder constructor.
     * @param Context $context
     * @param \Magento\Sales\Api\OrderRepositoryInterface $orderRepository
     * @param \Magento\Sales\Model\Reorder\Reorder|null $reorder
     * @param \Magento\Checkout\Model\Session $checkoutSession
     */
    public function __construct(
        Context $context,
        \Magento\Sales\Api\OrderRepositoryInterface $orderRepository,
        \Magento\Checkout\Model\Session $checkoutSession
    ) {
        $this->_orderRepository = $orderRepository;
        $this->checkoutSession = $checkoutSession;
        parent::__construct($context);
    }

    /**
     * @return \Magento\Framework\App\ResponseInterface|\Magento\Framework\Controller\Result\Redirect|\Magento\Framework\Controller\ResultInterface
     */
    public function execute()
    {
        /** @var \Magento\Framework\Controller\Result\Redirect $resultRedirect */
        $resultRedirect = $this->resultRedirectFactory->create();
        $orderId = $this->getRequest()->getParam('order_id');

        try {
            $order = $this->_orderRepository->get($orderId);
            if ($order) {
                try {
                    /* @var $cart \Magento\Checkout\Model\Cart */
                    $cart = $this->_objectManager->get(\Magento\Checkout\Model\Cart::class);
                    $cart->truncate();
                    $items = $order->getItemsCollection();
                    foreach ($items as $item) {
                        try {
                            $cart->addOrderItem($item);
                        } catch (\Magento\Framework\Exception\LocalizedException $e) {
                            if ($this->_objectManager->get(\Magento\Checkout\Model\Session::class)->getUseNotice(true)) {
                                $this->messageManager->addNoticeMessage($e->getMessage());
                            } else {
                                $this->messageManager->addErrorMessage($e->getMessage());
                            }

                            return $resultRedirect->setPath('checkout/cart');
                        } catch (\Exception $e) {
                            $this->messageManager->addExceptionMessage(
                                $e,
                                __('We can\'t add this item to your shopping cart right now.')
                            );
                            return $resultRedirect->setPath('checkout/cart');
                        }
                    }
                    $cart->save();
                } catch (LocalizedException $localizedException) {
                    $this->messageManager->addErrorMessage($localizedException->getMessage());
                    return $resultRedirect->setPath('checkout/cart');
                }

                return $resultRedirect->setPath('checkout/cart');
            }
        } catch (\Exception $ce) {
            return $resultRedirect->setPath('checkout/cart');
        }

        return $resultRedirect->setPath('checkout/cart');
    }
}
