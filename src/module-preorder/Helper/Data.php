<?php

namespace CoreMarketplace\PreOrder\Helper;

use Magento\Framework\App\Filesystem\DirectoryList;
use Magento\Framework\App\Area;
use Magento\Framework\Mail\Template\TransportBuilder;
use Magento\Framework\Translate\Inline\StateInterface;
use Magento\ConfigurableProduct\Model\Product\Type\Configurable;
use Magento\Catalog\Model\ResourceModel\Product\CollectionFactory as Products;
use Lof\PreOrder\Model\ResourceModel\Item\CollectionFactory as Items;
use Lof\PreOrder\Model\ResourceModel\PreOrder\CollectionFactory;
use Lof\MarketPlace\Helper\Data as MarketplaceHelper;

class Data extends \Lofmp\PreOrder\Helper\Data 
{
    /**
     * @var \Magento\Store\Model\StoreManagerInterface
     */
    protected $_storeManager;

    /**
     * @var \Magento\Cms\Model\Template\FilterProvider
     */
    protected $_filterProvider;

    /**
     * @var \Magento\Framework\Stdlib\DateTime\TimezoneInterface
     */
    protected $_localeDate;

    /**
     * @var \Magento\Framework\Locale\CurrencyInterface
     */
    protected $_localeCurrency;

    /**
     * @var \Magento\Framework\ObjectManagerInterface
     */
    protected $_objectManager;

    /**
     * @var StateInterface
     */
    protected $_inlineTranslation;
    /**
     * @var \Magento\Customer\Model\Session
     */
    protected $customerSession;

    /**
     * @var \Magento\Checkout\Model\Session
     */
    protected $checkoutSession;

    /**
     * @var \Magento\Framework\Registry
     */
    protected $coreRegistry;

    /**
     * @var \Magento\Framework\Filter\FilterManager
     */
    protected $filterManager;
    /**
     * @var \Magento\Catalog\Model\ProductFactory
     */
    protected $_product;
    /**
     * @var Products
     */
    protected $_productCollection;

    public $_resource;

    /**
     * @var Configurable
     */
    protected $_configurable;

    /**
     * @var \Magento\Catalog\Model\Product
     */
    protected $_catalogProduct;

    /**
     * @var \Magento\Sales\Model\OrderFactory
     */
    protected $_order;

    /**
     * @var Items
     */
    protected $_itemCollection;
    /**
     * @var CollectionFactory
     */
    protected $_preorderCollection;

    /**
     * @var \Magento\Customer\Model\CustomerFactory
     */
    protected $customer;

    /**
     * @var \Psr\Log\LoggerInterface
     */
    private $logger;

    /**
     * @var \Magento\Framework\Json\EncoderInterface
     */
    protected $jsonEncoder;

    /**
     * @var MarketplaceHelper
     */
    protected $mpHelper;

