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

namespace Lof\MarketPlace\Block\Shipping;

use Lof\MarketPlace\Model\Config\Shipping\Methods\AbstractModel;

/**
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 * @phpstan-ignore-next-line
 */
class Methods extends \Lof\MarketPlace\Block\Seller\AbstractBlock
{
    /**
     * @var
     */
    protected $_form;

    /**
     * @return Methods
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    protected function _prepareLayout()
    {
        \Magento\Framework\Data\Form::setElementRenderer(
            $this->getLayout()->createBlock(\Lof\MarketPlace\Block\Widget\Form\Renderer\Element::class)
        );
        \Magento\Framework\Data\Form::setFieldsetRenderer(
            $this->getLayout()->createBlock(\Lof\MarketPlace\Block\Widget\Form\Renderer\Fieldset::class)
        );
        \Magento\Framework\Data\Form::setFieldsetElementRenderer(
            $this->getLayout()->createBlock(\Lof\MarketPlace\Block\Widget\Form\Renderer\Fieldset\Element::class)
        );

        return parent::_prepareLayout();
    }

    /**
     * Get form object
     *
     * @return Varien_Data_Form
     */
    public function getForm()
    {
        return $this->_form;
    }

    /**
     * @return Varien_Data_Form
     */
    public function getFormObject()
    {
        return $this->getForm();
    }

    /**
     * Get form HTML
     *
     * @return string
     */
    public function getFormHtml()
    {
        if (is_object($this->getForm())) {
            return $this->getForm()->getHtml();
        }
        return '';
    }

    /**
     * @param \Magento\Framework\Data\Form $form
     * @return $this
     */
    public function setForm(\Magento\Framework\Data\Form $form)
    {
        $this->_form = $form;
        $this->_form->setParent($this);
        $this->_form->setBaseUrl($this->getBaseUrl());
        return $this;
    }

    /**
     * @return $this
     * @SuppressWarnings(PHPMD.CyclomaticComplexity)
     * @SuppressWarnings(PHPMD.NPathComplexity)
     * @phpcs:disable Generic.Files.LineLength.TooLong
     */
    protected function _prepareForm()
    {
        $form = $this->_objectManager->get(\Magento\Framework\Data\Form::class);
        $form->setAction($this->getUrl('*/*/savemethods', ['section' => AbstractModel::SHIPPING_SECTION]))
            ->setId('form-validate')
            ->setMethod('POST')
            ->setEnctype('multipart/form-data')
            ->setUseContainer(true);

        $seller = $this->getSeller();

        $methods = $this->_objectManager->get(\Lof\MarketPlace\Model\Source\Shipping\Methods::class)->getMethods();

        if (count($methods) > 0) {
            foreach ($methods as $code => $method) {
                if (!isset($method['model'])) {
                    continue;
                }
                $model = $this->_objectManager->create($method['model']);
                $fields = $model->getFields();
                if (count($fields) > 0) {
                    $key_tmp = $this->_objectManager->get(\Lof\MarketPlace\Helper\Data::class)->getTableKey('key');
                    $seller_id_tmp = $this->_objectManager->get(\Lof\MarketPlace\Helper\Data::class)
                        ->getTableKey('seller_id');
                    $fieldset = $form->addFieldset('shipping_' . $code, ['legend' => $model->getLabel('label')]);
                    foreach ($fields as $id => $field) {
                       $key = strtolower(AbstractModel::SHIPPING_SECTION . '/' . $code . '/' . $id);
                       $setting = $this->_objectManager->create(\Lof\MarketPlace\Model\Config::class)
                            ->loadByField([$key_tmp, $seller_id_tmp], [$key, (int)$seller->getId()]);
                        $settingValue = null;
                        if ($setting) {
                            $settingValue = $setting->getValue();
                        }
                        if($settingValue === null){
                            $value = $field['values'];
                        }else{
                            $value = $settingValue;
                        }
                        $fieldset->addField(
                            $code . $model->getCodeSeparator() . $id,
                            isset($field['type']) ? $field['type'] : 'text',
                            [
                                'label' => $model->getLabel($id),
                                'value' => $value,
                                'name' => 'groups[' . $model->getCode() . '][' . $id . ']',
                                isset($field['class']) ? 'class' : '' => isset($field['class']) ? $field['class'] : '',
                                isset($field['required']) ? 'required' : '' => isset($field['required']) ? $field['required'] : '',
                                isset($field['onchange']) ? 'onchange' : '' => isset($field['onchange']) ? $field['onchange'] : '',
                                isset($field['onclick']) ? 'onclick' : '' => isset($field['onclick']) ? $field['onclick'] : '',
                                isset($field['href']) ? 'href' : '' => isset($field['href']) ? $field['href'] : '',
                                isset($field['target']) ? 'target' : '' => isset($field['target']) ? $field['target'] : '',
                                isset($field['values']) ? 'values' : '' => isset($field['values']) ? $field['values'] : '',
                                isset($field['after_element_html']) ? 'after_element_html' : '' => isset($field['after_element_html']) ? '<div><small>' . $field['after_element_html'] . '</small></div>' : '',
                            ]
                        );
                    }
                }
            }
        } else {
            $form->addField('default_message', 'note', ['text' => __('No Shipping Methods are Available.')]);
        }
        $this->setForm($form);
        return $this;
    }

