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
namespace Lof\Auction\Block\Adminhtml\Product;

use Magento\Backend\Block\Template\Context;
use Magento\Framework\Registry;

/**
 * Class ProductSetAttribute
 * @package Lof\Auction\Block\Adminhtml\Product
 */
class ProductSetAttribute extends \Magento\Backend\Block\Template
{
    /**
     * @var Registry
     */
    private $coreRegistry;

    /**
     * @param Context   $context
     * @param Registry               $coreRegistry
     * @param array                                     $data = []
     */
    public function __construct(
        Context $context,
        Registry $coreRegistry,
        array $data = []
    ) {
        $this->coreRegistry = $coreRegistry;
        parent::__construct($context);
    }

    /**
     * Define block template
     *
     * @return void
     */
    protected function _construct()
    {
        $this->setTemplate('attribute.phtml');
        parent::_construct();
    }

    /**
     * getAuctionType
     * @return false|string
     */
    public function getAuctionType()
    {
        return $this->coreRegistry->registry('product')->getAuctionType();
    }
}
