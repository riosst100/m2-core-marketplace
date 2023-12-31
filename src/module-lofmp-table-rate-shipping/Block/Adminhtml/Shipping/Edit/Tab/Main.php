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
 * @package    Lofmp_TableRateShipping
 * @copyright  Copyright (c) 2021 Landofcoder (https://www.landofcoder.com/)
 * @license    https://landofcoder.com/terms
 */

namespace Lofmp\TableRateShipping\Block\Adminhtml\Shipping\Edit\Tab;

use \Magento\Backend\Block\Widget\Tab\TabInterface;

class Main extends \Magento\Backend\Block\Widget\Form\Generic implements TabInterface
{
    /**
     * @var \Magento\Store\Model\System\Store
     */
    protected $_systemStore;

    /**
     * @var
     */
    protected $_coreRegistry;

    /**
     * @var \Lofmp\TableRateShipping\Model\Config\Source\Country
     */
    protected $country;

    /**
     * @var \Lofmp\TableRateShipping\Model\Config\Source\SellerOption
     */
    protected $sellerOption;

    /**
     * @var \Lofmp\TableRateShipping\Model\ShippingmethodFactory
     */
    protected $shippingMethod;

    /**
     * @param \Magento\Backend\Block\Template\Context $context
     * @param \Magento\Framework\Registry $registry
     * @param \Magento\Framework\Data\FormFactory $formFactory
     * @param \Magento\Store\Model\System\Store $systemStore
     * @param \Lofmp\TableRateShipping\Model\Config\Source\Country $country
     * @param \Lofmp\TableRateShipping\Model\Config\Source\SellerOption $sellerOption
     * @param \Lofmp\TableRateShipping\Model\ShippingmethodFactory $shippingmethodFactory
     * @param array $data
     */
    public function __construct(
        \Magento\Backend\Block\Template\Context $context,
        \Magento\Framework\Registry $registry,
        \Magento\Framework\Data\FormFactory $formFactory,
        \Magento\Store\Model\System\Store $systemStore,
        \Lofmp\TableRateShipping\Model\Config\Source\Country $country,
        \Lofmp\TableRateShipping\Model\Config\Source\SellerOption $sellerOption,
        \Lofmp\TableRateShipping\Model\ShippingmethodFactory $shippingmethodFactory,
        array $data = []
    ) {
        $this->country = $country;
        $this->shippingMethod = $shippingmethodFactory;
        $this->sellerOption = $sellerOption;
        $this->_systemStore = $systemStore;
        parent::__construct($context, $registry, $formFactory, $data);
    }

    /**
     * Prepare form
     *
     * @return $this
     * @SuppressWarnings(PHPMD.ExcessiveMethodLength)
     */
    protected function _prepareForm()
    {
        $model = $this->_coreRegistry->registry('lofmptablerateshipping_shipping');

        if ($this->_isAllowedAction('Lofmp_TableRateShipping::shipping')) {
            $isElementDisabled = false;
        } else {
            $isElementDisabled = true;
        }

        $form = $this->_formFactory->create();
        $form->setHtmlIdPrefix('page_');

        $fieldset = $form->addFieldset('base_fieldset', ['legend' => __('Shipping Information')]);

        if ($model->getId()) {
            $fieldset->addField('lofmpshipping_id', 'hidden', ['name' => 'lofmpshipping_id']);
        }
        $fieldset->addField(
            'shipping_method',
            'text',
            [
                'name' => 'shipping_method',
                'label' => __('Shipping Method Name'),
                'title' => __('Shipping Method Name'),
                'required' => false,
                'disabled' => $isElementDisabled
            ]
        );
        $fieldset->addField(
            'partner_id',
            'select',
            [
                'name' => 'partner_id',
                'label' => __('Seller'),
                'title' => __('Seller'),
                'required' => false,
                'values' => $this->sellerOption->toOptionArray(),
                'disabled' => $isElementDisabled
            ]
        );
        $fieldset->addField(
            'dest_country_id',
            'select',
            [
                'name' => 'dest_country_id',
                'label' => __('Country code'),
                'title' => __('Country code'),
                'required' => true,
                'values' => $this->country->toOptionArray(),
                'disabled' => $isElementDisabled
            ]
        );
        $fieldset->addField(
            'dest_region_id',
            'text',
            [
                'name' => 'dest_region_id',
                'label' => __('Region code'),
                'title' => __('Region code'),
                'required' => true,
                'after_element_html' => __('Enter specific region code or enter * for all'),
                'disabled' => $isElementDisabled
            ]
        );
        $fieldset->addField(
            'weight_from',
            'text',
            [
                'name' => 'weight_from',
                'label' => __('Weight from'),
                'title' => __('Weight from'),
                'required' => true,
                'after_element_html' => __("Enter weight from to calculate shipping"),
                'disabled' => $isElementDisabled
            ]
        );
        $fieldset->addField(
            'weight_to',
            'text',
            [
                'name' => 'weight_to',
                'label' => __('Weight to'),
                'title' => __('Weight to'),
                'required' => true,
                'after_element_html' => __('Enter weight to to calculate shipping'),
                'disabled' => $isElementDisabled
            ]
        );
        $fieldset->addField(
            'dest_zip',
            'text',
            [
                'name' => 'dest_zip',
                'label' => __('Zip from'),
                'title' => __('Zip from'),
                'required' => true,
                'after_element_html' => __('Enter specific zip or * for all'),
                'disabled' => $isElementDisabled
            ]
        );
        $fieldset->addField(
            'dest_zip_to',
            'text',
            [
                'name' => 'dest_zip_to',
                'label' => __('Zip to'),
                'title' => __('Zip to'),
                'after_element_html' => __('Enter specific zip or * for all'),
                'required' => true,
                'disabled' => $isElementDisabled
            ]
        );
        $fieldset->addField(
            'free_shipping',
            'text',
            [
                'name' => 'free_shipping',
                'label' => __('Free Shipping'),
                'title' => __('Free Shipping'),
                'required' => false,
                'after_element_html' => __('Minimum cart total amount for free shipping.
                Keep empty if not offering free shipping.'),
                'disabled' => $isElementDisabled
            ]
        );
        $fieldset->addField(
            'cart_total',
            'text',
            [
                'name' => 'cart_total',
                'label' => __('Minimum Cart Total Amount Condition'),
                'title' => __('Minimum Cart Total Amount Condition'),
                'required' => false,
                'after_element_html' => __('Minimum cart total amount will check condition for rate.
                Keep empty for zero and above.'),
                'disabled' => $isElementDisabled
            ]
        );
        $fieldset->addField(
            'price',
            'text',
            [
                'name' => 'price',
                'label' => __('Price'),
                'title' => __('Price'),
                'required' => true,
                'after_element_html' => __("Enter shipping price"),
                'disabled' => $isElementDisabled
            ]
        );
//        $fieldset->addField(
//            'cost',
//            'text',
//            [
//                'name' => 'cost',
//                'label' => __('Cost'),
//                'title' => __('Cost'),
//                'required' => false,
//                'after_element_html' => __('Enter shipping cost'),
//                'disabled' => $isElementDisabled
//            ]
//        );
        $shippingMethod = $this->shippingMethod->create()->load($model->getShippingMethodId());
        $data = $model->getData();
        if ($shippingMethod) {
            $data['shipping_method'] = $shippingMethod->getMethodName();
        }
        $form->setValues($data);
        $this->setForm($form);
        return parent::_prepareForm();
    }

    /**
     * Prepare label for tab
     *
     * @return \Magento\Framework\Phrase
     */
    public function getTabLabel()
    {
        return __('Shipping Data');
    }

    /**
     * Prepare title for tab
     *
     * @return \Magento\Framework\Phrase
     */
    public function getTabTitle()
    {
        return __('Shipping Data');
    }

    /**
     * {@inheritdoc}
     */
    public function canShowTab()
    {
        return true;
    }

    /**
     * {@inheritdoc}
     */
    public function isHidden()
    {
        return false;
    }

    /**
     * Check permission for passed action
     *
     * @param string $resourceId
     * @return bool
     */
    protected function _isAllowedAction($resourceId)
    {
        return $this->_authorization->isAllowed($resourceId);
    }
}
