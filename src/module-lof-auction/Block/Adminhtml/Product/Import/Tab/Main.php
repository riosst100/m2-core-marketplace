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
 * @package    Lof_Auction
 * @copyright  Copyright (c) 2021 Landofcoder (https://www.landofcoder.com/)
 * @license    https://landofcoder.com/terms
 */

namespace Lof\Auction\Block\Adminhtml\Product\Import\Tab;

use Magento\Backend\Block\Template\Context;
use Magento\Backend\Block\Widget\Tab\TabInterface;
use Magento\Framework\Data\FormFactory;
use Magento\Framework\Phrase;
use Magento\Framework\Registry;
use Magento\Store\Model\System\Store;

/**
 * Class Main
 *
 * @package Lof\Auction\Block\Adminhtml\Product\Import\Tab
 */
class Main extends \Magento\Backend\Block\Widget\Form\Generic implements TabInterface
{
    /**
     * @var Store
     */
    protected $_systemStore;

    /**
     * Core registry
     *
     * @var Registry
     */
    protected $_coreRegistry;

    /**
     * @param Context     $context
     * @param Registry    $registry
     * @param FormFactory $formFactory
     * @param Store       $systemStore
     * @param array       $data
     */
    public function __construct(
        Context $context,
        Registry $registry,
        FormFactory $formFactory,
        Store $systemStore,
        array $data = []
    ) {
        $this->_systemStore = $systemStore;
        parent::__construct($context, $registry, $formFactory, $data);
    }

    /**
     * @return Main
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    protected function _prepareForm()
    {
        // $form     = $this->_formFactory->create(
        //     [
        //         'data' =>
        //             [
        //                 'id' => 'edit_form',
        //                 'action' => $this->getData('action'),
        //                 'method' => 'post',
        //                 'enctype' => 'multipart/form-data',
        //             ],
        //     ]
        // );
        $form = $this->_formFactory->create();
        $fieldset = $form->addFieldset(
            'base_fieldset',
            [
                'legend' => __('CSV File'),
                'class' => 'fieldset-wide',
            ]
        );
        $fieldset->addField(
            'title',
            'file',
            [
                'label' => __('Import CSV'),
                'class' => 'required-entry',
                'required' => true,
                'name' => 'import_file',
            ]
        );
        $fieldset->addField(
            'sample_file',
            'link',
            [
                'href' => $this->getViewFileUrl('Lof_Auction::adminProductInfo.csv'),
                'value' => 'Download Sample File',
            ]
        );
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
        return __('Product Data');
    }

    /**
     * Prepare title for tab
     *
     * @return Phrase
     */
    public function getTabTitle()
    {
        return __('Product Data');
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