    /**
     * @return Methods
     */
    protected function _beforeToHtml()
    {
        $this->_prepareForm();
        $this->_initFormValues();
        return parent::_beforeToHtml();
    }

    /**
     * @return $this
     */
    protected function _initFormValues()
    {
        return $this;
    }

    /**
     * @param $attributes
     * @param $fieldset
     * @param array $exclude
     * @SuppressWarnings(PHPMD.CyclomaticComplexity)
     */
    protected function _setFieldset($attributes, $fieldset, $exclude = [])
    {
        $this->_addElementTypes($fieldset);
        foreach ($attributes as $attribute) {
            /* @var $attribute Mage_Eav_Model_Entity_Attribute */
            if (!$attribute || ($attribute->hasIsVisible() && !$attribute->getIsVisible())) {
                continue;
            }
            if (($inputType = $attribute->getFrontend()->getInputType())
                && !in_array($attribute->getAttributeCode(), $exclude)
                && ('media_image' != $inputType)
            ) {

                $fieldType = $inputType;
                $rendererClass = $attribute->getFrontend()->getInputRendererClass();
                if (!empty($rendererClass)) {
                    $fieldType = $inputType . '_' . $attribute->getAttributeCode();
                    $fieldset->addType($fieldType, $rendererClass);
                }

                $element = $fieldset->addField(
                    $attribute->getAttributeCode(),
                    $fieldType,
                    [
                        'name' => $attribute->getAttributeCode(),
                        'label' => $attribute->getFrontend()->getLabel(),
                        'class' => $attribute->getFrontend()->getClass(),
                        'required' => $attribute->getIsRequired(),
                        'note' => $attribute->getNote(),
                    ]
                )
                    ->setEntityAttribute($attribute);

                $element->setAfterElementHtml($this->_getAdditionalElementHtml($element));

                if ($inputType == 'select') {
                    $element->setValues($attribute->getSource()->getAllOptions(true, true));
                } elseif ($inputType == 'multiselect') {
                    $element->setValues($attribute->getSource()->getAllOptions(false, true));
                    $element->setCanBeEmpty(true);
                } elseif ($inputType == 'date') {
                    $element->setImage($this->getSkinUrl('images/calendar.gif'));
                    $element->setFormat($this->getDateFormat(\IntlDateFormatter::SHORT));
                } elseif ($inputType == 'datetime') {
                    $element->setImage($this->getSkinUrl('images/calendar.gif'));
                    $element->setTime(true);
                    $element->setStyle('width:50%;');
                    $element->setFormat($this->getDateTimeFormat(\IntlDateFormatter::SHORT));
                } elseif ($inputType == 'multiline') {
                    $element->setLineCount($attribute->getMultilineCount());
                }
            }
        }
    }

    /**
     * @param \Magento\Framework\Data\Form\AbstractForm $baseElement
     */
    protected function _addElementTypes(\Magento\Framework\Data\Form\AbstractForm $baseElement)
    {
        $types = $this->_getAdditionalElementTypes();
        foreach ($types as $code => $className) {
            $baseElement->addType($code, $className);
        }
    }

    /**
     * @return array
     */
    protected function _getAdditionalElementTypes()
    {
        return [];
    }

    /**
     * @param $element
     * @return string
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    protected function _getAdditionalElementHtml($element)
    {
        return '';
    }
}
