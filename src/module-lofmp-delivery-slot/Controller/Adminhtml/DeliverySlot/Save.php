<?php
/**
 * Landofcoder
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Landofcoder.com license that is
 * available through the world-wide-web at this URL:
 * https://landofcoder.com/license
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade this extension to newer
 * version in the future.
 *
 * @category   Landofcoder
 * @package    Lofmp_DeliverySlot
 * @copyright  Copyright (c) 2021 Landofcoder (https://landofcoder.com/)
 * @license    https://landofcoder.com/LICENSE-1.0.html
 */

namespace Lofmp\DeliverySlot\Controller\Adminhtml\DeliverySlot;

use Magento\Backend\App\Action\Context;
use Lofmp\DeliverySlot\Model\DeliverySlotsFactory;
use Magento\Framework\View\Result\PageFactory;
use Lofmp\DeliverySlot\Model\ResourceModel\DeliverySlots\SaveSlotFactory;

/**
 * Class Save
 * @package Lofmp\DeliverySlot\Controller\Adminhtml\DeliverySlot
 */
class Save extends \Magento\Backend\App\Action
{
    /**
     * Save constructor.
     *
     * @param Context $context
     * @param PageFactory $resultPageFactory
     * @param \Magento\Framework\Session\SessionManagerInterface $sessionManager
     * @param \Magento\Framework\Message\ManagerInterface $messageManager
     * @param SaveSlot $saveSlot
     */
    public function __construct(
        Context $context,
        PageFactory $resultPageFactory,
        \Magento\Framework\Session\SessionManagerInterface $sessionManager,
        \Magento\Framework\Message\ManagerInterface $messageManager,
        SaveSlotFactory $saveSlotFactory
    ) {
    
        parent::__construct($context);
        $this->resultPageFactory = $resultPageFactory;
        $this->sessionManager = $sessionManager;
        $this->messageManager = $messageManager;
        $this->saveSlotFactory = $saveSlotFactory;
    }

    /**
     * @return \Magento\Framework\App\ResponseInterface|\Magento\Framework\Controller\ResultInterface|\Magento\Framework\View\Result\Page
     */
    public function execute()
    {
        $slotId = $this->getRequest()->getParam('slot_id');
        $startTime = $this->getRequest()->getParam('start_time');
        $endTime = $this->getRequest()->getParam('end_time');
        $diff = (strtotime($endTime) - strtotime($startTime));
        $hoursDiff = $diff/60;
        if ($hoursDiff <= 0) {
            $this->messageManager->addErrorMessage('Start Time Should Be Less than End Time');
            $resultRedirect = $this->resultRedirectFactory->create();
            $resultRedirect->setPath('*/*/edit', ['slot_id' => $slotId]);
            return $resultRedirect;
        }
        $slotValues = $this->getRequest()->getParams();
        $saveSlotModel = $this->saveSlotFactory->create();
        try {
            if(isset($slotValues['seller_id']) && !$slotValues['seller_id']){
                unset($slotValues['seller_id']);
            }
            $slotData = $saveSlotModel->save($slotValues);
            if (!empty($slotData)) {
                $returnToEdit = (bool)$this->getRequest()->getParam('back', false);
                if ($returnToEdit) {
                    if ($slotId == null) {
                        $this->messageManager->addSuccessMessage('Successfully Added New Slot');
                        $resultRedirect = $this->resultRedirectFactory->create();
                        $resultRedirect->setPath('*/*/edit', ['slot_id' => $slotData->getId()]);
                        return $resultRedirect;
                    } else {
                        $this->messageManager->addSuccessMessage('Successfully Updated  Slot');
                        $resultRedirect = $this->resultRedirectFactory->create();
                        $resultRedirect->setPath('*/*/edit', ['slot_id' => $slotId]);
                        return $resultRedirect;
                    }
                }
            } else {
                $this->messageManager->addErrorMessage('Unable to Add Slot');
                $resultRedirect = $this->resultRedirectFactory->create();
                $resultRedirect->setPath('*/*/index');
                return $resultRedirect;
            }
        } catch (\Exception $e) {
            $this->messageManager->addErrorMessage('Unable to Add Slot');
            $resultRedirect = $this->resultRedirectFactory->create();
            $resultRedirect->setPath('*/*/index');
            return $resultRedirect;
        }
        $this->messageManager->addSuccessMessage('Successfully Added New Slot');
        $resultRedirect = $this->resultRedirectFactory->create();
        $resultRedirect->setPath('*/*/index');
        return $resultRedirect;
    }
}
