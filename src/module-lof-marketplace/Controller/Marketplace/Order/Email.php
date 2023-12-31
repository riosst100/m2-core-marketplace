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

class Email extends \Lof\MarketPlace\Controller\Marketplace\Order
{

    /**
     * @return \Magento\Framework\App\ResponseInterface|\Magento\Framework\Controller\Result\Redirect|\Magento\Framework\Controller\ResultInterface
     */
    public function execute()
    {
        if ($order = $this->_initOrder()) {
            try {
                $this->_orderManagement->notify($order->getEntityId());
                $this->messageManager->addSuccessMessage(
                    __('You sent the order email.')
                );
            } catch (\Magento\Framework\Exception\LocalizedException $e) {
                $this->messageManager->addErrorMessage($e->getMessage());
            } catch (\Exception $e) {
                $this->messageManager->addErrorMessage(
                    __('We can\'t send the email order right now.')
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
        if (!!$this->helper->getConfig('sales_settings/can_send_email')) {
            return parent::dispatch($request);
        }

        $this->messageManager->addErrorMessage(
            __('We can\'t send the email order right now.')
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
