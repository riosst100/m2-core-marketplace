<?php
/**
 * Landofcoder
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Landofcoder.com license that is
 * available through the world-wide-web at this URL:
 * http://landofcoder.com/license
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade this extension to newer
 * version in the future.
 *
 * @category   Landofcoder
 * @package    Lofmp_PriceComparison
 * @copyright  Copyright (c) 2017 Landofcoder (http://www.landofcoder.com/)
 * @license    http://www.landofcoder.com/LICENSE-1.0.html
 */

namespace Lofmp\PriceComparison\Observer;

use Magento\Framework\Event\ObserverInterface;

class BeforeViewCart implements ObserverInterface
{
    /**
     * @var \Lofmp\PriceComparison\Helper\Data
     */
    protected $_assignHelper;

    /**
     * @param \Lofmp\PriceComparison\Helper\Data $helper
     */
    public function __construct(\Lofmp\PriceComparison\Helper\Data $helper)
    {
        $this->_assignHelper = $helper;
    }

    public function execute(\Magento\Framework\Event\Observer $observer)
    {
        if ($this->_assignHelper->isEnabled()) {
            $this->_assignHelper->checkStatus();
            $this->_assignHelper->checkCartPrice();
        }
    }
}
