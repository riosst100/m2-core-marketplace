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

namespace Lof\Auction\Block\Customer;

use Lof\Auction\Model\AutoAuction;
use Lof\Auction\Model\ResourceModel\Amount\Collection;
use Lof\Auction\Model\ResourceModel\Amount\CollectionFactory;
use Lof\Auction\Model\ResourceModel\Product\Source\AuctionStatus;
use Magento\Catalog\Model\Product;
use Magento\Catalog\Model\ProductRepository;
use Magento\Customer\Model\Session;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\Pricing\Helper\Data;
use Magento\Framework\View\Element\Template;
use Magento\Framework\View\Element\Template\Context;

/**
 * Auction index block
 */
class Index extends Template
{
    /**
     * @var string
     */
    protected $_template = 'auction/index.phtml';

    /**
     * @var Collection
     */
    protected $_auctionAmtCollectionFactory;

    /**
     * @var Session
     */
    protected $_customerSession;

    /**
     * @var Collection
     */
    protected $_auctionDetails;
    /**
     * @var  AutoAuction
     */
    protected $autoAuction;

    /**
     * @var
     */
    protected $_autoAuctionDetails;

    /**
     * @var ProductRepository
     */
    protected $_productRepository;
    /**
     * @var Data
     */
    private $_priceHelper;
    /**
     * @var Product
     */
    private $_product;

    /**
     * @param Context $context
     * @param Session $customerSession
     * @param Product $product
     * @param Data $priceHelper
     * @param CollectionFactory $auctionAmtCollectionFactory
     * @param AutoAuction $autoAuction
     * @param ProductRepository $productRepository
     * @param array $data
     */
    public function __construct(
        Context $context,
        Session $customerSession,
        Product $product,
        Data $priceHelper,
        CollectionFactory $auctionAmtCollectionFactory,
        AutoAuction $autoAuction,
        ProductRepository $productRepository,
        array $data = []
    ) {
        $this->autoAuction = $autoAuction;
        $this->_priceHelper = $priceHelper;
        $this->_auctionAmtCollectionFactory = $auctionAmtCollectionFactory;
        $this->_customerSession = $customerSession;
        $this->_product = $product;
        $this->_productRepository = $productRepository;
        parent::__construct($context, $data);
    }

    /**
     * @return void
     */
    protected function _construct()
    {
        parent::_construct();
        $this->pageConfig->getTitle()->set(__('Auction Details'));
    }
    /**
     * @return bool|\Magento\Sales\Model\ResourceModel\Order\Collection
     */
    public function getAutoAuctionDetails()
    {
        if (!($customerId = $this->_customerSession->getCustomerId())) {
            return false;
        }
        if (!$this->_autoAuctionDetails) {
            $this->_autoAuctionDetails = $this->autoAuction->getCollection()->addFieldToFilter(
                'customer_id',
                $customerId
            )->setOrder(
                'entity_id',
                'desc'
            );
        }
        return $this->_autoAuctionDetails;
    }


    /**
     * @return bool|\Magento\Sales\Model\ResourceModel\Order\Collection
     */
    public function getAuctionDetails()
    {
        if (!($customerId = $this->_customerSession->getCustomerId())) {
            return false;
        }
        if (!$this->_auctionDetails) {
            $this->_auctionDetails = $this->_auctionAmtCollectionFactory->create()->addFieldToSelect(
                '*'
            )->addFieldToFilter(
                'customer_id',
                $customerId
            )->setOrder(
                'entity_id',
                'desc'
            );
        }
        return $this->_auctionDetails;
    }

    /**
     * @return $this
     */
    protected function _prepareLayout()
    {
        parent::_prepareLayout();
        if ($this->getAuctionDetails()) {
            $pager = $this->getLayout()->createBlock(
                'Magento\Theme\Block\Html\Pager',
                'sales.order.history.pager'
            )->setCollection(
                $this->getAuctionDetails()
            );
            $this->setChild('pager', $pager);
            $this->getAuctionDetails()->load();
        }
        return $this;
    }

    /**
     * @return string
     */
    public function getPagerHtml()
    {
        return $this->getChildHtml('pager');
    }

    /**
     * @param int $id
     * @return string
     */
    public function getDeleteUrl($id)
    {
        return $this->getUrl('auction/account/deletebid', ['id' => $id]);
    }

    /**
     * @param int $productId
     * @return array
     * @throws NoSuchEntityException
     */
    public function getProductDetail($productId)
    {
        $pro = $this->_productRepository->getById($productId);
        return ['name'=> $pro->getName(), 'url' => $pro->getProductUrl()];
    }


    /**
     * get Formated price
     * @param $amount float
     * @return string
     *
     */
    public function formatPrice($amount)
    {
        return $this->_priceHelper->currency($amount, true, false);
    }

    /**
     * get Winning Status Label
     * @param $auctionData
     * @param bool $isManual
     * @return string
     */
    public function winningStatus($auctionData, $isManual = true)
    {
        if ($auctionData->getStatus() == AuctionStatus::STATUS_TIME_END) {
            $winningStatus = 0;
            if ($isManual) {
                $winningStatus = $auctionData->getWinningStatus();
            } else {
                $winningStatus = $auctionData->getFlag();
            }
            $status = $winningStatus == AuctionStatus::STATUS_PROCESS ? ('<span class="bid-winner">'.__("Winner").'</span>') : "--";
        } else {
            $status = __("Pending");
        }
        return $status;
    }

    /**
     * get Auction Status Label
     * @param $status int
     * @return string
     *
     */
    public function status($status)
    {
        $label = [0 =>__('Complete'), 1 =>__('Pending')];
        return isset($label[$status]) ? $label[$status] : '--';
    }

    /**
     * @return string
     */
    public function getBackUrl()
    {
        return $this->getUrl('customer/account/');
    }
}
