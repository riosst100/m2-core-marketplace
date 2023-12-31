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

namespace Lofmp\SellerMembership\Block\Widget;

/**
 * Class View
 * @package Magento\Catalog\Block\Category
 */
class Membership extends \Magento\Framework\View\Element\Template implements \Magento\Widget\Block\BlockInterface
{
    /**
     * Core registry
     *
     * @var \Magento\Framework\Registry
     */
    protected $_coreRegistry = null;

    /**
     * @var \Lofmp\SellerMembership\Helper\Data
     */
    protected $_customerHelper;

    /**
     * @param \Magento\Framework\View\Element\Template\Context $context
     * @param \Magento\Catalog\Model\Layer\Resolver $layerResolver
     * @param \Magento\Framework\Registry $registry
     * @param \Magento\Catalog\Helper\Category $categoryHelper
     * @param array $data
     */
    public function __construct(
        \Magento\Framework\View\Element\Template\Context $context,
        \Magento\Framework\Registry $registry,
        \Lofmp\SellerMembership\Helper\Data $customerHelper,
        array $data = []
    ) {
        $this->_customerHelper = $customerHelper;
        $this->_coreRegistry = $registry;
        parent::__construct($context, $data);
    }

    /**
     * @return $this
     */
    protected function _prepareLayout()
    {
        parent::_prepareLayout();

        if ($template = $this->getData("custom_template")) {
            $this->setTemplate($template);
        } else {
            $this->setTemplate("Lofmp_SellerMembership::widget/membership.phtml");
        }

        return $this;
    }

    /**
     * @return string
     */
    public function getProductListHtml()
    {
        $product_list = $this->getLayout()->createBlock(
            'Lofmp\SellerMembership\Block\Membership\Product\ListProduct'
        )->setWidgetData($this->getData())->setTemplate("Lofmp_SellerMembership::product/list.phtml");

        return $product_list->toHtml();
    }

    /**
     * @return string[]
     */
    public function getIdentities()
    {
        return ['lofmp_membership_widget_product_list'];
    }
}
