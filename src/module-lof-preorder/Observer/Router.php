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

namespace Lof\PreOrder\Observer;

use Magento\Framework\Event\ObserverInterface;

class Router implements ObserverInterface
{
    /**
     * @var \Lof\PreOrder\Helper\Data
     */
    protected $_preorderHelper;

    /**
     * @param \Lof\PreOrder\Helper\Data $preorderHelper
     */
    public function __construct(
        \Lof\PreOrder\Helper\Data $preorderHelper
    ) {
        $this->_preorderHelper = $preorderHelper;
    }

    public function execute(\Magento\Framework\Event\Observer $observer)
    {
        $this->_preorderHelper->createPreOrderProduct();
    }
}
