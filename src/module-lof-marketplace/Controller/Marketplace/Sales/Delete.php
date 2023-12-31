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

class Delete extends \Lof\MarketPlace\Controller\Marketplace\Order
{
    /**
     * @return \Magento\Framework\App\ResponseInterface|\Magento\Framework\Controller\ResultInterface|void
     */
    public function execute()
    {
        try {
            $trackId = $this->getRequest()->getParam('id');
            if ($this->_initShipment()) {
                $track = $this->_objectManager->create(\Magento\Sales\Model\Order\Shipment\Track::class)
                    ->load($trackId);

                if ($track->getId()) {
                    $track->delete();
                    $response = [
                        'error' => false
                    ];
                } else {
                    $response = [
                        'error' => true,
                        'message' => __(
                            'We can\'t load track with retrieving identifier right now.%1'
                        )
                    ];
                }
            } else {
                $response = [
                    'error' => true,
                    'message' => __(
                        'We can\'t initialize shipment for adding tracking number.'
                    ),
                ];
            }
        } catch (\Exception $e) {
            $response = [
                'error' => true,
                'message' => __('We can\'t delete tracking number.')
            ];
        }
        if (is_array($response)) {
            $response = $this->_objectManager->get(\Magento\Framework\Json\Helper\Data::class)->jsonEncode($response);
            $this->getResponse()->representJson($response);
        } else {
            $this->getResponse()->setBody($response);
        }
    }
}
