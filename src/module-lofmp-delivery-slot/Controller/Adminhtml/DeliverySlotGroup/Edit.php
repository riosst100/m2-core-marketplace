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
use Magento\Framework\Registry;

class Edit extends \Magento\Backend\App\Action
{
    const ADMIN_RESOURCE = 'Lofmp_DeliverySlot::lofmp_deliveryslot_config';

    public function __construct(
        Context $context,
        Registry $registry,
        PageFactory $resultPageFactory
    ) {
    
        parent::__construct($context);
        $this->resultPageFactory = $resultPageFactory;
        $this->registry = $registry;
    }

    public function execute()
    {
        $groupId = $this->getRequest()->getParam('group_id');
        if ($groupId) {
            $this->registry->register('group_id', $groupId);
        }
        $resultPage = $this->redirectPage();
        $isExistingRule = (bool)$groupId;
        if ($isExistingRule) {
            $resultPage->getConfig()->getTitle()->prepend(sprintf("SlotGroup %s", $groupId));
        } else {
            $resultPage->getConfig()->getTitle()->prepend(__('Delivery Group Slots'));
        }
        return $resultPage;
    }

    public function redirectPage()
    {
        $resultPage = $this->resultPageFactory->create();
        $resultPage->setActiveMenu('Lofmp_DeliverySlot::slot_groups');
        return $resultPage;
    }
}
