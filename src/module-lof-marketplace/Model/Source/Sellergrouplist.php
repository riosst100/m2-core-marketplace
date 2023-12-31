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
 * @package    Lof_MarketPlace
 * @copyright  Copyright (c) 2021 Landofcoder (https://www.landofcoder.com/)
 * @license    https://landofcoder.com/terms
 */

namespace Lof\MarketPlace\Model\Source;

class Sellergrouplist implements \Magento\Framework\Option\ArrayInterface
{
    /**
     * @var \Lof\MarketPlace\Model\Group
     */
    protected $_group;

    /**
     * Sellergrouplist constructor.
     * @param \Lof\MarketPlace\Model\Group $group
     */
    public function __construct(
        \Lof\MarketPlace\Model\Group $group
    ) {
        $this->_group = $group;
    }

    /**
     * Options getter
     *
     * @return array
     */
    public function toOptionArray()
    {
        $groups = $this->_group->getCollection()
            ->addFieldToFilter('status', '1');
        $groupList = [];

        foreach ($groups as $group) {
            $groupList[] = [
                'label' => $group->getName(),
                'value' => $group->getId()
            ];
        }

        return $groupList;
    }
}
