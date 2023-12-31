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

use Lof\MarketPlace\Model\SellerFactory;
use Magento\Customer\Model\Session;
use Magento\Customer\Model\Url as CustomerUrl;
use Magento\Framework\Url;
use Magento\Ui\Component\MassAction\Filter;
use Magento\Framework\App\Action\Context;
use Lofmp\DeliverySlot\Controller\Marketplace\DeliverySlot;

use Magento\Framework\View\Result\PageFactory;
use Lofmp\DeliverySlot\Model\DeliverySlotGroupFactory;

/**
 * Class Index
 * @package Lofmp\DeliverySlot\Controller\Marketplace\DeliverySlotGroup
 */
class Index extends DeliverySlot
{
    const SELLER_RESOURCE = 'Lofmp_DeliverySlot::lofmp_deliveryslot_config';

    /**
     * @var DeliverySlotGroupFactory
     */
    protected $deliverySlotGroupFactory;

    protected $resultPageFactory;
    
    /**
     * Index constructor.
     * @param Context $context
     * @param Session $customerSession,
     * @param CustomerUrl $customerUrl
     * @param Filter $filter
     * @param SellerFactory $sellerFactory
     * @param Url $url
     * @param PageFactory $resultPageFactory
     * @param DeliverySlotGroupFactory $deliverySlotGroupFactory
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
        DeliverySlotGroupFactory $deliverySlotGroupFactory,
        \Lofmp\DeliverySlot\Helper\Data $helperData
    ) {
        parent::__construct($context, $customerSession, $customerUrl, $filter, $url, $sellerFactory, $helperData);
        $this->resultPageFactory = $resultPageFactory;
        $this->deliverySlotGroupFactory = $deliverySlotGroupFactory;
    }

    public function execute()
    {
        $isActived = $this->isActiveSeler(true);
        if ($isActived) {
            $resultPage = $this->resultPageFactory->create();
            $resultPage->getConfig()->getTitle()->prepend((__('Slot Groups')));
            return $resultPage;
        }
    }
}
