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

namespace Lof\MarketPlace\Block\Widget;

class Sellerlist extends AbstractWidget
{
    /**
     * @var \Lof\MarketPlace\Model\Seller
     */
    protected $_sellerCollection;

    /**
     * Core registry
     *
     * @var \Magento\Framework\Registry
     */
    protected $_coreRegistry = null;

    /**
     * @var \Magento\Catalog\Helper\Category
     */
    protected $_sellerHelper;

    /**
     * @var \Magento\Cms\Model\Block
     */
    protected $_blockModel;

    /**
     * Sellerlist constructor.
     * @param \Magento\Framework\View\Element\Template\Context $context
     * @param \Magento\Framework\Registry $registry
     * @param \Lof\MarketPlace\Helper\Data $sellerHelper
     * @param \Lof\MarketPlace\Model\Seller $sellerCollection
     * @param \Magento\Cms\Model\Block $blockModel
     * @param array $data
     */
    public function __construct(
        \Magento\Framework\View\Element\Template\Context $context,
        \Magento\Framework\Registry $registry,
        \Lof\MarketPlace\Helper\Data $sellerHelper,
        \Lof\MarketPlace\Model\Seller $sellerCollection,
        \Magento\Cms\Model\Block $blockModel,
        array $data = []
    ) {
        $this->_sellerCollection = $sellerCollection;
        $this->_sellerHelper = $sellerHelper;
        $this->_coreRegistry = $registry;
        $this->_blockModel = $blockModel;
        parent::__construct($context, $sellerHelper);
    }

    /**
     * @return \Magento\Cms\Model\Block
     */
    public function getCmsBlockModel()
    {
        return $this->_blockModel;
    }

    /**
     * @return string|void
     */
    public function _toHtml()
    {
        if (!$this->_sellerHelper->getConfig('general_settings/enable')) {
            return;
        }

        $carousel_layout = $this->getConfig('carousel_layout');
        if ($carousel_layout == 'owl_carousel') {
            $this->setTemplate('widget/seller_list_owl.phtml');
        } else {
            $this->setTemplate('widget/seller_list_bootstrap.phtml');
        }
        if (($template = $this->getConfig('template')) != '') {
            $this->setTemplate($template);
        }

        return parent::_toHtml();
    }

    /**
     * @return \Magento\Framework\Data\Collection\AbstractDb|\Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection|null
     */
    public function getSellerCollection()
    {
        $number_item = $this->getConfig('number_item', 12);
        $sellerGroups = $this->getConfig('seller_groups');
        $collection = $this->_sellerCollection->getCollection()
            ->addFieldToFilter('status', 1);
        $sellerGroups = explode(',', $sellerGroups);
        if (is_array($sellerGroups)) {
            $collection->addFieldToFilter('group_id', ['in' => $sellerGroups]);
        }
        $collection->setPageSize($number_item)
            ->setCurPage(1)
            ->setOrder('position', 'ASC');
        return $collection;
    }
}
