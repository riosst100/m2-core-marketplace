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

namespace Lofmp\DeliverySlot\Controller\Adminhtml\DeliverySlotGroup;

use Lofmp\DeliverySlot\Model\DeliverySlotGroup;
use Magento\Backend\App\Action\Context;
use Magento\Framework\View\Result\PageFactory;

class Delete extends \Magento\Backend\App\Action
{
    const ADMIN_RESOURCE = 'Lofmp_DeliverySlot::lofmp_deliveryslot_config';

    public function __construct(
        Context $context,
        PageFactory $resultPageFactory,
        \Lofmp\DeliverySlot\Model\DeliverySlotGroup\DeleteGroupSlot $deletegroupslot
    ) {
    
        parent::__construct($context);
        $this->resultPageFactory = $resultPageFactory;
        $this->deletegroupslot = $deletegroupslot;
    }

    public function execute()
    {
        $groupId = $this->getRequest()->getParam('group_id');
        if ($groupId) {
            try {
                $result = $this->deletegroupslot->delete($groupId, 0, true);
                $Existingdata = (bool)$result;
                $resultRedirect = $this->resultRedirectFactory->create();
                if ($Existingdata) {
                    $this->messageManager->addSuccessMessage('SucessFully Deleted Group Slots');
                    $resultpage = $resultRedirect->setPath('*/*/index');
                } else {
                    $this->messageManager->addSuccessMessage('Unable Delete this Group Slot');
                    $resultpage = $resultRedirect->setPath('*/*/edit', ['group_id' => $groupId]);
                }
                return $resultpage;
            } catch (\Exception $e) {
                $this->messageManager->addSuccessMessage('Unable Delete this Group Slot');
                $resultpage = $resultRedirect->setPath('*/*/edit', ['group_id' => $groupId]);
                return $resultpage;
            }
        }
    }
}
