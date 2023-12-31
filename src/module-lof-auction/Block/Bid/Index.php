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

namespace Lof\Auction\Block\Bid;

use Exception;
use Lof\Auction\Helper\Data;
use Lof\Auction\Model\AmountFactory;
use Lof\Auction\Model\AutoAuctionFactory;
use Lof\Auction\Model\Product;
use Lof\Auction\Model\ProductFactory;
use Lof\Auction\Model\ResourceModel\MixAmount\Collection;
use Lof\Auction\Model\ResourceModel\MixAmount\CollectionFactory as MixAmountCollectionFactory;
use Lof\Auction\Model\ResourceModel\Product\Source\AuctionStatus;
use Magento\Catalog\Block\Product\Context;
use Magento\Customer\Model\Session as CustomerSession;
use Magento\Framework\Data\Collection\AbstractDb;
use Magento\Framework\DataObject;
use Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection;
use Magento\Framework\Pricing\PriceCurrencyInterface;
use Magento\Framework\Registry;
use Magento\Framework\Stdlib\DateTime\TimezoneInterface;

/**
 * Class Index
 * @package Lof\Auction\Block\Bid
 */
class Index extends \Magento\Framework\View\Element\Template
{
    /**
     * @var string
     */
    protected $_template = 'Lof_Auction::auction/top_link.phtml';

    /**
     * @var Data
     */
    protected $helper;
    /**
     * @var Product
     **/
    protected $auctionProduct;
    /**
     * @var CustomerSession
     */
    private $_customerSession;
    /**
     * @var Registry
     */
    private $_coreRegistry;
    /**
     * @var AmountFactory
     */
    private $_aucAmountFactory;
    /**
     * @var AutoAuctionFactory
     */
    private $_autoAuctionFactory;
    /**
     * @var PriceCurrencyInterface
     */
    private $_priceHelper;
    /**
     * @var ProductFactory
     */
    private $_auctionProFactory;
    /**
     * @var MixAmountCollectionFactory
     */
    private $_mixAmountCollectionFactory;
    /**
     * @var TimezoneInterface
     */
    private $timezone;

    protected $_urlInterface;


    /**
     * Index constructor.
     * @param Context $context
     * @param Data $helper
     * @param CustomerSession $customerSession
     * @param PriceCurrencyInterface $priceHelper
     * @param Product $auctionProduct
     * @param AmountFactory $aucAmountFactory
     * @param AutoAuctionFactory $autoAuctionFactory
     * @param ProductFactory $auctionProFactory
     * @param MixAmountCollectionFactory $mixAmountCollectionFactory
     * @param TimezoneInterface $timezone
     * @param \Magento\Framework\UrlInterface $urlInterface
     * @param array $data
     */
    public function __construct(
        Context $context,
        Data $helper,
        CustomerSession $customerSession,
        PriceCurrencyInterface $priceHelper,
        Product $auctionProduct,
        AmountFactory $aucAmountFactory,
        AutoAuctionFactory $autoAuctionFactory,
        ProductFactory $auctionProFactory,
        MixAmountCollectionFactory $mixAmountCollectionFactory,
        TimezoneInterface $timezone,
        \Magento\Framework\UrlInterface $urlInterface,
        array $data = []
    ) {
        $this->_customerSession = $customerSession;
        $this->_coreRegistry = $context->getRegistry();
        $this->_auctionProFactory = $auctionProFactory;
        $this->_aucAmountFactory = $aucAmountFactory;
        $this->auctionProduct = $auctionProduct;
        $this->helper = $helper;
        $this->_autoAuctionFactory = $autoAuctionFactory;
        $this->_priceHelper = $priceHelper;
        $this->_mixAmountCollectionFactory = $mixAmountCollectionFactory;
        $this->timezone = $timezone;
        $this->_urlInterface = $urlInterface;
        parent::__construct($context, $data);
    }

    public function getCurrentUrl() {
        return $this->_urlInterface->getCurrentUrl();
    }

    /**
     * For get Bidding record count
     * @param int $auctionId
     * @return string
     */
    public function getNumberOfBid($auctionId)
    {
        $records = $this->_mixAmountCollectionFactory->create()
            ->addFieldToFilter('auction_id', ['eq' => $auctionId]);
        if (count($records) < 2) {
            return count($records) . __(' Bid');
        } else {
            return count($records) . __(' Bids');
        }
    }

    /**
     * @param $auction_id
     * @return Collection
     */
    public function getHistory($auction_id)
    {
        return $this->_mixAmountCollectionFactory->create()
            ->addFieldToFilter('auction_id', ['eq' => $auction_id])
            ->addFieldToFilter('is_spam', ['eq' => 0])
            ->setOrder('amount', 'DESC');
    }


    /**
     * @param $customerId
     * @param $product_id
     * @return DataObject
     */
    public function getHistoryAuto($customerId, $product_id)
    {
        return $this->_autoAuctionFactory->create()->getCollection()
            ->addFieldToFilter('product_id', ['eq' => $product_id])
            ->addFieldToFilter('customer_id', $customerId)
            ->addFieldToFilter('status', ['eq' => 1])
            ->getFirstItem();
    }

