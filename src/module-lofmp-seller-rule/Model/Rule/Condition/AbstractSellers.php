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
 * @package    Lofmp_SellerRule
 * @copyright  Copyright (c) 2021 Landofcoder (https://www.landofcoder.com/)
 * @license    https://landofcoder.com/terms
 */

namespace Lofmp\SellerRule\Model\Rule\Condition;

use Magento\Rule\Model\Condition\AbstractCondition;
use Magento\Rule\Model\Condition\Context;

abstract class AbstractSellers extends AbstractCondition
{
    /**
     * @var \Lofmp\SellerRule\Model\Rule\Condition\SellerAttributes
     */
    protected $sellerAttributes;

    /**
     * @var \Lofmp\SellerRule\Model\Rule\Condition\SellerGroupOptions
     */
    protected $sellerGroupOptions;

    /**
     * @var \Magento\Backend\Helper\Data
     */
    protected $backendData;

    /**
     * @var \Lof\MarketPlace\Model\Seller
     */
    protected $sellerModel;

    /**
     * @var \Magento\Cms\Ui\Component\Listing\Column\Cms\Options
     */
    protected $storeViewOptions;

    /**
     * @var \Lofmp\SellerRule\Model\Rule\Condition\Countries
     */
    protected $countryOptions;

    /**
     * AbstractSellers constructor.
     * @param \Magento\Rule\Model\Condition\Context $context
     * @param \Magento\Backend\Helper\Data $backendData
     * @param \Lof\MarketPlace\Model\Seller $sellerModel
     * @param Countries $countryOptions
     * @param \Magento\Cms\Ui\Component\Listing\Column\Cms\Options $storeViewOptions
     * @param \Lofmp\SellerRule\Model\Rule\Condition\SellerAttributes $sellerAttributes
     * @param \Lofmp\SellerRule\Model\Rule\Condition\SellerGroupOptions $sellerGroupOptions
     * @param array $data
     */
    public function __construct(
        Context $context,
        \Magento\Backend\Helper\Data $backendData,
        \Lof\MarketPlace\Model\Seller $sellerModel,
        \Lofmp\SellerRule\Model\Rule\Condition\Countries $countryOptions,
        \Magento\Cms\Ui\Component\Listing\Column\Cms\Options $storeViewOptions,
        \Lofmp\SellerRule\Model\Rule\Condition\SellerAttributes $sellerAttributes,
        \Lofmp\SellerRule\Model\Rule\Condition\SellerGroupOptions $sellerGroupOptions,
        array $data = []
    ) {
        $this->countryOptions = $countryOptions;
        $this->storeViewOptions = $storeViewOptions;
        $this->sellerModel = $sellerModel;
        $this->backendData = $backendData;
        $this->sellerAttributes = $sellerAttributes;
        $this->sellerGroupOptions = $sellerGroupOptions;
        parent::__construct($context, $data);
    }

    /**
     * call all seller properties and bind to special attributes
     *
     * @return $this|\Lofmp\SellerRule\Model\Rule\Condition\AbstractSellers
     */
    public function loadAttributeOptions()
    {
        $sellerAttributes = $this->sellerAttributes->getSellerAllAttributes();
        $sellerUsages = $this->sellerAttributes->getSellerUsage();
        $attributes = [];

        foreach ($sellerAttributes as $attribute => $attributeLabel) {
            $attributes[$attribute] = $attributeLabel;
        }

        foreach ($sellerUsages as $attribute => $attributeLabel) {
            $attributes[$attribute] = $attributeLabel;
        }

        $this->_addSpecialAttributes($attributes);

        asort($attributes);
        $this->setAttributeOption($attributes);

        return $this;
    }

    /**
     * Add special attributes
     *
     * @param array &$attributes
     * @return void
     */
    protected function _addSpecialAttributes(array &$attributes)
    {
        $attributes['groups'] = __('Seller Groups');
        $attributes['specified'] = __('Specified Sellers');
    }

    /**
     * set value type for each attribute type
     *
     * @return string
     */
    public function getValueElementType()
    {
        switch ($this->getAttribute()) {
            case 'groups':
                return 'multiselect';
            case 'created_at':
                return 'date';
            case 'status':
            case 'store_id':
            case 'country_id':
            case 'verify_status':
                return 'select';
            default:
                return 'text';
        }
    }

    /**
     * set input value type for each attribute type
     *
     * @return string
     */
    public function getInputType()
    {
        switch ($this->getAttribute()) {
            case 'groups':
                return 'multiselect';
            case 'created_at':
                return 'date';
            case 'telephone':
            case 'postcode':
                return 'numeric';
            case 'status':
            case 'store_id':
            case 'country_id':
            case 'verify_status':
                return 'boolean';
            default:
                return 'string';
        }
    }

    /**
     * Prepares values options to be used as select options or hashed array
     * Result is stored in following keys:
     *  'value_select_options' - normal select array: array(array('value' => $value, 'label' => $label), ...)
     *  'value_option' - hashed array: array($value => $label, ...),
     *
     * @return $this
     * @SuppressWarnings(PHPMD.CyclomaticComplexity)
     */
    protected function _prepareValueOptions()
    {
        // Check that both keys exist. Maybe somehow only one was set not in this routine, but externally.
        $selectReady = $this->getData('value_select_options');
        $hashedReady = $this->getData('value_option');
        if ($selectReady && $hashedReady) {
            return $this;
        }

        // Get array of select options. It will be used as source for hashed options
        $selectOptions = null;
        if ($this->getAttribute() === 'groups') {
            $selectOptions = $this->sellerGroupOptions->toOptionArray();
        }

        if ($this->getAttribute() === 'status') {
            $selectOptions = $this->getSellerStatus($this->sellerModel->getAvailableStatuses());
        }

        if ($this->getAttribute() === 'verify_status') {
            $selectOptions = $this->getSellerStatus($this->sellerModel->getAvailableVerifyStatuses());
        }

        if ($this->getAttribute() === 'store_id') {
            $selectOptions = $this->storeViewOptions->toOptionArray();
        }

        if ($this->getAttribute() === 'country_id') {
            $selectOptions = $this->countryOptions->getCountries();
        }

        $this->_setSelectOptions($selectOptions, $selectReady, $hashedReady);

        return $this;
    }

    /**
     * Set new values only if we really got them
     *
     * @param array $selectOptions
     * @param array $selectReady
     * @param array $hashedReady
     * @return $this
     */
    protected function _setSelectOptions($selectOptions, $selectReady, $hashedReady)
    {
        if ($selectOptions !== null) {
            // Overwrite only not already existing valuesea
            if (!$selectReady) {
                $this->setData('value_select_options', $selectOptions);
            }
            if (!$hashedReady) {
                $hashedOptions = [];
                foreach ($selectOptions as $option) {
                    if (is_array($option['value'])) {
                        continue; // We cannot use array as index
                    }
                    $hashedOptions[$option['value']] = $option['label'];
                }
                $this->setData('value_option', $hashedOptions);
            }
        }
        return $this;
    }

    /**
     * @inheritDoc
     */
    public function getValueSelectOptions()
    {
        $this->_prepareValueOptions();
        return $this->getData('value_select_options');
    }

    /**
     * @param string|null $option
     * @return string
     */
    public function getValueOption($option = null)
    {
        $this->_prepareValueOptions();
        return $this->getData('value_option' . ($option !== null ? '/' . $option : ''));
    }

    /**
     * @return string
     */
    public function getValueElementChooserUrl()
    {
        $url = false;
        switch ($this->getAttribute()) {
            case 'specified':
                $url = 'lofmp_sellerrule/rule_sellers/chooser/choose_type/' . $this->getAttribute();
                if ($this->getJsFormObject()) {
                    $url .= '/form/' . $this->getJsFormObject();
                }
                break;
            default:
                break;
        }
        return $url !== false ? $this->backendData->getUrl($url) : '';
    }

    /**
     * @return string
     */
    public function getValueAfterElementHtml()
    {
        $html = '';
        switch ($this->getAttribute()) {
            case 'specified':
                $image = $this->_assetRepo->getUrl('images/rule_chooser_trigger.gif');
                break;
            default:
                break;
        }

        if (!empty($image)) {
            $html = '<a href="javascript:void(0)" class="rule-chooser-trigger"><img src="' .
                $image .
                '" alt="" class="v-middle rule-chooser-trigger" title="' .
                __('Select Specified Sellers') . '" /></a>';
        }
        return $html;
    }

    /**
     * @return bool
     */
    public function getExplicitApply()
    {
        switch ($this->getAttribute()) {
            case ('specified' || 'groups'):
                return true;
            default:
                break;
        }
        return false;
    }

    /**
     * @param \Magento\Framework\Model\AbstractModel $model
     * @return bool
     */
    public function validate(\Magento\Framework\Model\AbstractModel $model)
    {
        $attrCode = $this->getAttribute();
        if (!$model->getResource()) {
            return false;
        }

        if ($attrCode === 'created_at' && !is_int($this->getValue())){
            $this->setValue(strtotime($this->getValue()));
            $value = strtotime($model->getData($attrCode));
            return $this->validateAttribute($value);
        }
        return parent::validate($model);
    }

    /**
     * @param array $statuses
     * @return array
     */
    public function getSellerStatus(array $statuses)
    {
        $options = [];
        foreach ($statuses as $value => $label) {
            $options[] = [
                'value' => $value,
                'label' => $label
            ];
        }
        return $options;
    }
}
