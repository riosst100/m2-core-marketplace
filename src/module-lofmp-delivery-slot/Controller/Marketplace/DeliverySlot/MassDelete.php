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

/**
 * Class MassDelete
 * @package Lofmp\DeliverySlot\Controller\Marketplace\DeliverySlot
 */
class MassDelete extends DeliverySlot
{
    const SELLER_RESOURCE = 'Lofmp_DeliverySlot::lofmp_deliveryslot_config';

    protected $deliverySlots;
    protected $collectionFactory;
    /**
     * Index constructor.
     * @param Context $context
     * @param Session $customerSession,
     * @param CustomerUrl $customerUrl
     * @param Filter $filter
     * @param SellerFactory $sellerFactory
     * @param Url $url
     * @param \Lofmp\DeliverySlot\Model\ResourceModel\DeliverySlots\CollectionFactory $collectionFactory
     * @param \Lofmp\DeliverySlot\Model\DeliverySlots $deliverySlots
     * @param \Lofmp\DeliverySlot\Helper\Data $helperData
     */
    public function __construct(
        Context $context,
        Session $customerSession,
        CustomerUrl $customerUrl,
        Filter $filter,
        SellerFactory $sellerFactory,
        Url $url,
        \Lofmp\DeliverySlot\Model\ResourceModel\DeliverySlots\CollectionFactory $collectionFactory,
        \Lofmp\DeliverySlot\Model\DeliverySlots $deliverySlots,
        \Lofmp\DeliverySlot\Helper\Data $helperData
    ) {
        parent::__construct($context, $customerSession, $customerUrl, $filter, $url, $sellerFactory, $helperData);
        $this->deliverySlots = $deliverySlots;
        $this->collectionFactory = $collectionFactory;
    }

    /**
     * Index action
     *
     * @return \Magento\Backend\Model\View\Result\Page
     */
    public function execute()
    {
        $isActived = $this->isActiveSeler(true);
        if ($isActived) {
            $collection = $this->filter->getCollection($this->collectionFactory->create());
            $collectionSize = $collection->getSize();
            $recordDelete = 0;
            $seller = $this->getCurrentSeller();
            foreach ($collection as $item) {
                if($seller->getId() == $item->getSellerId()){
                    $item->delete();
                    $recordDelete++;
                }
            }
            $this->messageManager->addSuccess(__('A total of %1 record(s) have been deleted.', $recordDelete));

            /** @var \Magento\Backend\Model\View\Result\Redirect $resultRedirect */
            $resultRedirect = $this->resultFactory->create(ResultFactory::TYPE_REDIRECT);
            return $resultRedirect->setPath('*/*/*/');
        }
    }
}
