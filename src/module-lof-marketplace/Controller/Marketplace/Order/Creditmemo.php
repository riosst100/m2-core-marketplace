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

class Creditmemo extends \Lof\MarketPlace\Controller\Marketplace\Order
{
    /**
     * @var int $_sellerId
     */
    protected $_sellerId = 0;

    /**
     * @return \Magento\Framework\App\ResponseInterface|\Magento\Framework\Controller\Result\Redirect|\Magento\Framework\Controller\ResultInterface
     * @SuppressWarnings(PHPMD.CyclomaticComplexity)
     */
    public function execute()
    {
        if ($order = $this->_initOrder()) {
            try {
                $sellerId = $this->getSellerId();
                $this->_eventManager->dispatch(
                    'marketplace_seller_start_creditmemo',
                    ['account_controller' => $this, 'order' => $order, 'seller_id' => $sellerId]
                );
                $creditmemo = $this->_initOrderCreditmemo($order);
                if ($creditmemo) {
                    if (!$creditmemo->isValidGrandTotal()) {
                        throw new \Magento\Framework\Exception\LocalizedException(
                            __('The credit memo\'s total must be positive.')
                        );
                    }

                    $data = $this->getRequest()->getParam('creditmemo');
                    if (!empty($data['shipping_amount'])) {
                        $creditmemo->setShippingAmount(
                            $data['shipping_amount']
                        );
                    }
                    if (!empty($data['comment_text'])) {
                        $creditmemo->addComment(
                            $data['comment_text'],
                            isset($data['comment_customer_notify']),
                            isset($data['is_visible_on_front'])
                        );
                        $creditmemo->setCustomerNote($data['comment_text']);
                        $creditmemo->setCustomerNoteNotify(isset($data['comment_customer_notify']));
                    }

                    if (isset($data['do_offline'])) {
                        if (!$data['do_offline'] && !empty($data['refund_customerbalance_return_enable'])) {
                            throw new \Magento\Framework\Exception\LocalizedException(
                                __('Cannot create online refund for Refund to Store Credit.')
                            );
                        }
                    }

                    $creditmemoManagement = $this->_objectManager->create(
                        \Magento\Sales\Api\CreditmemoManagementInterface::class
                    );

                    $creditmemo = $creditmemoManagement
                        ->refund($creditmemo, (bool)$data['do_offline'], !empty($data['send_email']));

                    if (!empty($data['send_email'])) {
                        $this->_creditmemoSender->send($creditmemo);
                    }

                    if (!empty($data['send_email'])) {
                        $this->_creditmemoSender->send($creditmemo);
                    }

                    $this->_eventManager->dispatch(
                        'marketplace_seller_complete_creditmemo',
                        ['account_controller' => $this, 'order' => $order, 'seller_id' => $sellerId, 'creditmemo' => $creditmemo]
                    );

                    $this->messageManager->addSuccessMessage(__('You created the credit memo.'));
                }
            } catch (\Magento\Framework\Exception\LocalizedException $e) {
                $this->messageManager->addErrorMessage($e->getMessage());
            } catch (\Exception $e) {
                $this->messageManager->addErrorMessage(
                    __('We can\'t create credit memo for order right now.')
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
     * @param $order
     * @return bool|\Magento\Sales\Api\Data\InvoiceInterface
     */
    protected function _initCreditmemoInvoice($order)
    {
        $invoiceId = $this->_objectManager->create(\Lof\MarketPlace\Model\Invoice::class)
            ->load($order->getId(), 'seller_order_id')->getInvoiceId();

        if ($invoiceId) {
            $invoice = $this->_invoiceRepository->get($invoiceId);
            $invoice->setOrder($order);
            if ($invoice->getId()) {
                return $invoice;
            }
        }

        return false;
    }

    /**
     * @return int
     */
    protected function getSellerId()
    {
        if (!$this->_sellerId) {
            $this->_sellerId = $this->helper->getSellerId();
        }
        return $this->_sellerId;
    }

    /**
     * @param $order
     * @return \Magento\Sales\Model\Order\Creditmemo|bool
     * @SuppressWarnings(PHPMD.CyclomaticComplexity)
     * @SuppressWarnings(PHPMD.NPathComplexity)
     * @SuppressWarnings(PHPMD.ExcessiveMethodLength)
     */
    protected function _initOrderCreditmemo($order)
    {
        $refundData = $this->getRequest()->getParams();
        $sellerId = $this->getSellerId();
        $orderId = $order->getId();

        if (!$sellerId && !$orderId) {
            return false;
        }
        $invoice = $this->_initCreditmemoInvoice($order);
        $items = [];
        $shippingAmount = 0;

        $collection = $this->_objectManager->create(\Lof\MarketPlace\Model\Orderitems::class)
            ->getCollection()
            ->addFieldToFilter('order_id', (int)$orderId)
            ->addFieldToFilter('seller_id', (int)$sellerId);

        foreach ($collection as $saleproduct) {
            $orderData = $this->_objectManager->create(\Magento\Sales\Model\Order::class)->load($orderId);
            $orderItems = $orderData->getAllItems();
            foreach ($orderItems as $item) {
                if ($item->getData('item_id') == $saleproduct->getData('order_item_id')) {
                    array_push($items, $saleproduct->getData('order_item_id'));
                }
            }
        }

        $savedData = $this->_getItemData($order, $items);

        $qtys = [];
        foreach ($savedData as $orderItemId => $itemData) {
            if (isset($itemData['qty'])) {
                $qtys[$orderItemId] = $itemData['qty'];
            }
            if (isset($refundData['creditmemo']['items'][$orderItemId]['back_to_stock'])) {
                $backToStock[$orderItemId] = true;
            }
        }

        if (empty($refundData['creditmemo']['shipping_amount'])) {
            $refundData['creditmemo']['shipping_amount'] = 0;
        }
        if (empty($refundData['creditmemo']['adjustment_positive'])) {
            $refundData['creditmemo']['adjustment_positive'] = 0;
        }
        if (empty($refundData['creditmemo']['adjustment_negative'])) {
            $refundData['creditmemo']['adjustment_negative'] = 0;
        }
        if (!$shippingAmount >= $refundData['creditmemo']['shipping_amount']) {
            $refundData['creditmemo']['shipping_amount'] = 0;
        }
        $refundData['creditmemo']['qtys'] = $qtys;
        if ($invoice) {
            $creditmemo = $this->_creditmemoFactory->createByInvoice(
                $invoice,
                $refundData['creditmemo']
            );
        } else {
            $creditmemo = $this->_creditmemoFactory->createByOrder(
                $order,
                $refundData['creditmemo']
            );
        }

        /*
         * Process back to stock flags
         */
        foreach ($creditmemo->getAllItems() as $creditmemoItem) {
            $orderItem = $creditmemoItem->getOrderItem();
            $parentId = $orderItem->getParentItemId();
            if (isset($backToStock[$orderItem->getId()])) {
                $creditmemoItem->setBackToStock(true);
            } elseif ($orderItem->getParentItem() && isset($backToStock[$parentId]) && $backToStock[$parentId]) {
                $creditmemoItem->setBackToStock(true);
            } elseif (empty($savedData)) {
                $creditmemoItem->setBackToStock(
                    $this->_stockConfiguration->isAutoReturnEnabled()
                );
            } else {
                $creditmemoItem->setBackToStock(false);
            }
        }

        $this->_coreRegistry->register('current_creditmemo', $creditmemo);

        return $creditmemo;
    }

    /**
     * @param $order
     * @param $items
     * @return array|mixed
     * @SuppressWarnings(PHPMD.CyclomaticComplexity)
     */
    protected function _getItemData($order, $items)
    {
        $refundData = $this->getRequest()->getParams();
        $data['items'] = [];
        foreach ($order->getAllItems() as $item) {
            if (in_array($item->getItemId(), $items)
                && isset($refundData['creditmemo']['items'][$item->getItemId()]['qty'])
            ) {
                $data['items'][$item->getItemId()]['qty'] =
                    (int)$refundData['creditmemo']['items'][$item->getItemId()]['qty'];

                $_item = $item;
                // phpcs:disable Magento2.Performance.ForeachArrayMerge.ForeachArrayMerge
                $bundleitems = array_merge([$_item], $_item->getChildrenItems());
                if ($_item->getParentItem()) {
                    continue;
                }

                if ($_item->getProductType() == 'bundle') {
                    foreach ($bundleitems as $_bundleitem) {
                        if ($_bundleitem->getParentItem()) {
                            $data['items'][$_bundleitem->getItemId()]['qty'] =
                                (int)$refundData['creditmemo']['items'][$_bundleitem->getItemId()]['qty'];
                        }
                    }
                }
            } else {
                if (!$item->getParentItemId()) {
                    $data['items'][$item->getItemId()]['qty'] = 0;
                }
            }
        }

        if (isset($data['items'])) {
            $qtys = $data['items'];
        } else {
            $qtys = [];
        }
        return $qtys;
    }

    /**
     * @param RequestInterface $request
     * @return \Magento\Framework\App\ResponseInterface|\Magento\Framework\Controller\Result\Redirect
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function dispatch(RequestInterface $request)
    {
        if (!!$this->helper->getConfig('sales_settings/can_refund')) {
            return parent::dispatch($request);
        }

        $this->messageManager->addErrorMessage(
            __('We can\'t create credit memo for order right now.')
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
