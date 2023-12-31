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
use Lofmp\DeliverySlot\Model\ResourceModel\DeliverySlots\DeleteSlotFactory;
use Magento\Framework\Message\ManagerInterface;

/**
 * Class Delete
 * @package Lofmp\DeliverySlot\Controller\Adminhtml\DeliverySlot
 */
class Delete extends \Magento\Backend\App\Action
{
    const ADMIN_RESOURCE = 'Lofmp_DeliverySlot::lofmp_deliveryslot_config';

    /**
     * Index constructor.
     * @param Context $context
     * @param PageFactory $resultPageFactory
     */
    public function __construct(
        Context $context,
        DeleteSlotFactory $deleteSlotFactory,
        ManagerInterface $managerInterface
    ) {
        parent::__construct($context);
        $this->deleteSlotFactory = $deleteSlotFactory;
        $this->managerInterface = $managerInterface;
    }


    public function execute()
    {
        $slotId = $this->getRequest()->getParam('slot_id');
        if ($slotId) {
            try {
                $deleteSlot = $this->deleteSlotFactory->create();
                $status = $deleteSlot->delete($slotId);
                if ($status) {
                    $this->messageManager->addSuccessMessage('Successfully Deleted Slot');
                    $resultRedirect = $this->resultRedirectFactory->create();
                    $resultRedirect->setPath('*/*/index');
                    return $resultRedirect;
                } else {
                    $this->messageManager->addErrorMessage('Unable To Delete The Slot');
                    $resultRedirect = $this->resultRedirectFactory->create();
                    $resultRedirect->setPath('*/*/edit', ['slot_id' => $slotId]);
                    return $resultRedirect;
                }
            } catch (\Exception $e) {
                $this->messageManager->addErrorMessage('Unable To Delete The Slot');
                $resultRedirect = $this->resultRedirectFactory->create();
                $resultRedirect->setPath('*/*/edit', ['slot_id' => $slotId]);
                return $resultRedirect;
            }
        } else {
            $this->messageManager->addErrorMessage('Unable To Delete The Slot');
            $resultRedirect = $this->resultRedirectFactory->create();
            $resultRedirect->setPath('*/*/edit', ['slot_id' => $slotId]);
            return $resultRedirect;
        }
    }
}