    /**
     * @return AbstractDb|AbstractCollection|null
     * @throws Exception
     */
    public function getAuction()
    {
        $todayUtc = $this->helper->getCurrentTimeUtc();
        //$todayUtc = $this->helper->getTimezoneDateTime();
        return $this->auctionProduct->getCollection()
            ->addFieldToFilter('auction_status', AuctionStatus::STATUS_PROCESS)
            ->addFieldToFilter('start_auction_time', ['lteq' => $todayUtc])
            ->addFieldToFilter('stop_auction_time', ['gteq' => $todayUtc]);
    }

    /**
     * @return AbstractDb|AbstractCollection|null
     * @throws Exception
     */
    public function getStartingSoonAuction()
    {
        $todayUtc = $this->helper->getCurrentTimeUtc();
        //$todayUtc = $this->helper->getTimezoneDateTime();
        return $this->auctionProduct->getCollection()
            ->addFieldToFilter('auction_status', AuctionStatus::STATUS_PROCESS)
            ->addFieldToFilter('start_auction_time', ['gteq' => $todayUtc])
            ->addFieldToFilter('stop_auction_time', ['gteq' => $todayUtc]);
    }

    /**
     * @return AbstractDb|AbstractCollection
     * @throws Exception
     */
    public function getAuctionFeatured()
    {
        return $this->getAuction()->addFieldToFilter('featured_auction', 1);
    }

    /**
     * @return array of Auction Configuration Settings
     */
    public function getAuctionConfiguration()
    {
        return $this->helper->getAuctionConfiguration();
    }

    /**
     * @param $auctionId
     * @return string url of auction form
     */
    public function getAuctionFormAction($auctionId)
    {
        $this->getUrl();
        return $this->_urlBuilder->getUrl('lofauction/account/login/', ['id' => $auctionId]);
    }

    /**
     * {@inheritdoc}
     * @SuppressWarnings(PHPMD.NPathComplexity)
     */
    public function getLofProductPriceHtml(
        \Magento\Catalog\Model\Product $product,
        $priceType = null,
        $renderZone = \Magento\Framework\Pricing\Render::ZONE_ITEM_LIST,
        array $arguments = []
    ) {
        if (!isset($arguments['zone'])) {
            $arguments['zone'] = $renderZone;
        }
        $arguments['price_id'] = isset($arguments['price_id'])
            ? $arguments['price_id']
            : 'old-price-' . $product->getId() . '-' . $priceType;
        $arguments['include_container'] = isset($arguments['include_container'])
            ? $arguments['include_container']
            : true;
        $arguments['display_minimal_price'] = isset($arguments['display_minimal_price'])
            ? $arguments['display_minimal_price']
            : true;

        /** @var \Magento\Framework\Pricing\Render $priceRender */
        $priceRender = $this->getLayout()->getBlock('product.price.render.default');
        $price = '';
        if ($priceRender) {
            $price = $priceRender->render(
                \Magento\Catalog\Pricing\Price\FinalPrice::PRICE_CODE,
                $product,
                $arguments
            );
        }
        
        return $price;
    }


    /**
     * Prepare breadcrumbs
     *
     * @throws \Magento\Framework\Exception\LocalizedException
     * @return void
     */
    protected function _addBreadcrumbs()
    {
        $breadcrumbsBlock = $this->getLayout()->getBlock('breadcrumbs');
        $baseUrl = $this->_storeManager->getStore()->getBaseUrl();
        $auctionRoute = $this->helper->getConfig('general_settings/route');
        $auctionRoute = $auctionRoute?$auctionRoute:"lofauction/bid/index";
        $page_title = $this->helper->getConfig("auction_page/meta_title");
        $page_title = $page_title?$page_title:__("Auctions");
        if($breadcrumbsBlock)
        {
            $breadcrumbsBlock->addCrumb(
                'home',
                [
                    'label' => __('Home'),
                    'title' => __('Go to Home Page'),
                    'link' => $baseUrl
                ]
            );
            $breadcrumbsBlock->addCrumb(
                'auction_index',
                [
                    'label' => $page_title,
                    'title' => $page_title,
                    'link' => ''
                ]
            );
        }
    }

    /**
     * @return $this|All
     * @throws LocalizedException
     */
    protected function _prepareLayout()
    {
        $page_title = $this->helper->getConfig("auction_page/meta_title");
        $page_title = $page_title?$page_title:__("Auctions");
        $meta_description = $this->helper->getConfig("auction_page/meta_description");
        $meta_description = $meta_description?$meta_description:$page_title;
        $meta_keywords = $this->helper->getConfig("auction_page/meta_keywords");
        $meta_keywords = $meta_keywords?$meta_keywords:$page_title;
        $this->_addBreadcrumbs();
        if($page_title){
            $this->pageConfig->getTitle()->set($page_title);   
        }
        if($meta_keywords){
            $this->pageConfig->setKeywords($meta_keywords);   
        }
        if($meta_description){
            $this->pageConfig->setDescription($meta_description);   
        }
        return parent::_prepareLayout();
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
}
