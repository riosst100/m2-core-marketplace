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
 * @package    Lof_PreOrder
 * @copyright  Copyright (c) 2021 Landofcoder (https://www.landofcoder.com/)
 * @license    https://landofcoder.com/terms
 */

namespace Lof\PreOrder\Model\Plugin;

class CatalogInventoryBackorders
{
    /**
     * @return array
     */
    public function afterToOptionArray(
        \Magento\CatalogInventory\Model\Source\Backorders $subject,
        array $optionArray
    ) {
        $optionArray[] = [
            'value' => \Lof\PreOrder\Helper\Data::BACKORDERS_PREORDER_OPTION,
            'label'=> __('Allow Pre-Orders')
        ];
        return $optionArray;
    }
}
