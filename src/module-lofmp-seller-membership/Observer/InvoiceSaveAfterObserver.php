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

use Magento\Framework\Event\ObserverInterface;
use Magento\Framework\Serialize\SerializerInterface;
use Magento\Framework\App\ObjectManager;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Exception\NoSuchEntityException;
use Lof\MarketPlace\Api\SellerGroupRepositoryInterface;
use Lof\MarketPlace\Model\GroupFactory;

class InvoiceSaveAfterObserver implements ObserverInterface
{
    const DEFAULT_DURATION_MONTH = 1;
    /**
     * @var SellerGroupRepositoryInterface
     */
    private $groupRepository;

    /**
     * @var \Magento\Customer\Model\CustomerFactory
     */
    protected $_customerFactory;

    /**
     * @var \Lof\MarketPlace\Model\SellerFactory
     */
    protected $_sellerFactory;

    /**
     * @var \Lofmp\SellerMembership\Model\TransactionFactory
     */
    protected $_transactionFactory;

    /**
     * @var \Magento\Framework\Stdlib\DateTime\DateTime
     */
    protected $_date;

    /**
     * @var \Lofmp\SellerMembership\Model\MembershipFactory
     */
    protected $membershipFactory;

    /**
     * @var \Lof\MarketPlace\Helper\Data
     */
    protected $helper;

    /**
     * @var \Magento\Framework\Event\ManagerInterface
     */
    protected $_eventManager;

    /**
     * @var \Magento\Framework\ObjectManagerInterface
     */
    protected $_objectManager;

    /**
     * @var \Lofmp\SellerMembership\Helper\Data
     */
    protected $_helperData;

    /**
     * @var SerializerInterface
     */
    protected $serializer;

    /**
     * @var \Magento\Catalog\Model\ResourceModel\Product\CollectionFactory
     */
    protected $_productCollectionFactory;

    /**
     * @var GroupFactory
     */
    protected $_sellerGroupFactory;

    /**
     * InvoiceSaveAfterObserver constructor.
     * @param \Magento\Customer\Model\CustomerFactory $customerFactory
     * @param \Lof\MarketPlace\Model\SellerFactory $sellerFactory
     * @param \Lofmp\SellerMembership\Model\MembershipFactory $membershipFactory
     * @param \Lofmp\SellerMembership\Model\TransactionFactory $transactionFactory
     * @param \Lof\MarketPlace\Helper\Data $helper
     * @param \Magento\Framework\Stdlib\DateTime\DateTime $date
     * @param \Magento\Framework\Event\ManagerInterface $eventManager
     * @param \Magento\Framework\ObjectManagerInterface $objectManager
     * @param \Lofmp\SellerMembership\Helper\Data $_helperData
     * @param \Magento\Catalog\Model\ResourceModel\Product\CollectionFactory $productCollectionFactory
     * @param GroupFactory $sellerGroupFactory
     * @param SerializerInterface|null $serializer
     * @param SellerGroupRepositoryInterface|null $groupRepository
     */
    public function __construct(
        \Magento\Customer\Model\CustomerFactory $customerFactory,
        \Lof\MarketPlace\Model\SellerFactory $sellerFactory,
        \Lofmp\SellerMembership\Model\MembershipFactory $membershipFactory,
        \Lofmp\SellerMembership\Model\TransactionFactory $transactionFactory,
        \Lof\MarketPlace\Helper\Data $helper,
        \Magento\Framework\Stdlib\DateTime\DateTime $date,
        \Magento\Framework\Event\ManagerInterface $eventManager,
        \Magento\Framework\ObjectManagerInterface $objectManager,
        \Lofmp\SellerMembership\Helper\Data $helperData,
        \Magento\Catalog\Model\ResourceModel\Product\CollectionFactory $productCollectionFactory,
        GroupFactory $sellerGroupFactory,
        SerializerInterface $serializer = null,
        SellerGroupRepositoryInterface $groupRepository = null
    ) {
        $this->helper = $helper;
        $this->membershipFactory = $membershipFactory;
        $this->_transactionFactory = $transactionFactory;
        $this->_sellerFactory = $sellerFactory;
        $this->_customerFactory = $customerFactory;
        $this->_date = $date;
        $this->_eventManager = $eventManager;
        $this->_objectManager = $objectManager;
        $this->_helperData = $helperData;
        $this->serializer = $serializer ?: ObjectManager::getInstance()->get(SerializerInterface::class);
        $this->groupRepository = $groupRepository ?: ObjectManager::getInstance()->get(SellerGroupRepositoryInterface::class);
        $this->_productCollectionFactory = $productCollectionFactory;
        $this->_sellerGroupFactory = $sellerGroupFactory;
    }

