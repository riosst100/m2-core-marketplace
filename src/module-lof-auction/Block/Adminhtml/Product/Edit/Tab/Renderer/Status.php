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
namespace Lof\Auction\Block\Adminhtml\Product\Edit\Tab\Renderer;

use Lof\Auction\Helper\Data;
use Magento\Backend\Block\Widget\Grid\Column\Renderer\AbstractRenderer;
use Magento\Framework\DataObject;

/**
 * Class Status
 * @package Lof\Auction\Block\Adminhtml\Product\Edit\Tab\Renderer
 */
class Status extends AbstractRenderer
{
    /**
     * @var Data
     */
    protected $helper;

    /**
     * @param Data $helper
     */
    public function __construct(
        Data $helper
    ) {
        $this->helper = $helper;
    }

    /**
     * get category name
     * @param  DataObject $row
     * @return string
     */
    public function render(DataObject $row)
    {
        $statusId = $row->getStatus();
        return $statusId ? __('No') : __('Yes');
    }
}
