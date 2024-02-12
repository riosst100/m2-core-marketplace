<?php

namespace CoreMarketplace\MarketPlace\Model;

use Lof\MarketPlace\Api\Data\SellerInterface;
use Lof\MarketPlace\Api\Data\SellerInterfaceFactory;
use Lof\MarketPlace\Api\Data\SellersSearchResultsInterfaceFactory;
use Lof\MarketPlace\Helper\Data;
use Lof\MarketPlace\Model\ResourceModel\Seller\Collection;
use Lof\MarketPlace\Helper\WebsiteStore;
use Magento\Authorization\Model\CompositeUserContext;
use Magento\Catalog\Model\ResourceModel\Product\CollectionFactory;
use Magento\Customer\Api\AccountManagementInterface;
use Magento\Customer\Model\AddressFactory;
use Magento\Customer\Model\CustomerFactory;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\Model\Context;
use Magento\Framework\Registry;
use Magento\Store\Model\StoreManagerInterface;
use Magento\Framework\Api\DataObjectHelper;
use Magento\Framework\Api\ExtensibleDataObjectConverter;
use Magento\Framework\Api\ExtensionAttribute\JoinProcessorInterface;
use Magento\Framework\Api\SearchCriteria\CollectionProcessorInterface;
use Magento\Framework\Reflection\DataObjectProcessor;
use Lof\MarketPlace\Model\SenderFactory;
use Lof\MarketPlace\Model\ResourceModel\Seller as SellerResourceModel;
use Lof\MarketPlace\Model\Seller as LofSeller;
use Lof\MarketPlace\Model\SellerFactory;

class Seller extends LofSeller
{
    /**
     * @var DataObjectProcessor
     */
    protected $dataObjectProcessor;

    /**
     * @var CollectionFactory
     */
    protected $_productCollectionFactory;

    /**
     * @var StoreManagerInterface
     */
    protected $_storeManager;

    /**
     * URL Model instance
     *
     * @var \Magento\Framework\UrlInterface
     */
    protected $_url;

    /**
     * @var Data
     */
    protected $_sellerHelper;

    /**
     * @var \Magento\Customer\Model\Session
     */
    protected $customer;

    /**
     * @var SellerInterfaceFactory
     */
    protected $datasellerFactory;

    /**
     * @var CustomerFactory
     */
    protected $customerFactory;

    /**
     * @var AddressFactory
     */
    protected $addressFactory;

    /**
     * @var CompositeUserContext
     */
    protected $userContext;

    /**
     * @var SellerFactory
     */
    protected $sellerFactory;

    /**
     * @var Sender
     */
    protected $sender;

    /**
     * @var \Lof\MarketPlace\Helper\Seller
     */
    protected $heplerSeller;

    /**
     * @var AccountManagementInterface
     */
    protected $accountManagement;

    /**
     * @var Sender|SenderFactory
     */
    private $senderFactory;

    /**
     * @var DataObjectHelper
     */
    protected $dataObjectHelper;

    /**
     * @var SellerInterfaceFactory
     */
    protected $sellersDataFactory;

    /**
     * @var JoinProcessorInterface
     */
    protected $extensionAttributesJoinProcessor;

    /**
     * @var ExtensibleDataObjectConverter
     */
    protected $extensibleDataObjectConverter;

    /**
     * @var CollectionProcessorInterface
     */
    private $collectionProcessor;

    /**
     * @var SellersSearchResultsInterfaceFactory
     */
    protected $searchResultsFactory;

    /**
     * @var mixed|array|Object|null
     */
    protected $_seller_group;

    /**
     * @var WebsiteStore
     */
    protected $websiteStoreHelper;

    /**
     * @var \Magento\Customer\Model\Customer|mixed|object|null
     */
    protected $_sellerCustomer = null;

