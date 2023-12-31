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

class SaveslotGroup
{
    /**
     * @var \Lofmp\DeliverySlot\Model\DeliverySlotGroupFactory
     */
    protected $savemodel;

    public function __construct(\Lofmp\DeliverySlot\Model\DeliverySlotGroupFactory $savemodel
    )
    {
        $this->savemodel = $savemodel;
    }

    public function save($data, $newzip, $seller_id = 0)
    {
        try {
            $deliverygroup = $this->savemodel->create();
            $storedata = [
                'slot_group_name' => $data['slot_group_name'],
                'zip_code' => $newzip,
                'seller_id' => isset($data["seller_id"])?(int)$data["seller_id"]:$seller_id
            ];
            if(!$storedata['seller_id']){
                unset($storedata['seller_id']);
            }
            $deliverygroup->setData($storedata);
            $deliverygroup->save();
            return $deliverygroup->getData();
        } catch (\Exception $e) {
            return false;
        }
    }

    public function update($data, $newzip, $groupId, $seller_id = 0, $is_admin = false)
    {
        try {
            $deliverygroup = $this->savemodel->create()->load($groupId);
            if($is_admin || ($deliverygroup->getSellerId() == $seller_id)){
                $storedata = [
                    'slot_group_name' => $data['slot_group_name'],
                    'zip_code' => $newzip
                ];
                $deliverygroup->addData($storedata);
                $deliverygroup->save();
                return $deliverygroup->getData();
            }
        } catch (\Exception $e) {
            //$e->getMessage();
        }
        return false;
    }
}
