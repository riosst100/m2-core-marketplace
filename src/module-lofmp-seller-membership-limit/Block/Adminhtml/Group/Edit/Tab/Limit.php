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
namespace Lofmp\SellerMembershipLimit\Block\Adminhtml\Group\Edit\Tab;

class Limit extends \Magento\Backend\Block\Widget\Form\Generic implements \Magento\Backend\Block\Widget\Tab\TabInterface
{
    /**
     * Prepare form
     *
     * @return $this
     */
    protected function _prepareForm()
    {
        /*
         * Checking if user have permissions to save information
         */
        if ($this->_isAllowedAction('Lof_MarketPlace::group_edit')) {
            $isElementDisabled = false;
        } else {
            $isElementDisabled = true;
        }

        /** @var \Magento\Framework\Data\Form $form */
        $form = $this->_formFactory->create();

        $form->setHtmlIdPrefix('limit_');

        $model = $this->_coreRegistry->registry('lof_marketplace_group');

        $fieldset = $form->addFieldset(
            'limit_fieldset',
            ['legend' => __('Limit Data'), 'class' => 'fieldset-wide']
        );

        $fieldset->addField(
            'limit_product_duration',
            'text',
            [
                'name' => 'limit_product_duration',
                'label' => __('Limit Products per Duration'),
                'title' => __('Limit Products per Duration'),
                'required' => false,
                'note' => __('Number of limited products when sellers submit. Unlimit set: "-1"'),
                'disabled' => $isElementDisabled
            ]
        );

        $fieldset->addField(
            'limit_auction_duration',
            'text',
            [
                'name' => 'limit_auction_duration',
                'label' => __('Limit Auctions per Duration'),
                'title' => __('Limit Auctions per Duration'),
                'required' => false,
                'note' => __('Number of limited products when sellers create auction products. Unlimit set: "-1"'),
                'disabled' => $isElementDisabled
            ]
        );

//        $fieldset->addField(
//            'trial_days',
//            'text',
//            [
//                'name' => 'trial_days',
//                'label' => __('Trial Days'),
//                'title' => __('Trial Days'),
//                'required' => false,
//                'note' => __('Set "0" or empty for no trial permission.'),
//                'disabled' => $isElementDisabled
//            ]
//        );
        if ($model->getGroupId()) {
            $form->setValues($model->getData());
        }

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
        return __('Limit Data');
    }

    /**
     * Prepare title for tab
     *
     * @return \Magento\Framework\Phrase
     */
    public function getTabTitle()
    {
        return __('Limit Data');
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
