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
 * @copyright  Copyright (c) 2021 Landofcoder (https://landofcoder.com/)
 * @license    https://landofcoder.com/LICENSE-1.0.html
 */



namespace Lofmp\Rma\Controller\Adminhtml\Rma;

use Magento\Framework\Controller\ResultFactory;
use Magento\Framework\Exception\LocalizedException;
use Lofmp\Rma\Controller\Adminhtml\Rma;

class Save extends Rma
{
    public function __construct(
        \Lofmp\Rma\Helper\Data                                    $dataHelper,
        \Lofmp\Rma\Model\RmaFactory                               $rmaFactory,
        \Lofmp\Rma\Model\ItemFactory                              $itemFactory,
        \Lofmp\Rma\Model\AttachmentFactory                       $AttachmentFactory,
        \Magento\Sales\Model\OrderFactory                       $orderFactory,
        \Magento\Framework\Event\ManagerInterface               $eventManager,
        \Lofmp\Rma\Api\Repository\RmaRepositoryInterface $rmaRepository,
        \Lofmp\Rma\Api\Repository\MessageRepositoryInterface $messageRepository,
        \Magento\Framework\Registry                             $registry,
        \Magento\Backend\App\Action\Context                     $context
    ) {
        $this->rmaFactory           = $rmaFactory;
        $this->itemFactory          = $itemFactory;
        $this->orderFactory         = $orderFactory;
        $this->attachmentFactory  = $AttachmentFactory;
        $this->messageRepository     = $messageRepository;
        $this->rmaRepository         = $rmaRepository;
        $this->eventManager         = $eventManager;
        $this->registry             = $registry;
        $this->dataHelper          = $dataHelper;

        parent::__construct($context);
    }


    /**
     * {@inheritdoc}
     */
    public function execute()
    {
        /** @var \Magento\Framework\Controller\Result\Redirect $resultRedirect */
        $resultRedirect = $this->resultFactory->create(ResultFactory::TYPE_REDIRECT);

        if ($data = $this->getRequest()->getParams()) {

            if (!$this->dataHelper->validate($data)) {
                return $resultRedirect->setPath(
                    '*/*/add',
                    ['order_id' => $data['order_id'], '_current' => true]
                );
            }
            try {
                $user = $this->_auth->getUser();

                $rmadata =  $data;
                unset($rmadata['items']);

                if (empty($rmadata['return_address'])) {
                    unset($rmadata['return_address']);
                }
                $itemdata = $data['items'];
                foreach ($itemdata as $k => $item) {
                    if (!(int) $item['reason_id']) {
                        unset($item['reason_id']);
                    }
                    if (!(int) $item['resolution_id']) {
                        unset($item['resolution_id']);
                    }
                    if (!(int) $item['condition_id']) {
                        unset($item['condition_id']);
                    }
                    $itemdata[$k] = $item;
                }
                $rma = $this->rmaFactory->create();
                if (isset($rmadata['rma_id']) && $rmadata['rma_id']) {
                    $rma->load($data['rma_id']);
                }
                unset($rmadata['rma_id']);
                /** @var \Magento\Sales\Model\Order $order */
                $order = $this->orderFactory->create()->load((int) $rmadata['order_id']);
                $rma->setCustomerId($order->getCustomerId());
                $rma->setStoreId($order->getStoreId());

                if (!$rma->getUserId()) {
                    $rma->setUserId($user->getId());
                }
                if (!isset($rmadata["return_address"]) || !$rmadata["return_address"]) {
                    $seller_address = $this->dataHelper->getSellerAddress($rma->getSellerId());
                    $rmadata["return_address"] = $seller_address?$seller_address:$this->dataHelper->getConfig("general/return_address");
                }
                $rma->addData($rmadata);
                $rma->save();

                $this->registry->register('current_rma', $rma);
                foreach ($itemdata as $item) {
                    $itemModel = $this->itemFactory->create();
                    if (isset($item['item_id']) && $item['item_id']) {
                        $itemModel->load((int) $item['item_id']);
                    }
                    unset($item['item_id']);
                    $itemModel->addData($item)
                        ->setRmaId($rma->getId());
                    $itemModel->save();
                }
                $files = $this->getRequest()->getFiles();
                if ((isset($data['reply']) && $data['reply'] != '') || count($files->toArray()) > 0) {
                    $message = $this->messageRepository->create();
                    $message->setRmaId($rma->getId())
                        ->setText($data['reply'], false);
                    if (isset($data['internalcheck'])) {
                                $message->setInternal($data['internalcheck'])
                                ->setIsCustomerNotified(false)
                                ->setUserId($user->getId());
                    } else {
                            $message->setInternal(0)
                                ->setIsCustomerNotified(true)
                                ->setUserId($user->getId());
                    }
                        $this->messageRepository->save($message);

                        $rma->setLastReplyName($user->getName())
                            ->setIsAdminRead($user instanceof \Magento\User\Model\User);
                        $this->rmaRepository->save($rma);

                        $this->eventManager->dispatch(
                            'rma_add_message_after',
                            ['rma'=> $rma, 'message' => $message, 'user' => $user, 'params' => $data]
                        );
                }

                $this->eventManager->dispatch('rma_update_rma_after', ['rma' => $rma, 'user' => $user]);
                $this->messageManager->addSuccessMessage(__('RMA was successfully saved'));
                $this->backendSession->setFormData(false);

                if ($this->getRequest()->getParam('back')) {
                    return $resultRedirect->setPath('*/*/edit', ['id' => $rma->getId()]);
                }
                return $resultRedirect->setPath('*/*/');
            } catch (LocalizedException $e) {
                $this->messageManager->addErrorMessage($e->getMessage());
                $this->backendSession->setFormData($data);
                if ($this->getRequest()->getParam('id')) {
                    return $resultRedirect->setPath('*/*/edit', ['id' => $this->getRequest()->getParam('id')]);
                } else {
                    return $resultRedirect->setPath(
                        '*/*/add',
                        ['order_id' => $this->getRequest()->getParam('order_id')]
                    );
                }
            }
        }
        $this->messageManager->addErrorMessage(__('Unable to find rma to save'));

        return $resultRedirect->setPath('*/*/');
    }
}
