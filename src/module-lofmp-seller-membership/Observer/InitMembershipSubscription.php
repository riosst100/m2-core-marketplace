<?php
/**
 * Landofcoder
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Landofcoder.com license that is
 * available through the world-wide-web at this URL:
 * https://landofcoder.com/terms
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade this extension to newer
 * version in the future.
 *
 * @category   Landofcoder
 * @package    Lofmp_SellerMembership
 * @copyright  Copyright (c) 2021 Landofcoder (https://www.landofcoder.com/)
 * @license    https://landofcoder.com/terms
 */

namespace Lofmp\SellerMembership\Observer;

use Lof\MarketPlace\Model\GroupFactory;
use Magento\Framework\Event\ObserverInterface;
use Magento\Framework\Serialize\SerializerInterface;
use Magento\Framework\App\ObjectManager;

class InitMembershipSubscription implements ObserverInterface
{
    const DEFAULT_DURATION_MONTH = 1;

    /**
     * @var \Magento\Customer\Model\CustomerFactory
     */
    protected $_customerFactory;

    /**
     * @var \Lofmp\SellerMembership\Model\TransactionFactory
     */
    protected $_transactionFactory;

    /**
     * @var \Magento\Framework\Stdlib\DateTime\DateTime
     */
    protected $_date;

    /**
     * @var \Lofmp\SellerMembership\Model\Membership
     */
    protected $membership;

    /**
     * @var GroupFactory
     */
    protected $_groupFactory;

    /**
     * @var \Lofmp\SellerMembership\Helper\Data
     */
    protected $_helperData;

    /**
     * @var SerializerInterface
     */
    protected $serializer;

    /**
     * @var \Magento\Framework\ObjectManagerInterface $objectManager
     */
    protected $objectManager;

    /**
     * @var \Magento\Framework\DB\Transaction
     */
    protected $_transaction;

    /**
     * @var \Lof\MarketPlace\Helper\Data
     */
    protected $helper;

    /**
     * @var \Lof\MarketPlace\Model\SellerFactory
     */
    protected $_sellerFactory;

    /**
     * @var mixed|array
     */
    protected $_membershipGroupId = [];

    /**
     * InvoiceSaveAfterObserver constructor.
     * @param \Magento\Customer\Model\CustomerFactory $customerFactory
     * @param \Lofmp\SellerMembership\Model\Membership $membership
     * @param \Lofmp\SellerMembership\Model\TransactionFactory $transactionFactory
     * @param \Magento\Framework\Stdlib\DateTime\DateTime $date
     * @param GroupFactory $groupFactory
     * @param \Lofmp\SellerMembership\Helper\Data $helperData
     * @param \Magento\Framework\ObjectManagerInterface $objectManager
     * @param \Magento\Framework\DB\Transaction $transaction
     * @param \Lof\MarketPlace\Model\SellerFactory $sellerFactory
     * @param \Lof\MarketPlace\Helper\Data $helper
     * @param SerializerInterface|null
     */
    public function __construct(
        \Magento\Customer\Model\CustomerFactory $customerFactory,
        \Lofmp\SellerMembership\Model\Membership $membership,
        \Lofmp\SellerMembership\Model\TransactionFactory $transactionFactory,
        \Magento\Framework\Stdlib\DateTime\DateTime $date,
        GroupFactory $groupFactory,
        \Lofmp\SellerMembership\Helper\Data $helperData,
        \Magento\Framework\ObjectManagerInterface $objectManager,
        \Magento\Framework\DB\Transaction $transaction,
        \Lof\MarketPlace\Model\SellerFactory $sellerFactory,
        \Lof\MarketPlace\Helper\Data $helper,
        SerializerInterface $serializer = null
    ) {
        $this->helper = $helper;
        $this->membership = $membership;
        $this->_transactionFactory = $transactionFactory;
        $this->_customerFactory = $customerFactory;
        $this->_date = $date;
        $this->_groupFactory = $groupFactory;
        $this->_helperData = $helperData;
        $this->_sellerFactory = $sellerFactory;
        $this->serializer = $serializer ?: ObjectManager::getInstance()->get(SerializerInterface::class);
        $this->_objectManager = $objectManager;
        $this->_transaction = $transaction;
    }

