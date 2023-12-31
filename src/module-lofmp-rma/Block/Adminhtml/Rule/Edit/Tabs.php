<?php
/**
 * LandOfCoder
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Landofcoder.com license that is
 * available through the world-wide-web at this URL:
 * https://landofcoder.com/license-agreement.html
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade this extension to newer
 * version in the future.
 *
 * @category   LandOfCoder
 * @package    Lofmp_Rma
 * @copyright  Copyright (c) 2021 Landofcoder (https://landofcoder.com/)
 * @license    https://landofcoder.com/LICENSE-1.0.html
 */



namespace Lofmp\Rma\Block\Adminhtml\Rule\Edit;

use Magento\Backend\Block\Widget\Tabs as WidgetTabs;

class Tabs extends WidgetTabs
{
    /**
     * {@inheritdoc}
     */
    protected function _construct()
    {
        parent::_construct();
        $this->setId('rule_tabs');
        $this->setDestElementId('edit_form');
    }

    /**
     * {@inheritdoc}
     */
    protected function _beforeToHtml()
    {
        $this->addTab('general_section', [
            'label'   => __('General Information'),
            'title'   => __('General Information'),
            'content' => $this->getLayout()->createBlock(
                '\Lofmp\Rma\Block\Adminhtml\Rule\Edit\Tab\General'
            )->toHtml(),
        ]);

        $this->addTab('condition_section', [
            'label'   => __('Conditions'),
            'title'   => __('Conditions'),
            'content' => $this->getLayout()->createBlock(
                '\Lofmp\Rma\Block\Adminhtml\Rule\Edit\Tab\Condition'
            )->toHtml(),
        ]);

        $this->addTab('action_section', [
            'label'   => __('Actions'),
            'title'   => __('Actions'),
            'content' => $this->getLayout()->createBlock(
                '\Lofmp\Rma\Block\Adminhtml\Rule\Edit\Tab\Action'
            )->toHtml(),
        ]);

        $this->addTab('notification_section', [
            'label'   => __('Notifications'),
            'title'   => __('Notifications'),
            'content' => $this->getLayout()->createBlock(
                '\Lofmp\Rma\Block\Adminhtml\Rule\Edit\Tab\Notification'
            )->toHtml(),
        ]);

        return parent::_beforeToHtml();
    }
}
