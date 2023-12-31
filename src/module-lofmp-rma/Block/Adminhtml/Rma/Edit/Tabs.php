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

namespace Lofmp\Rma\Block\Adminhtml\Rma\Edit;

use Magento\Backend\Block\Widget\Tabs as WidgetTabs;

class Tabs extends WidgetTabs
{
    /**
     * {@inheritdoc}
     */
    protected function _construct()
    {
        parent::_construct();
        $this->setId('rma_tabs');
        $this->setDestElementId('edit_form');
    }

    /**
     * {@inheritdoc}
     */
    protected function _beforeToHtml()
    {
        $this->addTab('general_section', [
            'label' => __('General Information'),
            'title' => __('General Information'),
            'content' => $this->getLayout()->createBlock(
                '\Lofmp\Rma\Block\Adminhtml\Rma\Edit\Tab\GeneralInfo'
            )->toHtml(),
        ]);
        $this->addTab('fields', [
            'label' => __('More Information'),
            'title' => __('Field'),
            'content' => $this->getLayout()->createBlock(
                '\Lofmp\Rma\Block\Adminhtml\Rma\Edit\Tab\Field'
            )->toHtml(),
        ]);

        $this->addTab('items', [
            'label' => __('Items'),
            'title' => __('Items'),
            'content' => $this->getLayout()->createBlock('\Lofmp\Rma\Block\Adminhtml\Rma\Edit\Tab\Items')
                ->setTemplate('rma/edit/form/items.phtml')
                ->toHtml(),
        ]);

        $this->addTab('messages', [
            'label' => __('Messages'),
            'title' => __('Messages'),
            'content' => $this->getLayout()->createBlock('\Lofmp\Rma\Block\Adminhtml\Rma\Edit\Tab\History')
                ->setTemplate('rma/edit/form/history.phtml')
                ->toHtml(),

        ]);

        return parent::_beforeToHtml();
    }
}
