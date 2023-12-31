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
namespace Lof\Auction\Block\Adminhtml\Product\Import;

/**
 * Class Tabs
 * @package Lof\Auction\Block\Adminhtml\Product\Import
 */
class Tabs extends \Magento\Backend\Block\Widget\Tabs
{
    /**
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    protected function _construct()
    {
        parent::_construct();
        $this->setId('product_tabs');
        $this->setDestElementId('edit_form');
        $this->setTitle(__('Import'));

        $this->addTab(
            'import_product',
            [
            'label' => __('Import Product'),
            'content' => $this->getLayout()->createBlock('Lof\Auction\Block\Adminhtml\Product\Import\Tab\Main')->toHtml()
            ]
        );
    }
}
