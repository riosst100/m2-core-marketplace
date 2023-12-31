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

namespace Lof\MarketPlace\Block\Adminhtml\ShippingCommission\Edit;

class Tabs extends \Magento\Backend\Block\Widget\Tabs
{
    /**
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    protected function _construct()
    {
        parent::_construct();
        $this->setId('page_tabs');
        $this->setDestElementId('edit_form');
        $this->setTitle(__('Shipping Commission Information'));

        $this->addTab(
            'main_section',
            [
                'label' => __('General Information'),
                'content' => $this->getLayout()
                    ->createBlock(\Lof\MarketPlace\Block\Adminhtml\ShippingCommission\Edit\Tab\Main::class)->toHtml(),
            ]
        );

        $this->addTab(
            'actions',
            [
                'label' => __('Actions'),
                'content' => $this->getLayout()
                    ->createBlock(\Lof\MarketPlace\Block\Adminhtml\ShippingCommission\Edit\Tab\Actions::class)->toHtml()
            ]
        );
    }
}
