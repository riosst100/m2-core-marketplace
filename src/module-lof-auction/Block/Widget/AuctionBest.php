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
namespace Lof\Auction\Block\Widget;

use Lof\Auction\Helper\Data;
use Lof\Auction\Model\AmountFactory;
use Lof\Auction\Model\AutoAuctionFactory;
use Lof\Auction\Model\ProductFactory;
use Magento\Catalog\Model\Product;
use Magento\Cms\Model\Block;
use Magento\Customer\Model\Session as CustomerSession;
use Magento\Framework\Data\Collection\AbstractDb;
use Magento\Framework\DataObject;
use Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection;
use Magento\Framework\Pricing\PriceCurrencyInterface;
use Magento\Framework\Registry;
use Magento\Framework\View\Element\Template\Context;

/**
 * Class AuctionBest
 * @package Lof\Auction\Block\Widget
 */
class AuctionBest extends AbstractWidget
{

    /**
     * Core registry
     *
     * @var Registry
     */
    protected $_coreRegistry = null;

    /**
     * @var Block
     */
    protected $_blockModel;

    /**
     * @var Product
     */
    protected $_product;

    /**
     * @var CustomerSession
     */
    protected $_customerSession;

    /**
     * @var ProductFactory
     */
    protected $_auctionProFactory;
    /**
     * @var \Lof\Auction\Model\Product
     */
    private $auctionProduct;
    /**
     * @var PriceCurrencyInterface
     */
    private $_priceHelper;
    /**
     * @var Data
     */
    private $helper;
    /**
     * @var AmountFactory
     */
    private $_aucAmountFactory;
    /**
     * @var AutoAuctionFactory
     */
    private $_autoAuctionFactory;

    protected $_urlInterface;

    /**
     * @param Context $context
     * @param Registry $registry
     * @param Block $blockModel
     * @param \Lof\Auction\Model\Product $auctionProduct
     * @param PriceCurrencyInterface $priceHelper
     * @param Product $product
     * @param CustomerSession $customerSession
     * @param ProductFactory $auctionProFactory
     * @param Data $helper
     * @param AmountFactory $aucAmountFactory
     * @param AutoAuctionFactory $autoAuctionFactory
     * @param \Magento\Framework\UrlInterface $urlInterface
     * @param array $data
     */
    public function __construct(
        Context $context,
        Registry $registry,
        Block $blockModel,
        \Lof\Auction\Model\Product $auctionProduct,
        PriceCurrencyInterface $priceHelper,
        Product $product,
        CustomerSession $customerSession,
        ProductFactory $auctionProFactory,
        Data $helper,
        AmountFactory $aucAmountFactory,
        AutoAuctionFactory $autoAuctionFactory,
        \Magento\Framework\UrlInterface $urlInterface,
        array $data = []
    ) {
        $this->auctionProduct = $auctionProduct;
        $this->_coreRegistry = $registry;
        $this->_blockModel = $blockModel;
        $this->_priceHelper = $priceHelper;
        $this->_product = $product;
        $this->_customerSession = $customerSession;
        $this->_auctionProFactory = $auctionProFactory;
        $this->helper = $helper;
        $this->_aucAmountFactory = $aucAmountFactory;
        $this->_autoAuctionFactory = $autoAuctionFactory;
        $this->_urlInterface = $urlInterface;
        parent::__construct($context, $data);
    }

    public function getCurrentUrl() {
        return $this->_urlInterface->getCurrentUrl();
    }

    public function getToday(){
        return $this->helper->getTimezoneDateTime();
    }

    /**
     *
     * @param int $currentProId
     * @return AbstractDb|AbstractCollection|null of current category product in auction and buy it now
     */
    public function getHistory($currentProId = 0)
    {
        if (!$currentProId) {
            $curPro = $this->_coreRegistry->registry('current_product');
            if ($curPro) {
                $currentProId = $curPro->getEntityId();
            } else {
                $auctionId = $this->getRequest()->getParam('id');
                $currentProId = $this->getAuctionProId($auctionId);
                $curPro = $this->_product->load($currentProId);
            }
        }
        $aucAmtData = $this->_aucAmountFactory->create()->getCollection()
            ->addFieldToFilter('product_id', ['eq' => $currentProId])
            ->addFieldToFilter('status', ['eq'=>1])
            ->setOrder('auction_amount', 'DESC');
        return  $aucAmtData;
    }


