<?php
/**
 * Landofcoder
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Landofcoder.com license that is
 * available through the world-wide-web at this URL:
 * http://landofcoder.com/license
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade this extension to newer
 * version in the future.
 *
 * @category   Landofcoder
 * @package    Lofmp_PriceComparison
 *
 * @copyright  Copyright (c) 2016 Landofcoder (http://www.landofcoder.com/)
 * @license    http://www.landofcoder.com/LICENSE-1.0.html
 */
namespace Lofmp\PriceComparison\Block\Adminhtml\Product\Edit;

class Tabs extends \Magento\Backend\Block\Widget\Tabs
{
    protected function _construct()
    {
        parent::_construct();
        $this->setId('product_tabs');
        $this->setDestElementId('edit_form');
        $this->setTitle(__('Information'));

        $this->addTab(
            'shipping_information',
            [
            'label' => __('Product Information'),
            'content' => $this->getLayout()->createBlock('Lofmp\PriceComparison\Block\Adminhtml\Product\Edit\Tab\Main')->toHtml()
            ]
        );
    }
}