    /**
     * Seller constructor.
     *
     * @param Context $context
     * @param Registry $registry
     * @param SellerResourceModel|null $resource
     * @param Collection|null $resourceCollection
     * @param SellerFactory $sellerFactory
     * @param CollectionFactory $productCollectionFactory
     * @param StoreManagerInterface $storeManager
     * @param \Magento\Framework\UrlInterface $url
     * @param Data $sellerHelper
     * @param \Magento\Customer\Model\Session $customer
     * @param SellerInterfaceFactory $datasellerFactory
     * @param SenderFactory $sender
     * @param CustomerFactory $customerFactory
     * @param AddressFactory $addressFactory
     * @param AccountManagementInterface $accountManagement
     * @param CompositeUserContext $userContext
     * @param \Lof\MarketPlace\Helper\Seller $heplerSeller
     * @param DataObjectHelper $dataObjectHelper
     * @param JoinProcessorInterface $extensionAttributesJoinProcessor
     * @param ExtensibleDataObjectConverter $extensibleDataObjectConverter
     * @param CollectionProcessorInterface $collectionProcessor
     * @param SellersSearchResultsInterfaceFactory $searchResultsFactory
     * @param WebsiteStore $websiteStoreHelper
     * @param DataObjectProcessor $dataObjectProcessor
     * @param SellerResourceModel|null
     * @param Collection|null
     * @SuppressWarnings(PHPMD.ExcessiveParameterList)
     */
    public function __construct(
        Context $context,
        Registry $registry,
        SellerFactory $sellerFactory,
        CollectionFactory $productCollectionFactory,
        StoreManagerInterface $storeManager,
        \Magento\Framework\UrlInterface $url,
        Data $sellerHelper,
        \Magento\Customer\Model\Session $customer,
        SellerInterfaceFactory $datasellerFactory,
        SenderFactory $sender,
        CustomerFactory $customerFactory,
        AddressFactory $addressFactory,
        AccountManagementInterface $accountManagement,
        CompositeUserContext $userContext,
        \Lof\MarketPlace\Helper\Seller $heplerSeller,
        DataObjectHelper $dataObjectHelper,
        JoinProcessorInterface $extensionAttributesJoinProcessor,
        ExtensibleDataObjectConverter $extensibleDataObjectConverter,
        CollectionProcessorInterface $collectionProcessor,
        SellersSearchResultsInterfaceFactory $searchResultsFactory,
        WebsiteStore $websiteStoreHelper,
        DataObjectProcessor $dataObjectProcessor,
        SellerResourceModel $resource = null,
        Collection $resourceCollection = null,
        array $data = []
    ) {
        $this->_storeManager = $storeManager;
        $this->_url = $url;
        $this->_productCollectionFactory = $productCollectionFactory;
        $this->_sellerHelper = $sellerHelper;
        $this->customer = $customer;
        $this->customerFactory = $customerFactory;
        $this->addressFactory = $addressFactory;
        $this->datasellerFactory = $datasellerFactory;
        $this->sellersDataFactory = $datasellerFactory;
        $this->senderFactory = $sender;
        $this->userContext = $userContext;
        $this->sellerFactory = $sellerFactory;
        $this->heplerSeller = $heplerSeller;
        $this->accountManagement = $accountManagement;
        $this->dataObjectHelper = $dataObjectHelper;
        $this->extensionAttributesJoinProcessor = $extensionAttributesJoinProcessor;
        $this->extensibleDataObjectConverter = $extensibleDataObjectConverter;
        $this->collectionProcessor = $collectionProcessor;
        $this->searchResultsFactory = $searchResultsFactory;
        $this->websiteStoreHelper = $websiteStoreHelper;
        $this->dataObjectProcessor = $dataObjectProcessor;

        parent::__construct(
            $context,
            $registry,
            $sellerFactory,
            $productCollectionFactory,
            $storeManager,
            $url,
            $sellerHelper,
            $customer,
            $datasellerFactory,
            $sender,
            $customerFactory,
            $addressFactory,
            $accountManagement,
            $userContext,
            $heplerSeller,
            $dataObjectHelper,
            $extensionAttributesJoinProcessor,
            $extensibleDataObjectConverter,
            $collectionProcessor,
            $searchResultsFactory,
            $websiteStoreHelper,
            $dataObjectProcessor,
            $resource,
            $resourceCollection,
            $data
        );
    }

    /**
     * @return string
     * @throws NoSuchEntityException
     */
    public function getUrl()
    {
        $pwaUrl = $this->_sellerHelper->getPwaBaseUrl();

        $helper = $this->_sellerHelper;
        $urlPrefixConfig = $helper->getConfig('general_settings/url_prefix');
        $urlPrefix = '';
        if ($urlPrefixConfig) {
            $urlPrefix = $urlPrefixConfig . '/';
        }
        $urlSuffix = $helper->getConfig('general_settings/url_suffix');
        return $pwaUrl . $urlPrefix . $this->getUrlKey();
    }
}
