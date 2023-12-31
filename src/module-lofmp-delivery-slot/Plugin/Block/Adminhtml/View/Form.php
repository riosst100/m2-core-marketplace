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

namespace Lofmp\DeliverySlot\Plugin\Block\Adminhtml\View;

/**
 * Class Form
 * @package Lofmp\DeliverySlot\Plugin\Block\Adminhtml\View
 */
class Form extends \Magento\Shipping\Block\Adminhtml\View\Form
{
    /**
     * @param \Magento\Shipping\Block\Adminhtml\View\Form $subject
     */
    public function beforeToHtml(
        \Magento\Shipping\Block\Adminhtml\View\Form $subject
    ) {
        $subject->setTemplate(
            'Lofmp_DeliverySlot::order/shipping/view.phtml'
        );
    }
}