    /**
     * Add the notification if there are any seller awaiting for approval.
     *
     * @param \Magento\Framework\Event\Observer $observer
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function execute(\Magento\Framework\Event\Observer $observer)
    {
        if ($this->_helperData->isEnabled()) {
            $invoice = $observer->getInvoice();
            //$order = $invoice->getOrder();

            /*Return if the invoice is not paid*/
            if ($invoice->getState() != \Magento\Sales\Model\Order\Invoice::STATE_PAID) {
                return;
            }

            /*Buy membership package*/
            $this->processBuyMembership($invoice);
        }
    }

    /**
     * Process buy membership transaction.
     *
     * @param \Magento\Sales\Model\Order\Invoice $invoice
     */
    public function processBuyMembership(\Magento\Sales\Model\Order\Invoice $invoice)
    {
        $order = $invoice->getOrder();

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
                'additional_info',
                ['like' => 'invoice|' . $invoice->getId()]
            );

        if ($trans->count()) {
            return;
        }

        foreach ($invoice->getAllItems() as $item) {
            $orderItem = $item->getOrderItem();
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
            $group = $this->_sellerGroupFactory->create()->load($relatedGroupId);
            if (count($group->getData()) == 0) {
                continue;
            }

            $duration = isset($membershipOption[0]) && $membershipOption[0] ? (int)$membershipOption[0] : self::DEFAULT_DURATION_MONTH;
            $durationUnit = isset($membershipOption[1]) && trim($membershipOption[1]) ? trim($membershipOption[1]) : 'month';
            $price = $orderItem->getPrice();

            //$duration = $membershipOption[0];
            //$durationUnit = $membershipOption[1];
            //$price = $membershipOption[2];

            if (!$relatedGroupId || !$duration || !$durationUnit) {
                continue;
            }

            if (!$this->validateGroupId($relatedGroupId)) {
                continue;
            }

            $time = '';
            $duration = $duration * $item->getQty();

            switch ($durationUnit) {
                case 'day':
                    $time = "+$duration days";
                    break;
                case 'week':
                    $duration = $duration * 7;
                    $time = "+$duration days";
                    break;
                case 'month':
                    $time = "+$duration months";
                    break;
                case 'year':
                    $time = "+$duration years";
                    break;
            }

            $membership = $this->membershipFactory->create()->getCollection()
                ->addFieldToFilter('seller_id', $seller->getId())
                ->getFirstItem();

            if (count($membership->getData()) == 0) {
                $membership = $this->createDefaultMembership($seller->getId());
            }

            if ($seller->getGroupId() == $relatedGroupId) {
                /*Renew the current package*/
                $currentTime = $membership ? $membership->getData('expiration_date') : 0;
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

                //upgrade membership plan
                $membership->setGroupId($relatedGroupId);
                $membership->setSellerId($seller->getId());
                $membership->setExpirationDate($date);
                $membership->setStatus(\Lofmp\SellerMembership\Model\Membership::ENABLE);
                $membership->setDuration($duration . ' ' . $durationUnit);
                $membership->setPrice($price);
                $membership->setName($item->getName());
                $membership->setProductId($orderItem->getProductId());
                $membership->setItemId($orderItem->getId());
                $membership->setProductOptions($productOptions);
                $membership->save();

                $this->_eventManager->dispatch(
                    'controller_action_lofmp_seller_membership_membership_save_after',
                    ['controller' => $this, 'membership' => $membership, 'group' => $group]
                );

                //upgrade transaction
                $transCollection = $this->_transactionFactory->create()->getCollection()
                                    ->addFieldToFilter("seller_id", $seller->getId())
                                    ->addFieldtoFilter("item_id", $orderItem->getId())
                                    ->addFieldtoFilter("order_id", $order->getEntityId());

                $trans = $this->_transactionFactory->create();
                if ($transCollection->count()) {
                    $transId = $transCollection->getFirstItem()->getData("transaction_id");
                    $trans = $this->_transactionFactory->create()->load((int)$transId);
                }

                $trans->setData([
                    'seller_id' => $seller->getId(),
                    'package' => $item->getName(),
                    'amount' => $item->getBaseRowTotal(),
                    'duration' => $duration,
                    'duration_unit' => $durationUnit,
                    'additional_info' => 'invoice|' . $invoice->getId() . '||item|' . $orderItem->getId(),
                    'created_at' => $this->_date->timestamp(),
                    'group_id' => $order->getCustomerGroupId(),
                    'order_id' => $order->getEntityId(),
                    'order_increment_id' => $order->getIncrementId(),
                    'product_options' => $productOptions,
                    'product_id' => $orderItem->getProductId(),
                    'item_id' => $orderItem->getId()
                ]);
                $trans->save();

                $this->_eventManager->dispatch(
                    'controller_action_lofmp_seller_membership_transaction_save_after',
                    ['controller' => $this, 'transaction' => $trans]
                );

                $seller->setGroupId($relatedGroupId);
                $seller->save();

                $this->_eventManager->dispatch(
                    'controller_action_lofmp_seller_membership_seller_save_after',
                    ['controller' => $this, 'seller' => $seller]
                );
            } catch(\Exception $e) {
                $this->_helperData->writeLog([$e->getMessage()]);
            }
        }
    }

    /**
     * @param $seller_id
     * @return \Magento\Framework\DataObject
     */
    public function createDefaultMembership($seller_id)
    {
        $data = [];
        $group_id = $this->helper->getConfig('seller_settings/default_seller_group');
        $group = $this->_sellerGroupFactory->create()->load($group_id);
        $membershipModel = $this->membershipFactory->create();
        if ($group && $group->getGroupId()) {
            try{
                $data['seller_id'] = $seller_id;
                $data['group_id'] = $group_id;
                $collection = $this->_productCollectionFactory->create()
                                ->addAttributeToFilter('seller_group', $group_id)
                                ->getFirstItem();
                $product = $this->_objectManager->create('Magento\Catalog\Model\Product')->load($collection->getId());
                $productDuration = $product->getSellerDuration();
                $productDuration = isset($productDuration[0]) ? $productDuration[0] : [];

                $data['name'] = $product->getName();
                $data['price'] = !empty($productDuration) ? $productDuration['membership_price'] : $product->getPrice();
                $data['duration'] = !empty($productDuration) ? ($productDuration['membership_duration'] . ' ' . $productDuration['membership_unit']) : self::DEFAULT_DURATION_MONTH;

                $expiration_date = $this->_helperData->getExpirationDate(
                    !empty($productDuration) ? $productDuration['membership_duration'] : self::DEFAULT_DURATION_MONTH,
                    !empty($productDuration) ? $productDuration['membership_unit'] : "month"
                );
                $time = time() + $expiration_date;
                $date = date('Y-m-d h:i:s A', $time);
                $data['expiration_date'] = $date;
                $membershipModel->setData($data)->save();

                $this->_eventManager->dispatch(
                    'controller_action_lofmp_seller_membership_default_save_after',
                    ['controller' => $this, 'membership' => $membershipModel, 'group' => $group, 'product' => $product]
                );
            } catch(\Exception $e) {
                $this->_helperData->writeLog([$e->getMessage()]);
            }
        }

        return $membershipModel->getCollection()
            ->addFieldToFilter('seller_id', $seller_id)
            ->getFirstItem();
    }

    /**
     * Validate customer group id if exist
     *
     * @param string|int|null $groupId
     * @return bool
     * @throws LocalizedException
     */
    private function validateGroupId($groupId): bool
    {
        if ($groupId) {
            try {
                $this->groupRepository->get($groupId);
            } catch (NoSuchEntityException $e) {
                //throw new LocalizedException(__('The specified customer group id does not exist.'));
                return false;
            }
        }
        return true;
    }
}
