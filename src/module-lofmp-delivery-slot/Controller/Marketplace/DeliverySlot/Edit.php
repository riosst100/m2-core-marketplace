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

use Magento\Customer\Model\Session;
use Magento\Customer\Model\Url as CustomerUrl;
use Magento\Framework\Url;
use Magento\Ui\Component\MassAction\Filter;
use Magento\Framework\App\Action\Context;
use Lof\MarketPlace\Model\SellerFactory;
use Lofmp\DeliverySlot\Controller\Marketplace\DeliverySlot;
use Lofmp\DeliverySlot\Model\DeliverySlotFactory;
use Psr\Log\LoggerInterface;
use Magento\Framework\Registry;

use Magento\Framework\View\Result\PageFactory;

/**
 * Class Edit
 * @package Lofmp\DeliverySlot\Controller\Marketplace\DeliverySlot
 */
class Edit extends DeliverySlot
{
    const SELLER_RESOURCE = 'Lofmp_DeliverySlot::lofmp_deliveryslot_config';

    protected $resultPageFactory;
    protected $deliverySlotFactory;
    protected $coreRegistry;
    protected $logger;

    /**
     * Index constructor.
     * @param Context $context
     * @param Session $customerSession,
     * @param CustomerUrl $customerUrl
     * @param Filter $filter
     * @param SellerFactory $sellerFactory
     * @param Url $url
     * @param PageFactory $resultPageFactory
     * @param DeliverySlotFactory $deliverySlotFactory
     * @param Registry $registry
     * @param LoggerInterface $logger
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
        DeliverySlotFactory $deliverySlotFactory,
        Registry $registry,
        LoggerInterface $logger,
        \Lofmp\DeliverySlot\Helper\Data $helperData
    ) {
        parent::__construct($context, $customerSession, $customerUrl, $filter, $url, $sellerFactory, $helperData);
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
        return $resultPage;
    }

    /**
     * @return \Magento\Backend\Model\View\Result\Page|\Magento\Framework\App\ResponseInterface|\Magento\Framework\Controller\Result\Redirect|\Magento\Framework\Controller\ResultInterface
     */
    public function execute()
    {
        $isActived = $this->isActiveSeler(true);
        if ($isActived) {
            $slotId = $this->_initSlot();
            $resultPage = $this->_initAction();

            if (!empty($slotId) && isset($slotId)) {
                $resultPage->getConfig()->getTitle()->prepend(sprintf("#Slot %s", $slotId));
            } else {
                $resultPage->getConfig()->getTitle()->prepend(__('Add New Slot'));
            }

            return $resultPage;
        }
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
