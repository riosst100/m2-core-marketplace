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
namespace Lofmp\DeliverySlot\Controller\Marketplace\DeliverySlot;

use Lofmp\DeliverySlot\Model\DeliverySlotsFactory;
use Magento\Framework\View\Result\PageFactory;
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

/**
 * Class Delete
 * @package Lofmp\DeliverySlot\Controller\Marketplace\DeliverySlot
 */
class Delete extends DeliverySlot
{
    const SELLER_RESOURCE = 'Lofmp_DeliverySlot::lofmp_deliveryslot_config';

    protected $deleteSlotFactory;
    protected $managerInterface;

    /**
     * Index constructor.
     * @param Context $context
     * @param Session $customerSession,
     * @param CustomerUrl $customerUrl
     * @param Filter $filter
     * @param SellerFactory $sellerFactory
     * @param Url $url
     * @param DeleteSlotFactory $deleteSlotFactory
     * @param ManagerInterface $managerInterface
     * @param \Lofmp\DeliverySlot\Helper\Data $helperData
     */
    public function __construct(
        Context $context,
        Session $customerSession,
        CustomerUrl $customerUrl,
        Filter $filter,
        SellerFactory $sellerFactory,
        Url $url,
        DeleteSlotFactory $deleteSlotFactory,
        ManagerInterface $managerInterface,
        \Lofmp\DeliverySlot\Helper\Data $helperData
    ) {
        parent::__construct($context, $customerSession, $customerUrl, $filter, $url, $sellerFactory, $helperData);
        $this->deleteSlotFactory = $deleteSlotFactory;
        $this->managerInterface = $managerInterface;
        $this->helperData = $helperData;
    }
    
    public function execute()
    {
        $isActived = $this->isActiveSeler(true);
        if($isActived){
            $slotId = $this->getRequest()->getParam('slot_id');
            if ($slotId) {
                try {
                    $seller = $this->getCurrentSeller();
                    $deleteSlot = $this->deleteSlotFactory->create();
                    $status = $deleteSlot->delete($slotId, $seller->getId());
                    if ($status) {
                        $this->messageManager->addSuccessMessage('Successfully Deleted Slot');
                        $resultRedirect = $this->resultRedirectFactory->create();
                        $resultRedirect->setPath('*/*/*/index');
                        return $resultRedirect;
                    } else {
                        $this->messageManager->addErrorMessage('Unable To Delete The Slot');
                        $resultRedirect = $this->resultRedirectFactory->create();
                        $resultRedirect->setPath('*/*/*/edit', ['slot_id' => $slotId]);
                        return $resultRedirect;
                    }
                } catch (\Exception $e) {
                    $this->messageManager->addErrorMessage('Unable To Delete The Slot');
                    $resultRedirect = $this->resultRedirectFactory->create();
                    $resultRedirect->setPath('*/*/*/edit', ['slot_id' => $slotId]);
                    return $resultRedirect;
                }
            } else {
                $this->messageManager->addErrorMessage('Unable To Delete The Slot');
                $resultRedirect = $this->resultRedirectFactory->create();
                $resultRedirect->setPath('*/*/*/edit', ['slot_id' => $slotId]);
                return $resultRedirect;
            }
        }
    }
}
