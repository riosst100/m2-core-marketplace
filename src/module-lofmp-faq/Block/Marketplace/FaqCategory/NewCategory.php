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

class NewCategory extends \Magento\Framework\View\Element\Template {

    /**
     * Core registry
     *
     * @var \Magento\Framework\Registry
     */
    protected $_coreRegistry = null;

    public $_categoryFactory;
    /**
     * @var \Lof\MarketPlace\Model\Data
     */
    protected $_helper;
    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    protected $_resource;

    public $_animate;
    /**
     * @param \Magento\Framework\View\Element\Template\Context
     * @param \Magento\Framework\Registry
     * @param \Lof\MarketPlace\Model\Seller
     * @param \Magento\Framework\App\ResourceConnection
     * @param array
     */
    public function __construct(
        \Magento\Framework\View\Element\Template\Context $context,
        \Magento\Framework\Registry $registry,
        \Lofmp\Faq\Model\CategoryFactory $categoryFactory,
        \Lof\MarketPlace\Helper\Data $helper,
        \Lofmp\Faq\Model\Config\Source\AnimationType $animate,
        \Magento\Framework\App\ResourceConnection $resource,
        array $data = []
    ) {
        $this->_helper           = $helper;
        $this->_coreRegistry     = $registry;
        $this->_categoryFactory  = $categoryFactory->create()
                                    ->getCollection()
                                    ->addFieldToFilter('seller_id', ['eq' => $helper->getSellerId()]);
        $this->_resource         = $resource;
        $this->_animate          = $animate;
        parent::__construct($context);
    }

    /**
     * Prepare layout for change buyer
     *
     * @return Object
     */
    public function _prepareLayout() {
        $this->pageConfig->getTitle ()->set(__('New Category'));
        return parent::_prepareLayout ();
    }
}