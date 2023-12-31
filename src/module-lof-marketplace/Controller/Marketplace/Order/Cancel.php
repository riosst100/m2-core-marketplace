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
 * @package    Lof_MarketPlace
 * @copyright  Copyright (c) 2021 Landofcoder (https://www.landofcoder.com/)
 * @license    https://landofcoder.com/terms
 */

namespace Lof\MarketPlace\Controller\Marketplace\Order;

use Magento\Framework\App\RequestInterface;

class Cancel extends \Lof\MarketPlace\Controller\Marketplace\Order
{
    /**
     * @return \Magento\Framework\App\ResponseInterface|\Magento\Framework\Controller\Result\Redirect|\Magento\Framework\Controller\ResultInterface
     */
    public function execute()
    {
        if ($order = $this->_initOrder()) {
            try {
                $helper = $this->_objectManager->create(\Lof\MarketPlace\Helper\Data::class);
                $sellerId = $helper->getSellerId();
                $orderId = $order->getId();
                $this->_eventManager->dispatch(
                    'marketplace_seller_start_cancel_order',
                    ['account_controller' => $this, 'seller_id' => $sellerId, 'order' => $order, 'order_id' => $orderId]
                );

                $flag = $helper->cancelorder($order, $sellerId);

                if ($flag) {
                    $orderId = $this->getRequest()->getParam('id');

                    $trackingcoll = $this->_objectManager->create(\Lof\MarketPlace\Model\Order::class)
                        ->getCollection()
                        ->addFieldToFilter(
                            'order_id',
                            $orderId
                        )->addFieldToFilter(
                            'seller_id',
                            $sellerId
                        );

                    foreach ($trackingcoll as $tracking) {
                        $tracking->setTrackingNumber('canceled');
                        $tracking->setCarrierName('canceled');
                        $tracking->setIsCanceled(1);
                        $tracking->save();
                    }

                    $this->_eventManager->dispatch(
                        'marketplace_seller_complete_cancel_order',
                        ['account_controller' => $this, 'seller_id' => $sellerId, 'order' => $order, 'order_id' => $orderId]
                    );

                    $this->messageManager->addSuccessMessage(
                        __('The order has been cancelled.')
                    );
                } else {
                    $this->messageManager->addErrorMessage(
                        __('You are not permitted to cancel this order.')
                    );

                    return $this->resultRedirectFactory->create()->setPath(
                        'catalog/sales/order',
                        ['_secure' => $this->getRequest()->isSecure()]
                    );
                }
            } catch (\Magento\Framework\Exception\LocalizedException $e) {
                $this->messageManager->addErrorMessage($e->getMessage());
            } catch (\Exception $e) {
                $this->messageManager->addErrorMessage(
                    __('We can\'t cancel order right now.')
                );
            }

            return $this->resultRedirectFactory->create()->setPath(
                'catalog/sales/orderview/view',
                [
                    'id' => $order->getEntityId(),
                    '_secure' => $this->getRequest()->isSecure(),
                ]
            );
        } else {
            return $this->resultRedirectFactory->create()->setPath(
                'catalog/sales/order',
                ['_secure' => $this->getRequest()->isSecure()]
            );
        }
    }

    /**
     * @param RequestInterface $request
     * @return \Magento\Framework\App\ResponseInterface|\Magento\Framework\Controller\Result\Redirect
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function dispatch(RequestInterface $request)
    {
        if (!!$this->helper->getConfig('sales_settings/can_cancel')) {
            return parent::dispatch($request);
        }

        $this->messageManager->addErrorMessage(
            __('We can\'t cancel order right now.')
        );

        return  $this->resultRedirectFactory->create()->setPath(
            'catalog/sales/orderview/view',
            [
                'id' => $this->getRequest()->getParam('id'),
                '_secure' => $this->getRequest()->isSecure(),
            ]
        );
    }
}