    /**
     * Add the notification if there are any customer awaiting for approval.
     *
     * @param \Magento\Framework\Event\Observer $observer
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function execute(\Magento\Framework\Event\Observer $observer)
    {
        if ($this->_helperData->isEnabled()) {
            $orderids = $observer->getEvent()->getOrderIds();
            if (count($orderids) > 0 ) {
                foreach ($orderids as $_orderid) {
                    /*Buy membership package*/
                    $order = $this->_objectManager->create('Magento\Sales\Model\Order')->load($_orderid);
                    $this->processBuyMembership($order);
                }
            }
        }
    }

    /**
     * Process buy membership transaction.
     *
     * @param \Magento\Sales\Model\Order $order
     */
    public function processBuyMembership(\Magento\Sales\Model\Order $order)
    {
        $customerId = $order->getCustomerId();

        if (!$customerId) {
            return;
        }

        $customer = $this->_customerFactory->create();
        $customer->load($customerId);

        if (!$customer->getId()) {
            return;
        }

        $seller = $this->_sellerFactory->create();
        $seller->load($customer->getId(), 'customer_id');

        if (!$seller->getId()) {
            return;
        }

        /*Return if the transaction for the invoice is already exist.*/
        $trans = $this->_transactionFactory->create()->getCollection()
            ->addFieldToFilter(
                'order_id', (int)$order->getEntityId()
            );

        if ($trans->count()) {
            return;
        }

        foreach ($order->getAllItems() as $item) {
            $orderItem = $item;//->getOrderItem();
            if ($orderItem->getParentItemId()) {
                continue;
            }

            if ($orderItem->getProductType() != 'seller_membership') {
                continue;
            }

            $product = $orderItem->getProduct();
            if (!$product) {
                continue;
            }

            $membershipOptionByRequest = $orderItem->getProductOptions();

            if (!is_array($membershipOptionByRequest)) {
                $membershipOptionByRequest = $this->serializer->unserialize($membershipOptionByRequest);
            }
            $membershipOption = $membershipOptionByRequest['info_buyRequest']['seller_membership'];
            $membershipOption = explode("|", $membershipOption['duration']);
            $relatedGroupId = (int)$product->getData('seller_group');
            $duration = isset($membershipOption[0]) && $membershipOption[0] ? (int)$membershipOption[0] : self::DEFAULT_DURATION_MONTH;
            $durationUnit = isset($membershipOption[1]) && trim($membershipOption[1]) ? trim($membershipOption[1]) : 'month';
            $price = $orderItem->getPrice();

            if (!$relatedGroupId || !$duration || !$durationUnit) {
                continue;
            }

            $time = '';
            $duration = $duration * $orderItem->getQty();

            switch ($durationUnit) {
                case 'day':
                    $time = "+$duration days";
                    break;
                case 'week':
                    $duration = $duration * 7;
                    $time = "+$duration days";
                    break;
                case 'year':
                    $time = "+$duration years";
                    break;
                case 'month':
                default:
                    $time = "+$duration months";
                    break;
            }

            $membership = $this->membership->getCollection()->addFieldToFilter(
                'seller_id',
                $seller->getId()
            )->getFirstItem();

            if ($seller->getGroupId() == $relatedGroupId) {
                /*Renew the current package*/
                $currentTime = $membership->getData('expiration_date');
                if (!$currentTime) {
                    $currentTime = $this->_helperData->getTimezoneDateTime();
                }
            } else {
                /*Upgrade to new package*/
                $currentTime = $this->_helperData->getTimezoneDateTime();
            }
            $expiryTime = strtotime($currentTime . $time);
            $date = date('Y-m-d h:i:s A', $expiryTime);
            try {
                $productOptions = $orderItem->getProductOptions();
                $productOptions = is_array($productOptions)?$this->serializer->serialize($productOptions):$productOptions;

                $membership->setGroupId($relatedGroupId);
                $membership->setSeller($seller->getId());
                $membership->setExpirationDate($date);
                $membership->setStatus(\Lofmp\SellerMembership\Model\Membership::DISABLE);
                $membership->setDuration($duration . ' ' . $durationUnit);
                $membership->setPrice($price);
                $membership->setName($orderItem->getName());
                $membership->setProductId($orderItem->getProductId());
                $membership->setItemId($orderItem->getId());
                $membership->setProductOptions($productOptions);
                $membership->save();

                $trans = $this->_transactionFactory->create();
                $trans->setData([
                    'seller_id' => $seller->getId(),
                    'package' => $orderItem->getName(),
                    'amount' => $orderItem->getBaseRowTotal(),
                    'duration' => $duration,
                    'duration_unit' => $durationUnit,
                    'additional_info' => 'order|' . $order->getEntityId() . '||item|' . $orderItem->getId(),
                    'created_at' => $this->_date->timestamp(),
                    'group_id' => $order->getCustomerGroupId(),
                    'order_id' => $order->getEntityId(),
                    'order_increment_id' => $order->getIncrementId(),
                    'product_options' => $productOptions,
                    'product_id' => $orderItem->getProductId(),
                    'item_id' => $orderItem->getId()
                ]);
                $trans->save();
            } catch(\Exception $e) {
                $this->_helperData->writeLog([$e->getMessage()]);
            }
        }
    }
}
