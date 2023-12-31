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
 * @package    Lof_MarketPlace
 * @copyright  Copyright (c) 2021 Landofcoder (https://www.landofcoder.com/)
 * @license    https://landofcoder.com/terms
 */

namespace Lof\MarketPlace\Block\Seller\Product\Helper\Form;

use Magento\Catalog\Model\Product\Edit\WeightResolver;

class Weight extends \Magento\Catalog\Block\Adminhtml\Product\Helper\Form\Weight
{
    /**
     * @var \Lof\MarketPlace\Helper\Data
     */
    protected $productHelper;

    /**
     * @param \Magento\Framework\Data\Form\Element\Factory $factoryElement
     * @param \Magento\Framework\Data\Form\Element\CollectionFactory $factoryCollection
     * @param \Magento\Framework\Escaper $escaper
     * @param \Magento\Framework\Locale\Format $localeFormat
     * @param \Magento\Directory\Helper\Data $directoryHelper
     * @param \Lof\MarketPlace\Helper\Data $productHelper
     * @param array $data
     */
    public function __construct(
        \Magento\Framework\Data\Form\Element\Factory $factoryElement,
        \Magento\Framework\Data\Form\Element\CollectionFactory $factoryCollection,
        \Magento\Framework\Escaper $escaper,
        \Magento\Framework\Locale\Format $localeFormat,
        \Magento\Directory\Helper\Data $directoryHelper,
        \Lof\MarketPlace\Helper\Data $productHelper,
        array $data = []
    ) {
        parent::__construct(
            $factoryElement,
            $factoryCollection,
            $escaper,
            $localeFormat,
            $directoryHelper,
            $data
        );
        $this->productHelper = $productHelper;
    }

    /**
     * Add Weight Switcher radio-button element html to weight field
     *
     * @return string
     */
    public function getElementHtml()
    {
        if (!$this->getForm()->getDataObject()->getTypeInstance()->hasWeight()) {
            $this->weightSwitcher->setValue(WeightResolver::HAS_NO_WEIGHT);
        }
        if ($this->getDisabled()) {
            $this->weightSwitcher->setDisabled($this->getDisabled());
        }
        $disableVirtualType = in_array(
            \Magento\Catalog\Model\Product\Type::TYPE_VIRTUAL,
            $this->productHelper->getProductTypeRestriction()
        );
        // phpcs:disable Generic.Files.LineLength.TooLong
        return '<div class="admin__field-control weight-switcher">' .
            '<div class="admin__control-switcher' . ($disableVirtualType ? ' no-display' : '') . '" data-role="weight-switcher">' .
            $this->weightSwitcher->getLabelHtml() .
            '<div class="admin__field-control-group">' .
            $this->weightSwitcher->getElementHtml() .
            '</div>' .
            '</div>' .
            '<div class="admin__control-addon">' .
            \Magento\Framework\Data\Form\Element\Text::getElementHtml() .
            '<label class="admin__addon-suffix" for="' .
            $this->getHtmlId() .
            '"><span>' .
            $this->directoryHelper->getWeightUnit() .
            '</span></label>' .
            '</div>' .
            '</div>';
    }

    /**
     * @return \Magento\Framework\Phrase
     */
    public function getLabel()
    {
        return __("Weight");
    }

    /**
     * @return mixed|string
     */
    public function getName()
    {
        return 'product[weight]';
    }
}
