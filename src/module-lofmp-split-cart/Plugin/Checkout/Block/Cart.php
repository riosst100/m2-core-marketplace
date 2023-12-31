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
 * @package    Lofmp_SplitCart
 * @copyright  Copyright (c) 2021 Landofcoder (https://www.landofcoder.com/)
 * @license    https://landofcoder.com/terms
 */

namespace Lofmp\SplitCart\Plugin\Checkout\Block;

use Lofmp\SplitCart\Helper\ConfigData;

class Cart
{
    /**
     * @var ConfigData
     */
    private $moduleConfig;

    /**
     * @param ConfigData $moduleConfig
     */
    public function __construct(ConfigData $moduleConfig)
    {
        $this->moduleConfig = $moduleConfig;
    }

    /**
     * @param \Magento\Checkout\Block\Cart $subject
     * @return null
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function beforeToHtml(\Magento\Checkout\Block\Cart $subject)
    {
        if ($this->moduleConfig->isEnabled()) {
            $layout = $subject->getLayout();
            $layout->unsetElement('cart.summary');
            $layout->unsetElement('checkout.cart.shipping');
            $layout->unsetElement('checkout.cart.totals.container');
            $layout->unsetElement('checkout.cart.coupon');
            $layout->unsetElement('checkout.cart.methods.multishipping');
            $layout->unsetElement('checkout.cart.methods.multishipping');
        }

        return null;
    }
}
