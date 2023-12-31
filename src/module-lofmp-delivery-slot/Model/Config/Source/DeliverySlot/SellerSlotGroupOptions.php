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
namespace Lofmp\DeliverySlot\Model\Config\Source\DeliverySlot;

use Magento\Framework\Option\ArrayInterface;
use Lofmp\DeliverySlot\Model\ResourceModel\Marketplace\DeliverySlotGroup\Grid\CollectionFactory;
/**
 * Class SlotGroupOptions
 * @package Lofmp\DeliverySlot\Model\Config\Source\DeliverySlot
 */
class SellerSlotGroupOptions implements ArrayInterface
{
    /**
     * @var CollectionFactory
     */
    protected $collectionFactory;

    /**
     * SlotGroupOptions constructor.
     * @param CollectionFactory $collectionFactory
     */
    public function __construct(
        CollectionFactory $collectionFactory
    ) {
        $this->collectionFactory = $collectionFactory;
    }

    /**
     * @return array
     */
    public function toOptionArray()
    {
        $slotGroupCollection = $this->collectionFactory->create();
        $options = [];
        if ($slotGroupCollection->getSize() ) {
            foreach ($slotGroupCollection as $slotGroup) {
                $options[] = [
                    'label' => $slotGroup->getSlotGroupName(),
                    'value' => $slotGroup->getGroupId()
                ];
            }
            
        }
        return $options;
    }
}
