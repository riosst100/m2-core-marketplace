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

namespace Lofmp\DeliverySlot\Controller\Marketplace\DeliverySlotGroup;

use Lofmp\DeliverySlot\Model\DeliverySlotGroup;
use Magento\Framework\View\Result\PageFactory;
use Lofmp\DeliverySlot\Model\DeliverySlotsFactory;
use Lofmp\DeliverySlot\Model\ResourceModel\DeliverySlots\SaveSlotFactory;
use Magento\Customer\Model\Session;
use Magento\Customer\Model\Url as CustomerUrl;
use Magento\Framework\Url;
use Magento\Ui\Component\MassAction\Filter;
use Magento\Framework\App\Action\Context;
use Lof\MarketPlace\Model\SellerFactory;
use Lofmp\DeliverySlot\Controller\Marketplace\DeliverySlot;
use Magento\Framework\Controller\ResultFactory;
use Lofmp\DeliverySlot\Model\ResourceModel\DeliverySlots\DeleteSlotFactory;
use Magento\Framework\Message\ManagerInterface;

class Delete extends DeliverySlot
{
    const SELLER_RESOURCE = 'Lofmp_DeliverySlot::lofmp_deliveryslot_config';

    protected $resultPageFactory;
    protected $deletegroupslot;

    /**
     * Index constructor.
     * @param Context $context
     * @param Session $customerSession,
     * @param CustomerUrl $customerUrl
     * @param Filter $filter
     * @param SellerFactory $sellerFactory
     * @param Url $url
     * @param PageFactory $resultPageFactory
     * @param \Lofmp\DeliverySlot\Model\DeliverySlotGroup\DeleteGroupSlot $deletegroupslot
     * @param \Lofmp\DeliverySlot\Helper\Data $helperData
     */
    public function __construct(
        Context $context,
        Session $customerSession,
        CustomerUrl $customerUrl,
        Filter $filter,
        SellerFactory $sellerFactory,
        Url $url,
        PageFactory $resultPageFactory,
        \Lofmp\DeliverySlot\Model\DeliverySlotGroup\DeleteGroupSlot $deletegroupslot,
        \Lofmp\DeliverySlot\Helper\Data $helperData
    ) {
        parent::__construct($context, $customerSession, $customerUrl, $filter, $url, $sellerFactory, $helperData);
        $this->resultPageFactory = $resultPageFactory;
        $this->deletegroupslot = $deletegroupslot;
    }

    public function execute()
    {
        $isActived = $this->isActiveSeler(true);
        if ($isActived) {
            $groupId = $this->getRequest()->getParam('group_id');
            if ($groupId) {
                $seller = $this->getCurrentSeller();
                try {
                    $result = $this->deletegroupslot->delete($groupId, $seller->getId());
                    $existingdata = (bool)$result;
                    $resultRedirect = $this->resultRedirectFactory->create();
                    if ($existingdata) {
                        $this->messageManager->addSuccessMessage('SucessFully Deleted Group Slots');
                        $resultpage = $resultRedirect->setPath('*/*/*/index');
                    } else {
                        $this->messageManager->addSuccessMessage('Unable Delete this Group Slot');
                        $resultpage = $resultRedirect->setPath('*/*/*/edit', ['group_id' => $groupId]);
                    }
                    return $resultpage;
                } catch (\Exception $e) {
                    $this->messageManager->addSuccessMessage('Unable Delete this Group Slot');
                    $resultpage = $resultRedirect->setPath('*/*/*/edit', ['group_id' => $groupId]);
                    return $resultpage;
                }
            }
        }
    }
}
