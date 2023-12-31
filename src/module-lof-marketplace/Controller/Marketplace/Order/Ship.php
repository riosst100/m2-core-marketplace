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
use Magento\Sales\Model\Order\Shipment;

class Ship extends \Lof\MarketPlace\Controller\Marketplace\Order
{
    /**
     * @return \Magento\Framework\App\ResponseInterface|\Magento\Framework\Controller\Result\Redirect|\Magento\Framework\Controller\ResultInterface
     * @SuppressWarnings(PHPMD.CyclomaticComplexity)
     * @SuppressWarnings(PHPMD.NPathComplexity)
     * @SuppressWarnings(PHPMD.ExcessiveMethodLength)
     * @phpcs:disable Generic.Metrics.NestingLevel.TooHigh
     */
    public function execute()
    {
        $order = $this->_initOrder();
        if (!$order) {
            return $this->resultRedirectFactory->create()->setPath(
                'catalog/sales/order',
                ['_secure' => $this->getRequest()->isSecure()]
            );
        }
        try {
            $helper = $this->_objectManager->create(\Lof\MarketPlace\Helper\Data::class);
            $sellerId = $helper->getSellerId();
            $orderId = $order->getId();
            $trackingid = '';
            $trackingData = [];
            $paramData = $this->getRequest()->getParams();

            $this->_eventManager->dispatch(
                'marketplace_seller_start_shipment',
                ['account_controller' => $this, 'order' => $order, 'seller_id' => $sellerId, 'order_id' => $orderId, 'request' => $paramData]
            );

            $orderSeller = $this->_objectManager->create(\Lof\MarketPlace\Model\Order::class)
                ->getCollection()
                ->addFieldToFilter('order_id', $orderId)
                ->addFieldToFilter('seller_id', $sellerId)
                ->getFirstItem();
            if (!$orderSeller->getIsInvoiced()) {
                $this->messageManager->addErrorMessage(
                    __('Can not create shipment. Please create invoice before create shipment.')
                );
                return $this->resultRedirectFactory->create()->setPath(
                    'catalog/sales/orderview/',
                    [
                        'id' => $order->getEntityId(),
                        '_secure' => $this->getRequest()->isSecure(),
                    ]
                );
            }

            if (!empty($paramData['tracking_id'])) {
                $trackingid = $paramData['tracking_id'];
                $trackingData[1]['number'] = $trackingid;
                $trackingData[1]['carrier_code'] = 'custom';
            }
            if (!empty($paramData['carrier'])) {
                $carrier = $paramData['carrier'];
                $trackingData[1]['title'] = $carrier;
            }
            if (!empty($paramData['api_shipment'])) {
                $this->_eventManager->dispatch(
                    'generate_api_shipment',
                    [
                        'api_shipment' => $paramData['api_shipment'],
                        'order_id' => $orderId,
                    ]
                );
                $shipmentData = $this->_customerSession->getData('shipment_data');
                $trackingid = $shipmentData['tracking_number'];
                $trackingData[1]['number'] = $trackingid;
                $trackingData[1]['carrier_code'] = 'custom';
                $this->_customerSession->unsetData('shipment_data');
            }

            if (empty($paramData['api_shipment']) || $trackingid != '') {
                if ($order->canUnhold()) {
                    $this->messageManager->addErrorMessage(
                        __('Can not create shipment as order is in HOLD state')
                    );
                } else {
                    $items = [];

                    $collection = $this->_objectManager->create(\Lof\MarketPlace\Model\Orderitems::class)
                        ->getCollection()
                        ->addFieldToFilter('order_id', ['eq' => $orderId])
                        ->addFieldToFilter('seller_id', ['eq' => $sellerId]);
                    foreach ($collection as $saleproduct) {
                        $orderData = $this->_objectManager->create(\Magento\Sales\Model\Order::class)
                            ->load($orderId);
                        $orderItems = $orderData->getAllItems();
                        foreach ($orderItems as $item) {
                            if ($item->getData('item_id') == $saleproduct->getData('order_item_id')) {
                                array_push($items, $saleproduct->getData('order_item_id'));
                            }
                        }
                    }

                    $itemsarray = $this->_getShippingItemQtys($order, $items);

                    if (count($itemsarray) > 0) {
                        $shipment = false;
                        $shipmentId = 0;
                        if (!empty($paramData['shipment_id'])) {
                            $shipmentId = $paramData['shipment_id'];
                        }
                        if ($shipmentId) {
                            $shipment = $this->_objectManager->create(\Magento\Sales\Model\Order\Shipment::class)
                                ->load($shipmentId);
                        } elseif ($orderId) {
                            if ($order->getForcedDoShipmentWithInvoice()) {
                                $this->messageManager
                                    ->addErrorMessage(
                                        __('Cannot do shipment for the order separately from invoice.')
                                    );
                            }
                            if (!$order->canShip($sellerId)) {
                                $this->messageManager->addErrorMessage(
                                    __('Cannot do shipment for the order.')
                                );
                            }

                            $shipment = $this->_prepareShipment(
                                $order,
                                $itemsarray['data'],
                                $trackingData
                            );
                        }
                        if ($shipment) {
                            $shipment->getOrder()->setCustomerNoteNotify(
                                !empty($data['send_email'])
                            );
                            $shippingLabel = '';
                            if (!empty($data['create_shipping_label'])) {
                                $shippingLabel = $data['create_shipping_label'];
                            }
                            $isNeedCreateLabel = !empty($shippingLabel) && $shippingLabel;
                            $shipment->getOrder()->setIsInProcess(true);
                            $transactionSave = $this->_objectManager
                                ->create(\Magento\Framework\DB\Transaction::class)
                                ->addObject($shipment)
                                ->addObject($shipment->getOrder());
                            $transactionSave->save();
                            $this->_shipmentSender->send($shipment);
                            $shipmentCreatedMessage = __('The shipment has been created.');
                            $labelMessage = __('The shipping label has been created.');
                            $this->messageManager->addSuccessMessage(
                                $isNeedCreateLabel ? $shipmentCreatedMessage . ' ' . $labelMessage
                                    : $shipmentCreatedMessage
                            );

                            $this->_eventManager->dispatch(
                                'marketplace_seller_complete_shipment',
                                ['account_controller' => $this, 'order' => $order, 'seller_id' => $sellerId, 'order_id' => $orderId, 'shipment' => $shipment]
                            );
                        }
                    }
                }
            }
        } catch (\Magento\Framework\Exception\LocalizedException $e) {
            $this->messageManager->addErrorMessage($e->getMessage());
        } catch (\Exception $e) {
            $this->messageManager->addErrorMessage(
                __('We can\'t ship order right now.')
            );
        }

        return $this->resultRedirectFactory->create()->setPath(
            'catalog/sales/orderview/view',
            [
                'id' => $order->getEntityId(),
                '_secure' => $this->getRequest()->isSecure(),
            ]
        );
    }

    /**
     * Prepare shipment.
     *
     * @param $order
     * @param $items
     * @param $trackingData
     * @return Shipment|false
     */
    protected function _prepareShipment($order, $items, $trackingData)
    {
        $shipment = $this->_shipmentFactory->create(
            $order,
            $items,
            $trackingData
        );

        if (!$shipment->getTotalQty()) {
            return false;
        }

        return $shipment->register();
    }

    /**
     * @param RequestInterface $request
     * @return \Magento\Framework\App\ResponseInterface|\Magento\Framework\Controller\Result\Redirect
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function dispatch(RequestInterface $request)
    {
        if (!!$this->helper->getConfig('sales_settings/can_ship')) {
            return parent::dispatch($request);
        }

        $this->messageManager->addErrorMessage(
            __('We can\'t ship order right now.')
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