    /**
     * Data constructor.
     *
     * @param \Magento\Framework\App\Helper\Context                          $context
     * @param \Magento\Cms\Model\Template\FilterProvider                     $filterProvider
     * @param \Magento\Store\Model\StoreManagerInterface                     $storeManager
     * @param \Magento\Framework\Locale\CurrencyInterface                    $localeCurrency
     * @param \Magento\Framework\Stdlib\DateTime\TimezoneInterface           $localeDate
     * @param \Magento\Framework\ObjectManagerInterface                      $objectManager
     * @param \Magento\Customer\Model\Session                                $customerSession
     * @param \Magento\Checkout\Model\Session                                $checkoutSession
     * @param \Magento\Customer\Model\CustomerFactory                        $customer
     * @param \Magento\Framework\Registry                                    $coreRegistry
     * @param \Magento\Framework\Mail\Template\TransportBuilder              $transportBuilder
     * @param \Magento\Framework\Translate\Inline\StateInterface             $inlineTranslation
     * @param \Magento\Catalog\Model\ResourceModel\Product\CollectionFactory $productCollection
     * @param \Magento\ConfigurableProduct\Model\Product\Type\Configurable   $configurable
     * @param \Lof\PreOrder\Model\ResourceModel\Item\CollectionFactory       $itemCollection
     * @param \Lof\PreOrder\Model\ResourceModel\PreOrder\CollectionFactory   $preorderCollection
     * @param \Magento\Sales\Model\OrderFactory                              $order
     * @param \Magento\Framework\App\ResourceConnection                      $resource
     * @param \Magento\Catalog\Model\ProductFactory                          $product
     * @param \Magento\Framework\Filter\FilterManager                        $filterManager
     * @param \Psr\Log\LoggerInterface                                       $logger
     * @param \Magento\Framework\Json\EncoderInterface                       $jsonEncoder
     * @param MarketplaceHelper                                              $mpHelper
     */
    public function __construct(
        \Magento\Framework\App\Helper\Context $context,
        \Magento\Cms\Model\Template\FilterProvider $filterProvider,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        \Magento\Framework\Locale\CurrencyInterface $localeCurrency,
        \Magento\Framework\Stdlib\DateTime\TimezoneInterface $localeDate,
        \Magento\Framework\ObjectManagerInterface $objectManager,
        \Magento\Customer\Model\Session $customerSession,
        \Magento\Checkout\Model\Session $checkoutSession,
        \Magento\Customer\Model\CustomerFactory $customer,
        \Magento\Framework\Registry $coreRegistry,
        TransportBuilder $transportBuilder,
        StateInterface $inlineTranslation,
        Products $productCollection,
        Configurable $configurable,
        Items $itemCollection,
        CollectionFactory $preorderCollection,
        \Magento\Sales\Model\OrderFactory $order,
        \Magento\Framework\App\ResourceConnection $resource,
        \Magento\Catalog\Model\ProductFactory $product,
        \Magento\Framework\Filter\FilterManager $filterManager,
        \Psr\Log\LoggerInterface $logger,
        \Magento\Framework\Json\EncoderInterface $jsonEncoder,
        MarketplaceHelper $mpHelper
    ) {
        parent::__construct(
            $context,
            $filterProvider,
            $storeManager,
            $localeCurrency,
            $localeDate,
            $objectManager,
            $customerSession,
            $checkoutSession,
            $customer,
            $coreRegistry,
            $transportBuilder,
            $inlineTranslation,
            $productCollection,
            $configurable,
            $itemCollection,
            $preorderCollection,
            $order,
            $resource,
            $product,
            $filterManager,
            $logger,
            $jsonEncoder
        );
        $this->_urlBuilder         = $context->getUrlBuilder();
        $this->_filterProvider     = $filterProvider;
        $this->_storeManager       = $storeManager;
        $this->_localeDate         = $localeDate;
        $this->_localeCurrency     = $localeCurrency;
        $this->_objectManager      = $objectManager;
        $this->customerSession     = $customerSession;
        $this->checkoutSession     = $checkoutSession;
        $this->coreRegistry        = $coreRegistry;
        $this->filterManager       = $filterManager;
        $this->_configurable       = $configurable;
        $this->_productCollection  = $productCollection;
        $this->_catalogProduct     = $product->create();
        $this->_resource           = $resource;
        $this->_product            = $product;
        $this->_order              = $order;
        $this->_transportBuilder   = $transportBuilder;
        $this->_inlineTranslation  = $inlineTranslation;
        $this->customer            = $customer;
        $this->_preorderCollection = $preorderCollection;
        $this->_itemCollection     = $itemCollection;
        $this->logger              = $logger;
        $this->jsonEncoder         = $jsonEncoder;
        $this->mpHelper            = $mpHelper;
    }

    public function showPreorderOption()
    {
        if ($this->allowSellerManage()) {
            if ($seller = $this->mpHelper->getCurrentSeller()) {
                if ($group = $seller->getGroup()) {
                    if (isset($group['use_auction']) && $group['use_auction'] == 1) {
                        return true;
                    }
                }
            }
        }
        
        return false;
    }
}