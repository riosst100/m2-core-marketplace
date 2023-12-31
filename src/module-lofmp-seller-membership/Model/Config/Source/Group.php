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
namespace Lofmp\SellerMembership\Model\Config\Source;

class Group extends \Magento\Eav\Model\Entity\Attribute\Source\AbstractSource
{
    protected $group;

    /**
     * @param \Lofmp\SellerMembership\Model\Category
     */
    public function __construct(
        \Lof\MarketPlace\Model\Group $group
    ) {
        $this->group = $group;
    }

    public function getAllOptions()
    {
        $options = [];
        foreach ($this->group->getCollection()->addFieldToFilter('status', '1') as $key => $group) {
            $options[] = [
                'label' => $group->getData('name'),
                'value' => $group->getData('group_id')
            ];
        }
        return $options;
    }
}
