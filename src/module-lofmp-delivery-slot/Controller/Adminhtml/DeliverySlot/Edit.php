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
use Magento\Framework\View\Result\PageFactory;
use Lofmp\DeliverySlot\Model\DeliverySlotFactory;
use Psr\Log\LoggerInterface;
use Magento\Framework\Registry;

/**
 * Class Edit
 * @package Lofmp\DeliverySlot\Controller\Adminhtml\DeliverySlot
 */
class Edit extends \Magento\Backend\App\Action
{
    const ADMIN_RESOURCE = 'Lofmp_DeliverySlot::lofmp_deliveryslot_config';

    /**
     * Edit constructor.
     * @param Context $context
     * @param PageFactory $resultPageFactory
     * @param DeliverySlotFactory $deliverySlotFactory
     * @param Registry $registry
     * @param LoggerInterface $logger
     */
    public function __construct(
        Context $context,
        PageFactory $resultPageFactory,
        DeliverySlotFactory $deliverySlotFactory,
        Registry $registry,
        LoggerInterface $logger
    ) {
        parent::__construct($context);
        $this->resultPageFactory = $resultPageFactory;
        $this->deliverySlotFactory = $deliverySlotFactory;
        $this->coreRegistry = $registry;
        $this->logger = $logger;
    }

    /**
     * Init layout, menu and breadcrumb
     *
     * @return \Magento\Backend\Model\View\Result\Page
     */
    protected function _initAction()
    {
        $resultPage = $this->resultPageFactory->create();
        $resultPage->setActiveMenu('Lofmp_DeliverySlot::slots');
        $resultPage->addBreadcrumb(__('Slots'), __('Delivery Slots'));
        return $resultPage;
    }

    /**
     * @return \Magento\Backend\Model\View\Result\Page|\Magento\Framework\App\ResponseInterface|\Magento\Framework\Controller\Result\Redirect|\Magento\Framework\Controller\ResultInterface
     */
    public function execute()
    {
        $slotId = $this->_initSlot();
        $resultPage = $this->_initAction();

        if (!empty($slotId) && isset($slotId)) {
            $resultPage->getConfig()->getTitle()->prepend(sprintf("#Slot %s", $slotId));
        } else {
            $resultPage->getConfig()->getTitle()->prepend(__('Add New Slot'));
        }

        return $resultPage;
    }

    /**
     * @return int
     */
    public function _initSlot()
    {
        $slotId = (int)$this->getRequest()->getParam('slot_id');
        if ($slotId) {
            $this->coreRegistry->register('slot_id', $slotId);
        }
        return $slotId;
    }
}
