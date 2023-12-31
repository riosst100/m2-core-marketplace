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
 * @package    Lofmp_Faq
 * @copyright  Copyright (c) 2021 Landofcoder (https://www.landofcoder.com/)
 * @license    https://landofcoder.com/terms
 */
namespace Lofmp\Faq\Block\Adminhtml\Category\Edit;

class Tabs extends \Magento\Backend\Block\Widget\Tabs
{
    /**
     * @return void
     */
    protected function _construct()
    {
        parent::_construct();
        $this->setId('question_tabs');
        $this->setDestElementId('edit_form');
        $this->setTitle(__('Category Information'));
    }

    /**
     * @return $this
     * @SuppressWarnings(PHPMD.CyclomaticComplexity)
     * @SuppressWarnings(PHPMD.NPathComplexity)
     * @SuppressWarnings(PHPMD.ExcessiveMethodLength)
     */
    protected function _prepareLayout()
    {
        $this->addTab(
                'main_section',
                [
                    'label' => __('General'),
                    'content' => $this->getLayout()->createBlock('Lofmp\Faq\Block\Adminhtml\Category\Edit\Tab\Main')->toHtml()
                ]
            );

        // $this->addTab(
        //         'questions',
        //         [
        //             'label' => __('Questions'),
        //             'url' => $this->getUrl('lofmpfaq/*/questions', ['_current' => true]),
        //             'class' => 'ajax'
        //         ]
        //     );

        $this->addTab(
                'design',
                [
                    'label' => __('Design'),
                    'content' => $this->getLayout()->createBlock('Lofmp\Faq\Block\Adminhtml\Category\Edit\Tab\Design')->toHtml()
                ]
            );

        $this->addTab(
                'meta',
                [
                    'label' => __('SEO'),
                    'content' => $this->getLayout()->createBlock('Lofmp\Faq\Block\Adminhtml\Category\Edit\Tab\Meta')->toHtml()
                ]
            );
    }
}
