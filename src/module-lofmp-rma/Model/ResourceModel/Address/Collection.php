<?php
/**
 * LandOfCoder
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Landofcoder.com license that is
 * available through the world-wide-web at this URL:
 * https://landofcoder.com/license-agreement.html
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade this extension to newer
 * version in the future.
 *
 * @category   LandOfCoder
 * @package    Lofmp_Rma
 * @copyright  Copyright (c) 2021 Landofcoder (https://landofcoder.com/)
 * @license    https://landofcoder.com/LICENSE-1.0.html
 */


namespace Lofmp\Rma\Model\ResourceModel\Address;

class Collection extends \Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection
{
    /**
     * {@inheritdoc}
     */
    protected function _construct()
    {
        $this->_init('Lofmp\Rma\Model\Address', 'Lofmp\Rma\Model\ResourceModel\Address');
    }

    /**
     * {@inheritdoc}
     */
    public function toOptionArray($emptyOption = false, $defaultAddress = '')
    {
        $arr = [];
        if ($emptyOption) {
            $defaultLabel = __('-- Default Address --');
            if (empty($defaultAddress)) {
                $defaultLabel = __('-- Please Select --');
                $defaultAddress = 0;
            }
            $arr[0] = ['value' => $defaultAddress, 'label' => $defaultLabel];
        }
        /** @var \Lofmp\Rma\Model\Address $item */
        foreach ($this->addActiveFilter() as $item) {
            $arr[] = ['value' => $item->getAddress(), 'label' => $item->getName()];
        }

        return $arr;
    }

    /**
     * @return $this
     */
    public function addActiveFilter()
    {
        $this->getSelect()
            ->where('is_active', 1)
        ;

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    protected function _initSelect()
    {
        parent::_initSelect();

        $this->addOrder('sort_order', self::SORT_ORDER_ASC);

        return $this;
    }

     /************************/
}
