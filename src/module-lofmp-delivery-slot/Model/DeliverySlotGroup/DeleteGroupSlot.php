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

namespace Lofmp\DeliverySlot\Model\DeliverySlotGroup;

class DeleteGroupSlot
{  
    /**
     * @var \Lofmp\DeliverySlot\Model\DeliverySlotGroupFactory
     */
    protected $deletemodel;

    /**
     * @var \Lofmp\DeliverySlot\Model\ResourceModel\DeliverySlotGroupFactory
     */
    protected $deletefactory;

    public function __construct(
        \Lofmp\DeliverySlot\Model\DeliverySlotGroupFactory $deletemodel,
        \Lofmp\DeliverySlot\Model\ResourceModel\DeliverySlotGroupFactory $deletefactory
    )
    {
        $this->deletemodel = $deletemodel;
        $this->deletefactory = $deletefactory;
    }

    public function delete($groupId, $seller_id = 0, $is_admin = false)
    {
        try {
             $groupdata = [
                'group_id' => $groupId,
                'seller_id' => (int)$seller_id
             ];
             if($is_admin){
                unset($groupdata['seller_id']);
             }
             $data = $this->deletemodel->create(['data' => $groupdata]);
             $deleteData =  $this->deletefactory->create();
             $deleteData->delete($data);
             
              return true;
        } catch (\Exception $e) {
            return false;
        }
    }
}
