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

use Magento\Framework\View\Result\PageFactory;
use Lofmp\DeliverySlot\Model\ResourceModel\DeliverySlots\SaveSlotFactory;
use Magento\Customer\Model\Session;
use Magento\Customer\Model\Url as CustomerUrl;
use Magento\Framework\Url;
use Magento\Ui\Component\MassAction\Filter;
use Magento\Framework\App\Action\Context;
use Lof\MarketPlace\Model\SellerFactory;
use Lofmp\DeliverySlot\Controller\Marketplace\DeliverySlot;
use Lofmp\DeliverySlot\Model\DeliverySlotsFactory;

/**
 * Class Save
 * @package Lofmp\DeliverySlot\Controller\Marketplace\DeliverySlot
 */
class Save extends DeliverySlot
{
    protected $resultPageFactory;
    protected $sessionManager;
    protected $messageManager;
    protected $saveSlotFactory;
    protected $deliverySlotsFactory;

    /**
     * Index constructor.
     * @param Context $context
     * @param Session $customerSession,
     * @param CustomerUrl $customerUrl
     * @param Filter $filter
     * @param SellerFactory $sellerFactory
     * @param Url $url
     * @param PageFactory $resultPageFactory
     * @param SaveSlotFactory $saveSlotFactory
     * @param DeliverySlotsFactory $deliverySlotsFactory
     * @param \Magento\Framework\Session\SessionManagerInterface $sessionManager
     * @param \Magento\Framework\Message\ManagerInterface $messageManager
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
        SaveSlotFactory $saveSlotFactory,
        DeliverySlotsFactory $deliverySlotsFactory,
        \Magento\Framework\Session\SessionManagerInterface $sessionManager,
        \Magento\Framework\Message\ManagerInterface $messageManager,
        \Lofmp\DeliverySlot\Helper\Data $helperData
    ) {
        parent::__construct($context, $customerSession, $customerUrl, $filter, $url, $sellerFactory, $helperData);
        $this->resultPageFactory = $resultPageFactory;
        $this->sessionManager = $sessionManager;
        $this->messageManager = $messageManager;
        $this->saveSlotFactory = $saveSlotFactory;
        $this->deliverySlotsFactory = $deliverySlotsFactory;
    }

    /**
     * @return \Magento\Framework\App\ResponseInterface|\Magento\Framework\Controller\ResultInterface|\Magento\Framework\View\Result\Page
     */
    public function execute()
    {
        $isActived = $this->isActiveSeler(true);
        if ($isActived) {
            $resultRedirect = $this->resultRedirectFactory->create();
            $seller = $this->getCurrentSeller();
            $seller_id = $seller?$seller->getId():0;
            $slotId = $this->getRequest()->getParam('slot_id');
            $startTime = $this->getRequest()->getParam('start_time');
            $endTime = $this->getRequest()->getParam('end_time');
            $diff = (strtotime($endTime) - strtotime($startTime));
            $hoursDiff = $diff/60;
            if ($hoursDiff <= 0) {
                $this->messageManager->addErrorMessage('Start Time Should Be Less than End Time');
                $resultRedirect->setPath('deliveryslot/deliveryslot/edit', ['slot_id' => $slotId]);
                return $resultRedirect;
            }
            $slotValues = $this->getRequest()->getParams();
            $saveSlotModel = $this->saveSlotFactory->create();
            $deliverySlotModel = $this->deliverySlotsFactory->create()->load((int)$slotId);
            try {
                $allow_save = true;
                if($deliverySlotModel->getId() && $deliverySlotModel->getSellerId() != $seller_id){
                    $allow_save = false;
                }
                if($allow_save){
                    $slotValues['seller_id'] = $seller_id;
                    $slotData = $saveSlotModel->save($slotValues);
                    if (!empty($slotData)) {
                        $returnToEdit = (bool)$this->getRequest()->getParam('back', false);
                        if ($returnToEdit) {
                            if ($slotId == null) {
                                $this->messageManager->addSuccessMessage('Successfully Added New Slot');
                                $resultRedirect->setPath('deliveryslot/deliveryslot/edit', ['slot_id' => $slotData->getId()]);
                                return $resultRedirect;
                            } else {
                                $this->messageManager->addSuccessMessage('Successfully Updated  Slot');
                                $resultRedirect->setPath('deliveryslot/deliveryslot/edit', ['slot_id' => $slotId]);
                                return $resultRedirect;
                            }
                        }
                    } else {
                        $this->messageManager->addErrorMessage('Unable to Add Slot');
                        $resultRedirect->setPath('deliveryslot/deliveryslot/index');
                        return $resultRedirect;
                    }
                }
            } catch (\Exception $e) {
                $this->messageManager->addErrorMessage('Unable to Add Slot');
                $resultRedirect->setPath('deliveryslot/deliveryslot/index');
                return $resultRedirect;
            }
            $this->messageManager->addSuccessMessage('Successfully Added New Slot');
            $resultRedirect->setPath('deliveryslot/deliveryslot/index');
            return $resultRedirect;
        }
    }
}
