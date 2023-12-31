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

namespace Lofmp\DeliverySlot\Model;

use Lofmp\DeliverySlot\Api\SellerDeliverySlotInterface;
use Lofmp\DeliverySlot\Api\Data\SellerDeliverySlotSearchResultsInterfaceFactory;
use Lofmp\DeliverySlot\Api\Data\SellerDeliverySlotSearchResultsInterface;
use Magento\Framework\Api\DataObjectHelper;
use Lofmp\DeliverySlot\Helper\Data;
use Magento\Framework\Serialize\SerializerInterface;
use Lofmp\DeliverySlot\Model\ResourceModel\DeliverySlots\CollectionFactory;
use Lofmp\DeliverySlot\Model\ResourceModel\DeliverySlotGroup\CollectionFactory as DeliverSlotGroupCollection;
use Magento\Framework\Stdlib\DateTime\DateTime;
use Magento\Sales\Model\ResourceModel\Order\CollectionFactory as OrderCollection;
use Magento\Quote\Api\CartRepositoryInterface;
use Magento\Quote\Model\QuoteFactory;
use Magento\Checkout\Model\SessionFactory;
use Lof\MarketPlace\Api\SellersRepositoryInterface;

/**
 * Class SellerDeliverySlot
 * @package Lofmp\DeliverySlot\Model
 */
class SellerDeliverySlot implements SellerDeliverySlotInterface
{
    /**
     * @var DataObjectHelper
     */
    protected $dataObjectHelper;
    /**
     * @var Data
     */
    protected $data;

    /**
     * @var SerializerInterface
     */
    protected $serializer;

    /**
     * @var CollectionFactory
     */
    protected $collectionFactory;

    /**
     * @var DeliverSlotGroupCollection
     */
    protected $deliverSlotGroupCollection;

    /**
     * @var \Magento\Customer\Model\Session
     */
    protected $checkoutSession;

    /**
     * @var CartRepositoryInterface
     */
    private $quoteRepository;

    /**
     * @var QuoteFactory
     */
    protected $quoteFactory;

    /**
     * @var SellersRepositoryInterface
     */
    protected $sellerRepository;

    /**
     * @var SellerDeliverySlotSearchResultsInterfaceFactory
     */
    protected $deliverySlotResultsInterfaceFactory;

    /**
     * @var CollectionFactory
     */
    protected $orderCollection;

    protected $quote = null;

    /**
     * DeliverySlot constructor.
     *
     * @param Data $data
     * @param SerializerInterface $serializer
     * @param CollectionFactory $collectionFactory
     * @param DeliverSlotGroupCollection $deliverSlotGroupCollection
     * @param DateTime $dateTime
     * @param OrderCollection $orderCollection
     * @param CartRepositoryInterface $quoteRepository
     * @param QuoteFactory $quoteFactory
     * @param SellersRepositoryInterface $sellerRepository
     * @param SellerDeliverySlotSearchResultsInterfaceFactory $deliverySlotResultsInterfaceFactory
     * @param DataObjectHelper $dataObjectHelper
     */
    public function __construct(
        Data $data,
        SerializerInterface $serializer,
        CollectionFactory $collectionFactory,
        DeliverSlotGroupCollection $deliverSlotGroupCollection,
        DateTime $dateTime,
        OrderCollection $orderCollection,
        CartRepositoryInterface $quoteRepository,
        QuoteFactory $quoteFactory,
        SellersRepositoryInterface $sellerRepository,
        SellerDeliverySlotSearchResultsInterfaceFactory $deliverySlotResultsInterfaceFactory,
        DataObjectHelper $dataObjectHelper
    ) {
    
        $this->data = $data;
        $this->serializer = $serializer;
        $this->collectionFactory = $collectionFactory;
        $this->deliverSlotGroupCollection = $deliverSlotGroupCollection;
        $this->dateTime = $dateTime;
        $this->orderCollection = $orderCollection;
        $this->quoteRepository = $quoteRepository;
        $this->quoteFactory = $quoteFactory;
        $this->sellerRepository = $sellerRepository;
        $this->deliverySlotResultsInterfaceFactory = $deliverySlotResultsInterfaceFactory;
        $this->dataObjectHelper = $dataObjectHelper;
    }

