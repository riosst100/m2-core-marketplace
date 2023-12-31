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

namespace Lof\MarketPlace\Block\Adminhtml\Withdrawal\Edit\Tab;

class Main extends \Magento\Backend\Block\Widget\Form\Generic implements \Magento\Backend\Block\Widget\Tab\TabInterface
{
    /**
     * @var \Magento\Store\Model\System\Store
     */
    protected $_systemStore;

    /**
     * @var \Magento\Cms\Model\Wysiwyg\Config
     */
    protected $_wysiwygConfig;

    /**
     * @var \Magento\Cms\Model\Wysiwyg\Config
     */
    protected $_wysiwygConfig1;

    /**
     * @var \Lof\MarketPlace\Model\ResourceModel\Payment\CollectionFactory
     */
    protected $paymentCollectionFactory;

    /**
     * @param \Magento\Backend\Block\Template\Context $context
     * @param \Magento\Framework\Registry $registry
     * @param \Magento\Framework\Data\FormFactory $formFactory
     * @param \Magento\Store\Model\System\Store $systemStore
     * @param \Magento\Cms\Model\Wysiwyg\Config $wysiwygConfig
     * @param \Lof\MarketPlace\Model\ResourceModel\Payment\CollectionFactory $paymentCollectionFactory
     * @param array $data
     */
    public function __construct(
        \Magento\Backend\Block\Template\Context $context,
        \Magento\Framework\Registry $registry,
        \Magento\Framework\Data\FormFactory $formFactory,
        \Magento\Store\Model\System\Store $systemStore,
        \Magento\Cms\Model\Wysiwyg\Config $wysiwygConfig,
        \Lof\MarketPlace\Model\ResourceModel\Payment\CollectionFactory $paymentCollectionFactory,
        array $data = []
    ) {
        $this->_systemStore = $systemStore;
        $this->_wysiwygConfig = $wysiwygConfig;
        $this->_wysiwygConfig1 = $wysiwygConfig;
        $this->paymentCollectionFactory = $paymentCollectionFactory;
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
        /** @var $model \Lof\MarketPlace\Model\Withdrawal */
        $model = $this->_coreRegistry->registry('lof_marketplace_withdrawal');

        /**
         * Checking if user have permission to save information
         */
        if ($this->_isAllowedAction('Lof_MarketPlace::withdrawal_edit')) {
            $isElementDisabled = false;
        } else {
            $isElementDisabled = true;
        }

        /** @var \Magento\Framework\Data\Form $form */
        $form = $this->_formFactory->create();

        $form->setHtmlIdPrefix('seller_');

        $fieldset = $form->addFieldset('base_fieldset', ['legend' => __('Withdrawal Information')]);

        if ($model->getId()) {
            $fieldset->addField('withdrawal_id', 'hidden', ['name' => 'withdrawal_id']);
        }

        $fieldset->addField(
            'payment_id',
            'select',
            [
                'label' => __('Payment Id'),
                'title' => __('Payment Id'),
                'name' => 'payment_id',
                'required' => true,
                'options' => $this->getPaymentList(),
                'disabled' => $isElementDisabled
            ]
        );

        $fieldset->addField(
            'seller_id',
            'text',
            [
                'name' => 'seller_id',
                'label' => __('Seller Id'),
                'title' => __('Seller Id'),
                'required' => true,
                'disabled' => $isElementDisabled
            ]
        );

        $fieldset->addField(
            'email',
            'text',
            [
                'name' => 'email',
                'label' => __('Payment Email'),
                'title' => __('Payment Email'),
                'required' => true,
                'disabled' => $isElementDisabled
            ]
        );

        $fieldset->addField(
            'amount',
            'text',
            [
                'name' => 'amount',
                'label' => __('Amount'),
                'title' => __('Amount'),
                'required' => true,
                'disabled' => $isElementDisabled
            ]
        );

        $fieldset->addField(
            'comment',
            'textarea',
            [
                'name' => 'comment',
                'style' => 'height:200px;',
                'label' => __('Comment'),
                'title' => __('Comment'),
                'disabled' => $isElementDisabled,
            ]
        );

        $fieldset->addField(
            'admin_comment',
            'textarea',
            [
                'name' => 'admin_comment',
                'label' => __('Admin Comment'),
                'title' => __('Admin Comment'),
                'disabled' => $isElementDisabled,
            ]
        );

        $form->setValues($model->getData());
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
        return __('Withdrawal Information');
    }

    /**
     * Prepare title for tab
     *
     * @return \Magento\Framework\Phrase
     */
    public function getTabTitle()
    {
        return __('Withdrawal Information');
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

    /**
     * get payment list
     *
     * @return mixed|array
     */
    protected function getPaymentList()
    {
        $collection = $this->paymentCollectionFactory->create();
        $collection->getSelect()->order("main_table.order ASC");
        $return = [];
        if ($collection->getSize()) {
            foreach ($collection as $item) {
                $return[$item->getId()] = __("%1 - (Min: %2, Max: %3, Fee: %4)", $item->getName(), $item->getMinAmount(), $item->getMaxAmount(), $item->getFee());
            }
        }
        return $return;
    }
}
