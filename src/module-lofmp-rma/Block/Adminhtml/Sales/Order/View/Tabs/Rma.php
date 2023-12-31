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



namespace Lofmp\Rma\Block\Adminhtml\Sales\Order\View\Tabs;

use Magento\Backend\Block\Widget;
use Magento\Backend\Block\Widget\Context;
use Magento\Backend\Block\Widget\Tab\TabInterface;

// use Lofmp\Rma\Api\Service\Order\OrderManagementInterface as RmaHelper;

class Rma extends Widget implements TabInterface
{
    public function __construct(
        // RmaHelper $rmaHelper,
        Context $context,
        array $data = []
    ) {
        // $this->rmaHelper = $rmaHelper;

        parent::__construct($context, $data);
    }

    /**
     * {@inheritdoc}
     */
    public function getTabLabel()
    {
        return __('RMA');
    }

    /**
     * {@inheritdoc}
     */
    public function getTabTitle()
    {
        return __('RMA');
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
     * {@inheritdoc}
     */
    protected function _toHtml()
    {
        $id = $this->getRequest()->getParam('order_id');
        $rmaNewUrl = $this->getUrl('rma/rma/add', ['order_id' => $id]);
        $button = $this->getLayout()->createBlock('\Magento\Backend\Block\Widget\Button')
            ->setClass('add')
            ->setType('button')
            ->setOnClick('window.location.href=\'' . $rmaNewUrl . '\'')
            ->setLabel(__('Create RMA for this order'));

        $grid = $this->getLayout()->createBlock('\Lofmp\Rma\Block\Adminhtml\Rma\Grid');
        $grid->setId('rma_grid_internal');
        $grid->setActiveTab('RMA');
        $grid->addCustomFilter('order_id', $id);
        $grid->setFilterVisibility(false);
        $grid->setExportVisibility(false);
        $grid->setPagerVisibility(0);

        $grid->setTabMode(true);

        /*if ($this->rmaHelper->isAllowToReturn($id)) {
            $meetMessage = __('Order meets RMA policy');
        } else {
            $meetMessage = __('Order doesn\'t meet RMA policy');
        }*/
        $meetMessage = __('Order doesn\'t meet RMA policy');
        return '<br>
        <div>' . $button->toHtml() . '<div style="float:right;color:#eb5e00"><i>' . $meetMessage . '</i></div>
        <br><br>' . $grid->toHtml() . '</div>';
    }
}
