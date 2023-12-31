<?php
/**
 * Landofcoder
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the landofcoder.com license that is
 * available through the world-wide-web at this URL:
 * http://landofcoder.com/license
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade this extension to newer
 * version in the future.
 *
 * @category   Landofcoder
 * @package    Lofmp_Faq
 * @copyright  Copyright (c) 2017 Landofcoder (http://www.landofcoder.com/)
 * @license    http://www.landofcoder.com/LICENSE-1.0.html
 */

namespace Lofmp\Faq\Block\Marketplace\FaqCategory;

class ListCategory extends \Magento\Framework\View\Element\Template {


    public $_categoryFactory;

    /**
     * @var \Lof\MarketPlace\Model\Data
     */

    protected $_helper;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */

    protected $_resource;

    /**
     * @param \Magento\Framework\View\Element\Template\Context
     * @param \Magento\Framework\Registry
     * @param \Lof\MarketPlace\Model\Seller
     * @param \Magento\Framework\App\ResourceConnection
     * @param array
     */
    public function __construct(
        \Magento\Framework\View\Element\Template\Context $context,
        \Lofmp\Faq\Model\CategoryFactory $categoryFactory,
        \Lof\MarketPlace\Helper\Data $helper,
        \Magento\Framework\App\ResourceConnection $resource
    ) {
        $this->_helper        = $helper;
        $this->_categoryFactory = $categoryFactory->create()
                                    ->getCollection()
                                    ->addFieldToFilter('main_table.seller_id', ['eq' => $helper->getSellerId()]);
        $this->_resource      = $resource;
        parent::__construct($context);
    }

    /**
     * Prepare layout for change buyer
     *
     * @return Object
     */
    public function _prepareLayout() {
        $this->pageConfig->getTitle ()->set(__('Manager Category'));
        return parent::_prepareLayout ();
    }
}