    /**
     * @param $customerId
     * @param int $currentProId
     * @return DataObject
     */
    public function getHistoryAuto($customerId, $currentProId = 0)
    {
        if (!$currentProId) {
            $curPro = $this->_coreRegistry->registry('current_product');
            if ($curPro) {
                $currentProId = $curPro->getEntityId();
            } else {
                $auctionId = $this->getRequest()->getParam('id');
                $currentProId = $this->getAuctionProId($auctionId);
                $curPro = $this->_product->load($currentProId);
            }
        }
        return $this->_autoAuctionFactory->create()->getCollection()
            ->addFieldToFilter('product_id', ['eq' => $currentProId])
            ->addFieldToFilter('customer_id', $customerId)
            ->addFieldToFilter('status', ['eq'=>1])
            ->getFirstItem();
    }

    /**
     * @return AbstractDb|AbstractCollection|null
     */
    public function getAuction()
    {
        if (!isset($this->_auctionCollection)) {
            $limit = $this->getData("limit");
            $limit = $limit?(int)$limit:4;
            $featured = $this->getData("featured");
            $support_automatic = $this->getData("support_automatic");
            $stop_auction_date = $this->getData("stop_auction_date");
            $auction_status = $this->getData("auction_status");

            $collection = $this->auctionProduct->getCollection();
            if ($featured) {
                $featuredFilters = explode(",", $featured);
                if ($featuredFilters) {
                    $collection->addFieldToFilter("featured_auction", ["in" => $featuredFilters]);
                }
            }
            if ($support_automatic) {
                $supportAutomaticFilters = explode(",", $support_automatic);
                if ($supportAutomaticFilters) {
                    $collection->addFieldToFilter("auto_auction_opt", ["in" => $supportAutomaticFilters]);
                }
            }
            if ($stop_auction_date) {
                $dateFilter = $this->helper->getTimezoneDateTime($stop_auction_date);
                if ($dateFilter) {
                    $collection->addFieldToFilter("stop_auction_time", ["lteq" => $dateFilter]);
                }
            }
            if ($auction_status) {
                $auctionStatusFilters = explode(",", $auction_status);
                if ($auctionStatusFilters) {
                    $collection->addFieldToFilter("auction_status", ["in" => $auctionStatusFilters]);
                }
            }
            $collection->setOrder("created_at", "DESC")
                        ->setPageSize($limit)
                        ->setCurPage(1);
            $this->_auctionCollection = $collection;
        }
        return $this->_auctionCollection;
    }
    /**
     * @return Block
     */
    public function getCmsBlockModel()
    {
        return $this->_blockModel;
    }

    /**
     * @return string
     */
    public function _toHtml()
    {
        $custom_template = $this->getData("custom_template");
        if ($custom_template) {
            $this->setTemplate($custom_template);
        } else {
            $this->setTemplate('Lof_Auction::widget/auction.phtml');
        }
        return parent::_toHtml();
    }
    /**
     * get currency in format
     * @param $amount float
     * @return string
     *
     */
    public function formatPrice($amount)
    {
        return $this->_priceHelper->convertAndFormat($amount);
    }

    /**
     * @param int|null $auctionId
     * @return string url of auction form
     */

    public function getAuctionFormAction($auctionId = null)
    {
        return $this->_customerSession->isLoggedIn() ?
                        $this->_urlBuilder->getUrl("lofauction/account/login"):
                        $this->_urlBuilder->getUrl('lofauction/account/login/', ['id'=>$auctionId]);
    }

    /**
     * get auction product id
     * @param $auctionId int
     * @return int
     *
     */
    public function getAuctionProId($auctionId)
    {
        return  $this->_auctionProFactory->create()->load($auctionId)->getProductId();
    }

    /**
     * @return array of Auction Configuration Settings
     */
    public function getAuctionConfiguration()
    {
        return $this->helper->getAuctionConfiguration();
    }

