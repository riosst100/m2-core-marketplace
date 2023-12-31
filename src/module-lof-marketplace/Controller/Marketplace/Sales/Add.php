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

namespace Lof\MarketPlace\Controller\Marketplace\Sales;

class Add extends \Lof\MarketPlace\Controller\Marketplace\Order
{
    /**
     * @return \Magento\Framework\App\ResponseInterface|\Magento\Framework\Controller\ResultInterface|void
     */
    public function execute()
    {
        try {
            $carrier = $this->getRequest()->getPost('carrier');
            $number = $this->getRequest()->getPost('number');
            $title = $this->getRequest()->getPost('title');
            $orderId = $this->getRequest()->getParam('order_id');
            $shipmentId = $this->getRequest()->getParam('shipment_id');
            if (empty($carrier)) {
                throw new \Magento\Framework\Exception\LocalizedException(
                    __('Please specify a carrier.')
                );
            }
            if (empty($number)) {
                throw new \Magento\Framework\Exception\LocalizedException(
                    __('Please enter a tracking number.')
                );
            }

            $shipment = $this->_initShipment();
            $track = $this->_objectManager->create(
                \Magento\Sales\Model\Order\Shipment\Track::class
            )->setNumber(
                $number
            )->setCarrierCode(
                $carrier
            )->setTitle(
                $title
            );
            $shipment->addTrack($track)->save();
            $trackId = $track->getId();

            if ($track->isCustom()) {
                $numberclass = 'display';
                $numberclasshref = 'no-display';
                $trackingPopupUrl = '';
            } else {
                $numberclass = 'no-display';
                $numberclasshref = 'display';
                $trackingPopupUrl = $this->_objectManager->create(
                    \Magento\Shipping\Helper\Data::class
                )->getTrackingPopupUrlBySalesModel($track);
            }

            $response = [
                'error' => false,
                'carrier' => $this->_objectManager->create(
                    \Lof\MarketPlace\Block\Sale\Shipment\View::class
                )->getCarrierTitle($carrier),
                'title' => $title,
                'number' => $number,
                'numberclass' => $numberclass,
                'numberclasshref' => $numberclasshref,
                'trackingPopupUrl' => $trackingPopupUrl,
                'trackingDeleteUrl' => $this->_objectManager->create(
                    \Magento\Framework\UrlInterface::class
                )->getUrl(
                    'sale/tracking/delete',
                    [
                        'order_id' => $orderId,
                        'shipment_id' => $shipmentId,
                        'id' => $trackId,
                        '_secure' => $this->getRequest()->isSecure()
                    ]
                )
            ];
        } catch (\Magento\Framework\Exception\LocalizedException $e) {
            $response = ['error' => true, 'message' => $e->getMessage()];
        } catch (\Exception $e) {
            $response = [
                'error' => true,
                'message' => __('Cannot add tracking number.%1', $e->getMessage())
            ];
        }

        if (is_array($response)) {
            $response = $this->_objectManager->get(
                \Magento\Framework\Json\Helper\Data::class
            )->jsonEncode($response);
            $this->getResponse()->representJson($response);
        } else {
            $this->getResponse()->setBody($response);
        }
    }
}
