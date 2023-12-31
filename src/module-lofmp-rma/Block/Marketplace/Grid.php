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

namespace Lofmp\Rma\Block\Marketplace;

class Grid extends \Magento\Framework\View\Element\Template
{
    /**
     * @var \Lofmp\Rma\Model\ResourceModel\Rma\Collection
     */
    protected $_rmaCollection;

    /**
     * @param \Magento\Customer\Model\Session $customerSession
     * @param \Lof\MarketPlace\Model\SellerFactory $sellerFactory
     * @param \Lofmp\Rma\Model\ResourceModel\Rma\Collection $RmaCollection
     * @param \Lofmp\Rma\Model\ResourceModel\Item\Collection $ItemCollection
     * @param \Magento\Framework\View\Element\Template\Context $context
     * @param \Lof\MarketPlace\Helper\Data $helper
     * @param \Lofmp\Rma\Model\Status $statusFactory
     * @param array $data
     */
    public function __construct(
        \Magento\Customer\Model\Session $customerSession,
        \Lof\MarketPlace\Model\SellerFactory $sellerFactory,
        \Lofmp\Rma\Model\ResourceModel\Rma\Collection $RmaCollection,
        \Lofmp\Rma\Model\ResourceModel\Item\Collection $ItemCollection,
        \Magento\Framework\View\Element\Template\Context $context,
        \Lof\MarketPlace\Helper\Data $helper,
        \Lofmp\Rma\Model\Status $statusFactory,
        array $data = []
    ) {
        $this->_rmaCollection = $RmaCollection;
        $this->_ItemCollection = $ItemCollection;
        $this->customerSession = $customerSession;
        $this->_sellerFactory = $sellerFactory;
        $this->helper = $helper;
        $this->status = $statusFactory;
        parent::__construct($context);
    }

    /**
     * @return array|null
     */
    public function getSellerRma()
    {
        $sellerId = $this->helper->getSellerId();
        $sellerRmaList = $this->_rmaCollection
            ->addFieldToFilter('seller_id', $sellerId)
            ->setOrder('created_at', 'DESC')
            ->getData();
        return $sellerRmaList;
    }

    /**
     * Get status name by Id
     * @param int $id
     * @return string
     */
    public function getStatusName($id)
    {
        $status = $this->status->load($id);
        return $status->getName();
    }

    /**
     * {@inheritdoc}
     */
    protected function _prepareLayout()
    {
        parent::_prepareLayout();
        $this->pageConfig->getTitle()->set(__('Manage RMAs'));
        $pageMainTitle = $this->getLayout()->getBlock('page.main.title');
        if ($pageMainTitle) {
            $pageMainTitle->setPageTitle(__('Manage RMAs'));
        }
    }
}
