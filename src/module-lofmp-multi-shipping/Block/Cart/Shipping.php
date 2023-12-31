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
 * @package    Lofmp_MultiShipping
 * @copyright  Copyright (c) 2021 Landofcoder (https://www.landofcoder.com/)
 * @license    https://landofcoder.com/terms
 */

namespace Lofmp\MultiShipping\Block\Cart;

class Shipping extends \Magento\Checkout\Block\Cart\Shipping
{
    /**
     * @var \Magento\Framework\ObjectManagerInterface
     */
    protected $_objectManager;

    /**
     * @var \Lofmp\MultiShipping\Helper\Data
     */
    protected $_helperData;

    /**
     * Shipping constructor.
     *
     * @param \Magento\Framework\View\Element\Template\Context $context
     * @param \Magento\Customer\Model\Session $customerSession
     * @param \Magento\Checkout\Model\Session $checkoutSession
     * @param \Magento\Checkout\Model\CompositeConfigProvider $configProvider
     * @param \Magento\Framework\ObjectManagerInterface $objectInterface
     * @param \Lofmp\MultiShipping\Helper\Data $helperData
     * @param array $layoutProcessors
     * @param array $data
     */
    public function __construct(
        \Magento\Framework\View\Element\Template\Context $context,
        \Magento\Customer\Model\Session $customerSession,
        \Magento\Checkout\Model\Session $checkoutSession,
        \Magento\Checkout\Model\CompositeConfigProvider $configProvider,
        \Magento\Framework\ObjectManagerInterface $objectInterface,
        \Lofmp\MultiShipping\Helper\Data $helperData,
        array $layoutProcessors = [],
        array $data = []
    ) {
        parent::__construct($context, $customerSession, $checkoutSession, $configProvider, $layoutProcessors, $data);
        $this->_objectManager = $objectInterface;
        $this->_helperData = $helperData;
    }

    /**
     * @return string|string[]
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function getJsLayout()
    {
        if ($this->_helperData->isEnabled()) {
            return str_replace(
                "Magento_Checkout\/js\/view\/cart\/shipping-rates",
                "Lofmp_MultiShipping\/js/cart\/shipping-rates",
                parent::getJsLayout()
            );
        } else {
            return parent::getJsLayout();
        }
    }
}
