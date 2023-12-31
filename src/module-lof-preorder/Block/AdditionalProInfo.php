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

namespace Lof\PreOrder\Block;

/**
 * AdditionalProInfo
 */
class AdditionalProInfo extends \Magento\Framework\View\Element\Template
{
    protected $_preorderHelper;
    /**
     * @param \Magento\Framework\View\Element\Template\Context $context
     * @param \Lof\PreOrder\Helper\Data $preorderHelper
     * @param array $data
     */
    public function __construct(
        \Magento\Framework\View\Element\Template\Context $context,
        \Lof\PreOrder\Helper\Data $preorderHelper,
        array $data = []
    ) {
        $this->_preorderHelper = $preorderHelper;
        parent::__construct($context, $data);
    }

    /**
     * @return void
     */
    protected function _construct()
    {
        parent::_construct();
    }

    /**
     * @return $this
     */
    protected function _prepareLayout()
    {
        parent::_prepareLayout();
    }

    /**
     * @param mixed $product
     * @return additional information data
     */
    public function getAdditionalData($product = null)
    {
        // Do your code here
        return "Additional Informations";
    }
}
