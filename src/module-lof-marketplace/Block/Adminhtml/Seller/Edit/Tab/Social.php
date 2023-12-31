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

namespace Lof\MarketPlace\Block\Adminhtml\Seller\Edit\Tab;

use Magento\Backend\Block\Widget\Tab\TabInterface;

class Social extends \Magento\Backend\Block\Widget\Form\Generic implements TabInterface
{
    /**
     * @var \Lof\MarketPlace\Helper\Data
     */
    protected $helper;

    /**
     * @param \Magento\Backend\Block\Template\Context $context
     * @param \Magento\Framework\Registry $registry
     * @param \Magento\Framework\Data\FormFactory $formFactory
     * @param \Lof\MarketPlace\Helper\Data $helper
     * @param array $data
     */
    public function __construct(
        \Magento\Backend\Block\Template\Context $context,
        \Magento\Framework\Registry $registry,
        \Magento\Framework\Data\FormFactory $formFactory,
        \Lof\MarketPlace\Helper\Data $helper,
        array $data = []
    ) {
        $this->helper = $helper;

        parent::__construct(
            $context,
            $registry,
            $formFactory,
            $data
        );
    }

    /**
     * Prepare form
     *
     * @return $this
     * @SuppressWarnings(PHPMD.ExcessiveMethodLength)
     */
    protected function _prepareForm()
    {
        /*
         * Checking if user have permissions to save information
         */
        if ($this->_isAllowedAction('Lof_MarketPlace::seller_edit')) {
            $isElementDisabled = false;
        } else {
            $isElementDisabled = true;
        }

        /** @var \Magento\Framework\Data\Form $form */
        $form = $this->_formFactory->create();

        $form->setHtmlIdPrefix('seller_');

        $model = $this->_coreRegistry->registry('lof_marketplace_seller');

        $fieldset = $form->addFieldset(
            'meta_fieldset',
            ['legend' => __('Social Infomation'), 'class' => 'fieldset-wide']
        );

        if ($this->helper->isAllowedSocial('twitter')) {
            $fieldset->addField(
                'tw_active',
                'checkbox',
                [
                    'name' => 'tw_active',
                    'checked' => $model->getData('tw_active'),
                    'label' => __('Twitter Active'),
                    'title' => __('Twitter Active'),
                    'data-form-part' => $this->getData('tw_active'),
                    'onchange' => 'this.value = this.checked;',
                    'disabled' => $isElementDisabled
                ]
            );

            $fieldset->addField(
                'twitter_id',
                'text',
                [
                    'name' => 'twitter_id',
                    'label' => __('Twitter'),
                    'title' => __('Twitter'),
                    'disabled' => $isElementDisabled,
                    'note' => __('Please enter a full URL. Ex: https://www.twitter.com/page')
                ]
            );
        }

        if ($this->helper->isAllowedSocial('facebook')) {
            $fieldset->addField(
                'fb_active',
                'checkbox',
                [
                    'name' => 'fb_active',
                    'checked' => $model->getData('fb_active'),
                    'label' => __('Facebook Active'),
                    'title' => __('Facebook Active'),
                    'data-form-part' => $this->getData('fb_active'),
                    'onchange' => 'this.value = this.checked;',
                    'disabled' => $isElementDisabled
                ]
            );

            $fieldset->addField(
                'facebook_id',
                'text',
                [
                    'name' => 'facebook_id',
                    'label' => __('Facebook'),
                    'title' => __('Facebook'),
                    'disabled' => $isElementDisabled,
                    'note' => __('Please enter a full URL. Ex: https://www.facebook.com/page')
                ]
            );
        }

        if ($this->helper->isAllowedSocial('google')) {
            $fieldset->addField(
                'gplus_active',
                'checkbox',
                [
                    'name' => 'gplus_active',
                    'checked' => $model->getData('gplus_active'),
                    'label' => __('Google Plus Active'),
                    'title' => __('Google Plus Active'),
                    'data-form-part' => $this->getData('gplus_active'),
                    'onchange' => 'this.value = this.checked;',
                    'disabled' => $isElementDisabled
                ]
            );

            $fieldset->addField(
                'gplus_id',
                'text',
                [
                    'name' => 'gplus_id',
                    'label' => __('Google Plus'),
                    'title' => __('Google Plus'),
                    'disabled' => $isElementDisabled,
                    'note' => __('Please enter a full URL. Ex: https://plus.google.com/page')
                ]
            );
        }

        if ($this->helper->isAllowedSocial('youtube')) {
            $fieldset->addField(
                'youtube_active',
                'checkbox',
                [
                    'name' => 'youtube_active',
                    'checked' => $model->getData('youtube_active'),
                    'label' => __('Youtube Active'),
                    'title' => __('Youtube Active'),
                    'data-form-part' => $this->getData('youtube_active'),
                    'onchange' => 'this.value = this.checked;',
                    'disabled' => $isElementDisabled
                ]
            );

            $fieldset->addField(
                'youtube_id',
                'text',
                [
                    'name' => 'youtube_id',
                    'label' => __('Youtube'),
                    'title' => __('Youtube'),
                    'disabled' => $isElementDisabled,
                    'note' => __('Please enter a full URL. Ex: https://www.youtube.com/page')
                ]
            );
        }

        if ($this->helper->isAllowedSocial('vimeo')) {
            $fieldset->addField(
                'vimeo_active',
                'checkbox',
                [
                    'name' => 'vimeo_active',
                    'checked' => $model->getData('vimeo_active'),
                    'label' => __('Vimeo Active'),
                    'title' => __('Vimeo Active'),
                    'data-form-part' => $this->getData('vimeo_active'),
                    'onchange' => 'this.value = this.checked;',
                    'disabled' => $isElementDisabled
                ]
            );

            $fieldset->addField(
                'vimeo_id',
                'text',
                [
                    'name' => 'vimeo_id',
                    'label' => __('Vimeo'),
                    'title' => __('Vimeo'),
                    'disabled' => $isElementDisabled,
                    'note' => __('Please enter a full URL. Ex: https://www.vimeo.com/page')
                ]
            );
        }

        if ($this->helper->isAllowedSocial('linkedin')) {
            $fieldset->addField(
                'linkedin_active',
                'checkbox',
                [
                    'name' => 'linkedin_active',
                    'checked' => $model->getData('linkedin_active'),
                    'label' => __('Linkedin Active'),
                    'title' => __('Linkedin Active'),
                    'data-form-part' => $this->getData('linkedin_active'),
                    'onchange' => 'this.value = this.checked;',
                    'disabled' => $isElementDisabled
                ]
            );

            $fieldset->addField(
                'linkedin_id',
                'text',
                [
                    'name' => 'linkedin_id',
                    'label' => __('Linkedin'),
                    'title' => __('Linkedin'),
                    'disabled' => $isElementDisabled,
                    'note' => __('Please enter a full URL. Ex: https://www.linkedin.com/page')
                ]
            );
        }

        if ($this->helper->isAllowedSocial('instagram')) {
            $fieldset->addField(
                'instagram_active',
                'checkbox',
                [
                    'name' => 'instagram_active',
                    'checked' => $model->getData('instagram_active'),
                    'label' => __('Instagram Active'),
                    'title' => __('Instagram Active'),
                    'data-form-part' => $this->getData('instagram_active'),
                    'onchange' => 'this.value = this.checked;',
                    'disabled' => $isElementDisabled
                ]
            );

            $fieldset->addField(
                'instagram_id',
                'text',
                [
                    'name' => 'instagram_id',
                    'label' => __('Instagram'),
                    'title' => __('Instagram'),
                    'disabled' => $isElementDisabled,
                    'note' => __('Please enter a full URL. Ex: https://www.instagram.com/page')
                ]
            );
        }

        if ($this->helper->isAllowedSocial('pinterest')) {
            $fieldset->addField(
                'pinterest_active',
                'checkbox',
                [
                    'name' => 'pinterest_active',
                    'checked' => $model->getData('pinterest_active'),
                    'label' => __('Pinterest Active'),
                    'title' => __('Pinterest Active'),
                    'data-form-part' => $this->getData('pinterest_active'),
                    'onchange' => 'this.value = this.checked;',
                    'disabled' => $isElementDisabled
                ]
            );

            $fieldset->addField(
                'pinterest_id',
                'text',
                [
                    'name' => 'pinterest_id',
                    'label' => __('Pinterest'),
                    'title' => __('Pinterest'),
                    'disabled' => $isElementDisabled,
                    'note' => __('Please enter a full URL. Ex: https://www.pinterest.com/page')
                ]
            );
        }

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
        return __('Social Infomation');
    }

    /**
     * Prepare title for tab
     *
     * @return \Magento\Framework\Phrase
     */
    public function getTabTitle()
    {
        return __('Social Infomation');
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
