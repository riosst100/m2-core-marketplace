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
use Lofmp\DeliverySlot\Model\DeliverySlotGroupFactory;

use Magento\Framework\View\Result\PageFactory;
use Magento\Framework\Registry;

class Edit extends DeliverySlot
{
    const SELLER_RESOURCE = 'Lofmp_DeliverySlot::lofmp_deliveryslot_config';

    protected $resultPageFactory;
    protected $registry;

    /**
     * Index constructor.
     * @param Context $context
     * @param Session $customerSession,
     * @param CustomerUrl $customerUrl
     * @param Filter $filter
     * @param SellerFactory $sellerFactory
     * @param Url $url
     * @param PageFactory $resultPageFactory
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
        Registry $registry,
        \Lofmp\DeliverySlot\Helper\Data $helperData
    ) {
        parent::__construct($context, $customerSession, $customerUrl, $filter, $url, $sellerFactory, $helperData);
        $this->resultPageFactory = $resultPageFactory;
        $this->registry = $registry;
    }

    public function execute()
    {
        $isActived = $this->isActiveSeler(true);
        if ($isActived) {
            $groupId = $this->getRequest()->getParam('group_id');
            if ($groupId) {
                $this->registry->register('group_id', $groupId);
            }
            $resultPage = $this->redirectPage();
            $isExistingRule = (bool)$groupId;
            if ($isExistingRule) {
                $resultPage->getConfig()->getTitle()->prepend(sprintf("Slot Group %s", $groupId));
            } else {
                $resultPage->getConfig()->getTitle()->prepend(__('Delivery Group Slots'));
            }
            return $resultPage;
        }
    }

    public function redirectPage()
    {
        $resultPage = $this->resultPageFactory->create();
        return $resultPage;
    }
}
