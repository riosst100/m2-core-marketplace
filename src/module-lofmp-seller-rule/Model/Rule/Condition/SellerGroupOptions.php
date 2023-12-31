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
 * @package    Lofmp_SellerRule
 * @copyright  Copyright (c) 2021 Landofcoder (https://www.landofcoder.com/)
 * @license    https://landofcoder.com/terms
 */

namespace Lofmp\SellerRule\Model\Rule\Condition;

use Magento\Framework\Data\OptionSourceInterface;

class SellerGroupOptions implements OptionSourceInterface
{
    /**
     * @var \Lof\MarketPlace\Model\ResourceModel\Group\CollectionFactory
     */
    private $sellerGroupCollectionFactory;

    /**
     * SellerGroupOptions constructor.
     * @param \Lof\MarketPlace\Model\ResourceModel\Group\CollectionFactory $sellerGroupCollectionFactory
     */
    public function __construct(
        \Lof\MarketPlace\Model\ResourceModel\Group\CollectionFactory $sellerGroupCollectionFactory
    ) {
        $this->sellerGroupCollectionFactory = $sellerGroupCollectionFactory;
    }

    /**
     * @return array
     */
    public function toOptionArray(): array
    {
        $options = [];
        $groups = $this->getSellerGroups();
        foreach ($groups as $group) {
            $options[] = [
                'label' => $group->getName(),
                'value' => $group->getGroupId(),
            ];
        }
        return $options;
    }

    /**
     * @return array|\Magento\Framework\DataObject[]
     */
    private function getSellerGroups(): array
    {
        $groups = $this->sellerGroupCollectionFactory->create()->getItems();
        if ($groups) {
            return $groups;
        }
        return [];
    }

    /**
     * @return array
     */
    public function getHashSellerGroupOptions(): array
    {
        $groups = $this->getSellerGroups();
        $data = [];
        foreach ($groups as $group) {
            $data[$group->getId()] = $group->getName();
        }
        return $data;
    }
}