    /**
     * @var int $currentProId
     * @return array of current category product in auction and buy it now
     */
    public function getAuctionDetail($currentProId)
    {
        $auctionConfig = $this->getAuctionConfiguration();
        $auctionData = false;
        $curPro = $this->helper->getProductById($currentProId);
        if ($auctionConfig['enable']) {
            $auctionData = $this->_auctionProFactory->create()->getCollection()
                                    ->addFieldToFilter('product_id', ['eq'=>$currentProId])
                                    ->setOrder('created_at', 'DESC')
                                    ->setOrder('auction_status', 'ASC')
                                    ->getFirstItem()->getData();

            if (isset($auctionData['entity_id'])) {
                $aucAmtData = $this->_aucAmountFactory->create()->getCollection()
                                        ->addFieldToFilter('product_id', ['eq' => $currentProId])
                                        ->addFieldToFilter('auction_id', ['eq'=> $auctionData['entity_id']])
                                        ->addFieldToFilter('winning_status', ['eq'=>1])
                                        ->addFieldToFilter('shop', ['neq'=>0])->getFirstItem();

                if ($aucAmtData->getEntityId()) {
                    $aucAmtData = $this->_autoAuctionFactory->create()->getCollection()
                                            ->addFieldToFilter('auction_id', ['eq'=> $auctionData['entity_id']])
                                            ->addFieldToFilter('flag', ['eq'=>1])
                                            ->addFieldToFilter('shop', ['neq'=>0])->getFirstItem();
                }
                $today = $this->helper->getTimezoneDateTime();
                $auctionData['time_zone'] = $this->helper->getTimezoneName();
                $auctionData['stop_auction_utc_time'] = $auctionData['stop_auction_time'];
                $auctionData['start_auction_utc_time'] = $auctionData['start_auction_time'];
                //$today = $this->_localeDate->date()->format('m/d/y H:i:s');
                $auctionData['stop_auction_time'] = $this->helper->getTimezoneDateTime($auctionData['stop_auction_time']);
                $auctionData['start_auction_time'] = $this->helper->getTimezoneDateTime($auctionData['start_auction_time']);
                $auctionData['today_time'] = $today;
                $auctionData['current_time_stamp'] = strtotime($today);
                $auctionData['start_auction_time_stamp'] = strtotime($auctionData['start_auction_time']);
                $auctionData['stop_auction_time_stamp'] = strtotime($auctionData['stop_auction_time']);
                $auctionData['new_auction_start'] = $aucAmtData->getEntityId() ? true : false;
                $auctionData['auction_title'] = __('Bid on ').$curPro->getName();
                // $auctionData['pro_url'] = $curPro->getProductUrl();
                $auctionData['max_bids'] = isset($auctionData['max_bids']) ? (int)$auctionData['max_bids'] : 0;
                $auctionData['pro_url'] = $this->getCurrentUrl();// $this->getUrl().$curPro->getUrlKey().'.html';
                $auctionData['pro_name'] = $curPro->getName();
                $auctionData['pro_buy_it_now'] = $auctionData['buy_it_now'];
                $auctionData['pro_auction'] = $auctionData['auction_status'];
                $auctionData['buy_it_now'] = $auctionData['buy_it_now'];
                if ($auctionData['min_amount'] < $auctionData['starting_price']) {
                    $auctionData['min_amount'] = $auctionData['starting_price'];
                }
            } else {
                $auctionData = false;
            }
        }

        $this->_customerSession->setAuctionData($auctionData);
        return $auctionData;
    }

    /**
     * For get Bidding record count
     * @param int $auctionId
     * @return string
     */
    public function getNumberOfBid($auctionId)
    {
        $records = $this->_aucAmountFactory->create()->getCollection()
                                    ->addFieldToFilter('auction_id', ['eq'=>$auctionId]);
        if (count($records) < 2) {
            return count($records).__(' Bid');
        } else {
            return count($records).__(' Bids');
        }
    }

    /**
     * For get Bidding history link
     * @param array $auctionDetail
     * @return string url
     */
    public function getHistoryUrl($auctionData)
    {
        return $this->_urlBuilder->getUrl('lofauction/account/history', ['id'=>$auctionData['entity_id']]);
    }
}
