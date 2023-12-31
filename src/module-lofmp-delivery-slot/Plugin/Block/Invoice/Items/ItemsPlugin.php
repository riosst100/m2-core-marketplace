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

namespace Lofmp\DeliverySlot\Plugin\Block\Invoice\Items;

/**
 * Class ItemsPlugin
 * @package Lofmp\DeliverySlot\Plugin\Block\Invoice\Items
 */
class ItemsPlugin
{
    public function beforeToHtml(
        \Magento\Sales\Block\Order\Email\Invoice\Items $items
    ) {
        if ($items->getTemplate() == 'Magento_Sales::email/invoice/items.phtml') {
            $items->setTemplate('Lofmp_DeliverySlot::email/invoice/items.phtml');
        }
    }
}
