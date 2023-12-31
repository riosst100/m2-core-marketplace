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
 * @package    Lofmp_SellerIdentificationApproval
 * @copyright  Copyright (c) 2021 Landofcoder (https://www.landofcoder.com/)
 * @license    https://landofcoder.com/terms
 */

namespace Lofmp\SellerIdentificationApproval\Block\Adminhtml\Seller\Edit\Tab;

use Lofmp\SellerIdentificationApproval\Block\Seller\Register;
use Lofmp\SellerIdentificationApproval\Helper\Data;
use Magento\Backend\Block\Template\Context;
use Magento\Backend\Block\Widget\Form\Generic;
use Magento\Backend\Block\Widget\Tab\TabInterface;
use Magento\Framework\Data\FormFactory;
use Magento\Framework\Phrase;
use Magento\Framework\Registry;
use Magento\Framework\View\Design\Theme\LabelFactory;
use Magento\Framework\View\Model\PageLayout\Config\BuilderInterface;
use Magento\Theme\Model\Layout\Source\Layout;

/**
 * @SuppressWarnings(PHPMD.CamelCasePropertyName)
 */
class IdentificationVerification extends Generic implements TabInterface
{
    /**
     * @var LabelFactory
     */
    protected $_labelFactory;

    /**
     * @var Layout
     */
    protected $_pageLayout;

    /**
     * @var BuilderInterface
     */
    protected $pageLayoutBuilder;

    /**
     * @var Data
     */
    private $helper;

    /**
     * @param Context $context
     * @param Registry $registry
     * @param FormFactory $formFactory
     * @param Layout $pageLayout
     * @param LabelFactory $labelFactory
     * @param BuilderInterface $pageLayoutBuilder
     * @param Data $helper
     * @param array $data
     */
    public function __construct(
        Context $context,
        Registry $registry,
        FormFactory $formFactory,
        Layout $pageLayout,
        LabelFactory $labelFactory,
        BuilderInterface $pageLayoutBuilder,
        Data $helper,
        array $data = []
    ) {
        $this->pageLayoutBuilder = $pageLayoutBuilder;
        $this->_labelFactory = $labelFactory;
        $this->_pageLayout = $pageLayout;
        $this->helper = $helper;
        parent::__construct($context, $registry, $formFactory, $data);
    }

    /**
     * Prepare form tab configuration
     * @return void
     * @SuppressWarnings(PHPMD.CamelCaseMethodName)
     */
    //phpcs:ignore Generic.CodeAnalysis.UselessOverridingMethod.Found
    protected function _construct()
    {
        parent::_construct();
    }

    /**
     * Initialise form fields
     * @return $this
     * @SuppressWarnings(PHPMD.ExcessiveMethodLength)
     * @SuppressWarnings(PHPMD.CamelCaseMethodName)
     */
    protected function _prepareForm()
    {
        $model = $this->_coreRegistry->registry('lof_marketplace_seller');

        $form = $this->_formFactory->create();

        $form->setHtmlIdPrefix('seller_');

        $fieldset = $form->addFieldset('base_fieldset', ['legend' => __('Identification Verification')]);

        $fieldset->addField(
            'identification_request',
            'select',
            [
                'label' => __('Identification Requested'),
                'title' => __('Identification Requested'),
                'name' => 'identification',
                'options' => $this->getOptions()
            ]
        );

        $this->setChild(
            'form_after',
            $this->getLayout()->createBlock(Register::class)
                ->setTemplate('Lofmp_SellerIdentificationApproval::seller/upload.phtml')
                ->setNameInLayout('additional_fields')
        );

        $this->getChildHtml('additional_fields');

        $form->setValues($model->getData());

        $this->setForm($form);
        return parent::_prepareForm();
    }

    /**
     * Prepare label for tab
     *
     * @return Phrase
     */
    public function getTabLabel()
    {
        return __('Identification Verification');
    }

    /**
     * Prepare title for tab
     *
     * @return Phrase
     */
    public function getTabTitle()
    {
        return __('Identification Verification');
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
     * @SuppressWarnings(PHPMD.CamelCaseMethodName)
     */
    protected function _isAllowedAction($resourceId)
    {
        return $this->_authorization->isAllowed($resourceId);
    }

    /**
     * @return array
     * @SuppressWarnings(PHPMD.CamelCaseMethodName)
     * @SuppressWarnings(PHPMD.UnusedLocalVariable)
     */
    public function getOptions()
    {
        $options = $this->helper->getIdentificationTypes();
        foreach ($options as $key => $option) {
            if (!$this->helper->isEnable($key)) {
                unset($options[$key]);
            }
        }
        return $options;
    }
}