    /**
     * {@inheritdoc}
     */
    public function getConfig($cartId, $zip_code, $target_date = '')
    {
        /**
         * TODO:
         * 1. Get current quote
         * 2. Get Quote Items
         * 3. Get seller id in quote items
         * 4. Verivy delivery slot for each seller
         */
        $seller_ids = [];
        $seller_ids[] = 0;
        $quote = $this->getQuote($cartId);
        foreach ($quote->getAllVisibleItems() as $item) {
            $product    = $item->getProduct()->load($item->getProductId());
            if ($item->getParentItem() && $product->isVirtual()) continue;
            if ($item->getSellerId()) {
                $sellerId = $item->getSellerId();
            } else {
                $sellerId = $item->getLofSellerId() ? $item->getLofSellerId() : $product->getSellerId();
            }
            if ($sellerId && !in_array($sellerId, $seller_ids)) {
                $seller_ids[] = (int)$sellerId;
            } 
        }
        
        $result = $this->getDeliverySlotConfig($zip_code, $seller_ids, $target_date);
        return $result;
    }

    /**
     * Get Delivery slot config
     * @param string $zip_code
     * @param int[] $seller_ids
     * @param string|null $target_date
     * @return \Lofmp\DeliverySlot\Api\Data\SellerDeliverySlotInterface[]|string
     */
    protected function getDeliverySlotConfig($zip_code, $seller_ids = [], $target_date = '')
    {
        $sellerNameText = "";
        if ($this->data->getDeliverySlotConfig('enabled') != 0) {
            $messages = [];
            $noRecordMessages = [];
            $finalAvailableSlots = [];
            foreach ($seller_ids as $sellerId) {
                if ($sellerId && !$this->data->isSellerEnabledDeliverySlot($sellerId)) {
                    continue;
                }
                if ($this->data->checkSellerVacationMode($target_date, $sellerId ) == 1) {
                    $seller = $sellerId?$this->sellerRepository->get($sellerId):null;
                    //just allow check slot for admin and active seller
                    if ($sellerId && $seller && (!$seller->getSellerId() || $seller->getStatus() != 1)) {
                        continue;
                    }
                    $sellerName = $seller?$seller->getName():"";
                    $sellerNameText = $sellerName?(" - seller: ".$sellerName):"";

                    $deliverySlotGroups = $this->deliverSlotGroupCollection->create()
                            ->addFieldToFilter('zip_code', ['like' => "%$zip_code%"]);

                    if ($sellerId) {
                        $deliverySlotGroups->addFieldToFilter('seller_id', $sellerId);
                    } else {
                        $deliverySlotGroups->addFieldToFilter('seller_id', ["null" => true]);
                    }
                    $slotGroupId = '';
                    foreach ($deliverySlotGroups as $deliverySlotGroup) {
                        $zipCodes = $deliverySlotGroup->getZipCode();
                        $zipCodes = explode(",", $zipCodes);
                        if (in_array($zip_code, $zipCodes)) {
                            $slotGroupId = $deliverySlotGroup->getId();
                        }
                    }
                    if (!empty($slotGroupId) && isset($slotGroupId)) {
                        $currentDate = $this->dateTime->gmtDate();
                        $dates = [];
                        for ($i = 0; $i < $this->data->getDeliverySlotConfig('no_of_days'); $i++) {
                            $dates[] = $this->dateTime->date('D, d M Y', strtotime("+$i day", strtotime($currentDate)));
                        }
                        $slots = [];
                        foreach ($dates as $date) {
                            $day = strtolower($this->dateTime->date('D', $date));
                            $deliverySlotCollection = $this->collectionFactory->create()
                                ->addFieldToFilter('day', $day)
                                ->addFieldToFilter('start_time', ['gteq' => $this->dateTime->date(' H:i')])
                                ->addFieldToFilter('status', 1)
                                ->addFieldToFilter('parent_id', $slotGroupId);

                            if ($sellerId) {
                                $deliverySlotCollection->addFieldToFilter('seller_id', $sellerId);
                            } else {
                                $deliverySlotCollection->addFieldToFilter('seller_id', ["null" => true]);
                            }
                            $deliverySlot = $deliverySlotCollection->getData();
                            $slots[$date] = $deliverySlot;
                        }
                        
                        foreach ($slots as $key => $slot) {
                            $day = strtolower($this->dateTime->date('D', $key));
                            $availableSlots = [];
                            foreach ($slot as $sl) {
                                if ($sl['day'] == $day) {
                                    $date = $this->dateTime->date('Y-m-d', $key);
                                    $slotSellerId = isset($sl["seller_id"])?$sl["seller_id"]:0;
                                    $orderCollection = $this->orderCollection->create();
                                    $orderCollection->addFieldToFilter('main_table.delivery_date', $date);
                                    if ($slotSellerId) {
                                        $orderCollection->getSelect()->join(
                                            ['seller_order_table' => $orderCollection->getResource()->getTable("lof_marketplace_sellerorder")],
                                            'main_table.entity_id = seller_order_table.order_id',
                                            [
                                                'seller_id'
                                            ]
                                        )->group(
                                            'main_table.entity_id'
                                        );
                                        $orderCollection->addFieldToFilter('seller_order_table.seller_id', $slotSellerId);
                                    }
                                    $ordersCount = $orderCollection->addFieldToFilter('main_table.delivery_date', $date)->getTotalCount();
                                    if (!$sl['allocation'] || $ordersCount < $sl['allocation']) {
                                        $sl['current_status'] = 1;
                                        $availableSlots[] = $sl;
                                    } else {
                                        $sl['current_status'] = 0;
                                        $availableSlots[] = $sl;
                                    }
                                }
                            }
                            if (!empty($availableSlots)) {
                                $finalAvailableSlots[] = [
                                    'seller_name' => $sellerName,
                                    'date' => $key,
                                    'slots' => $availableSlots
                                ];
                            }
                        }
                    } else {
                        $noRecordMessages[] = "No Slots Available For Following ZipCode: ".$zip_code.$sellerNameText;
                    }
                } else {
                    $vacationMessage = $this->data->getSellerDeliverySlotVacationConfig('message', $sellerId);
                    $from_date = $this->data->getSellerDeliverySlotVacationConfig('from_date', $sellerId);
                    $to_date = $this->data->getSellerDeliverySlotVacationConfig('to_date', $sellerId);

                    if (isset($vacationMessage) && !empty($vacationMessage)) {
                        $messages[] = $vacationMessage." from ".$from_date." to ".$to_date.$sellerNameText;
                    } else {
                        $messages[] = "Slots not available , Vacation Period from ".$from_date." to ".$to_date.$sellerNameText;
                    }
                }
            }
            
            return $this->getSellerDeliveryResultsDataModel([
                            "items" => $finalAvailableSlots,
                            "vacation_messages" => $messages,
                            "no_slots_messages" => $noRecordMessages
                        ]);
        }

        return "Delivery Slot Is Not Enabled";
    }

    /**
     * Get active quote
     * @param int $cartId
     * @return \Magento\Quote\Model\Quote
     */
    protected function getQuote($cartId)
    {
        if (null === $this->quote) {
            $this->quote = $this->quoteRepository->get($cartId);
        }
        return $this->quote;
    }

    public function getSellerDeliveryResultsDataModel($arrayData = [])
    {
        $itemDataObject = $this->deliverySlotResultsInterfaceFactory->create();
        $this->dataObjectHelper->populateWithArray(
            $itemDataObject,
            $arrayData,
            SellerDeliverySlotSearchResultsInterface::class
        );
        
        return $itemDataObject;
    }
}
