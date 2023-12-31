<?php
/**
 * LandOfCoder
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Landofcoder.com license that is
 * available through the world-wide-web at this URL:
 * https://landofcoder.com/license-agreement.html
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade this extension to newer
 * version in the future.
 *
 * @category   LandOfCoder
 * @package    Lofmp_Rma
 * @copyright  Copyright (c) 2020 Landofcoder (https://landofcoder.com/)
 * @license    https://landofcoder.com/LICENSE-1.0.html
 */
namespace Lofmp\Rma\Controller\Rma;

use Magento\Framework\Controller\ResultFactory;
use Magento\Framework\App\Action\Context;
use Magento\Framework\Exception\CouldNotSaveException;

class Save extends \Lofmp\Rma\Controller\Rma
{
    public function __construct(
        \Lofmp\Rma\Helper\Data                                  $datahelper,
        \Lofmp\Rma\Helper\Help                                 $helper,
        \Lofmp\Rma\Model\RmaFactory                               $rmaFactory,
        \Lofmp\Rma\Model\ItemFactory                              $itemFactory,
        \Magento\Sales\Model\OrderFactory                       $orderFactory,
        \Magento\Framework\Event\ManagerInterface               $eventManager,
        \Lofmp\Rma\Api\Repository\RmaRepositoryInterface $rmaRepository,
        \Lofmp\Rma\Api\Repository\MessageRepositoryInterface $messageRepository,
        \Magento\Framework\Registry                             $registry,
        \Magento\Customer\Model\Session                         $customerSession,
        Context                                                 $context
    ) {
        $this->customerSession      = $customerSession;
        $this->datahelper           = $datahelper;
        $this->helper               = $helper;
        $this->rmaFactory           = $rmaFactory;
        $this->itemFactory          = $itemFactory;
        $this->orderFactory         = $orderFactory;
        $this->eventManager         = $eventManager;
        $this->registry             = $registry;
        $this->messageRepository     = $messageRepository;
        $this->rmaRepository         = $rmaRepository;

        parent::__construct($customerSession, $context);
    }


    /**
     * {@inheritdoc}
     */
    public function execute()
    {
        /** @var \Magento\Framework\Controller\Result\Redirect $resultRedirect */
        $resultRedirect = $this->resultFactory->create(ResultFactory::TYPE_REDIRECT);

        $data = $this->getRequest()->getParams();

        if (!$this->datahelper->validate($data)) {
                return $resultRedirect->setPath(
                    '*/*/select',
                    ['order_id' => $data['order_id'], '_current' => true]
                );
        }
        $currentcustomer = $this->customerSession->getCustomer();
        try {
            $rmaData = $data;
            unset($rmaData['items']);
            $rma = $this->rmaFactory->create();
            if (isset($rmaData['street2']) && $rmaData['street2'] != '') {
                $rmaData['street'] .= "\n".$rmaData['street2'];
                unset($rmaData['street2']);
            }
            /** @var \Magento\Sales\Model\Order $order */
            $order = $this->orderFactory->create()->load((int) $rmaData['order_id']);
            if ($order->getCustomerId() != $currentcustomer->getId()) {
                throw new CouldNotSaveException(__(
                    'Could not save the RMA request because current customer is different than order customer.'
                ));
            } else {
                $rma->setCustomerId($order->getCustomerId());
                $rma->setStoreId($order->getStoreId());
                $rma->setCustomerId($currentcustomer->getId());
                $rma->setStatusId($this->helper->getConfig($store = null, 'rma/general/default_status'));
                $rma->addData($rmaData);
                $rma->save();
                $this->registry->register('current_rma', $rma);
                $order = $this->orderFactory->create()->load((int) $data['order_id']);
                $itemCollection = $order->getItemsCollection();

                $itemdatas = $data['items'];
                foreach ($itemdatas as $k => $item) {
                    if (isset($item['reason_id']) && !(int)$item['reason_id']) {
                            unset($item['reason_id']);
                            $item['qty_requested'] = 0;
                    }
                    if (isset($item['resolution_id']) && !(int)$item['resolution_id']) {
                        unset($item['resolution_id']);
                    }
                    if (isset($item['condition_id']) && !(int)$item['condition_id']) {
                        unset($item['condition_id']);
                    }
                    $item['order_id'] = $data['order_id'];
                    $item['order_item_id'] = $k;

                    $orderItem = $itemCollection->getItemById($k);
                    if ($orderItem) {
                        $productId = $orderItem->getProductId();
                        if (!$productId) {
                            $product   = $this->productRepository->get($orderItem->getSku());
                            $productId = $product->getId();
                        }
                        $item['product_id'] = $productId;
                    }
                    $itemdatas[$k] = $item;
                }
                foreach ($itemdatas as $item) {
                    $items = $this->itemFactory->create();
                    if (isset($item['item_id']) && $item['item_id']) {
                        $items->load((int) $item['item_id']);
                    }
                    unset($item['item_id']);
                    $items->addData($item)
                        ->setRmaId($rma->getId());
                    $items->save();
                }
                $files = $this->getRequest()->getFiles();
                if ((isset($data['reply']) && $data['reply'] != '') || count($files->toArray()) > 0 ) {

                    $message = $this->messageRepository->create();
                    $message->setRmaId($rma->getId())
                            ->setText($data['reply'], false);

                    if (!isset($data['isNotified'])) {
                        $data['isNotified'] = 1;
                    }
                    if (!isset($data['isVisible'])) {
                        $data['isVisible'] = 1;
                    }
                    $message->setIsCustomerNotified($data['isNotified']);
                    $message->setIsVisibleInFrontend($data['isVisible']);
                    $message->setCustomerId($currentcustomer->getId())
                        ->setCustomerName($currentcustomer->getName());

                    $this->messageRepository->save($message);
                    $rma->setLastReplyName($currentcustomer->getName())
                        ->setIsAdminRead($currentcustomer instanceof \Magento\User\Model\User);

                    $this->rmaRepository->save($rma);
                    $this->eventManager->dispatch(
                        'rma_add_message_after',
                        ['rma'=> $rma, 'message' => $message, 'user' => $currentcustomer, 'params' => $data]
                    );
                }
                $this->eventManager->dispatch('rma_update_rma_after', ['rma' => $rma, 'user' => $currentcustomer]);
                $this->messageManager->addSuccessMessage(__('RMA was successfuly created'));
                return $resultRedirect->setPath('*/*/view', ['id' => $rma->getId(), '_nosid' => true]);
            }
        } catch (\Magento\Framework\Exception\LocalizedException $e) {
            $this->messageManager->addErrorMessage($e->getMessage());
            $this->customerSession->setFormData($data);
            if ($this->getRequest()->getParam('id')) {
                return $resultRedirect->setPath('*/*/view', ['id' => $this->getRequest()->getParam('id')]);
            } else {
                return $resultRedirect->setPath('*/*/new', ['seller_id' => $this->getRequest()->getParam('seller_id'),'order_id' => $this->getRequest()->getParam('order_id')]);
            }
        }
    }
}
