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

namespace Lof\MarketPlace\Model;

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

/**
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 * @SuppressWarnings(PHPMD.TooManyFields)
 */
class Seller extends \Magento\Framework\Model\AbstractModel
{
    const STATUS_PENDING = 2;
    const STATUS_ENABLED = 1;
    const STATUS_DISABLED = 0;
    const STATUS_VERIFY = 1;
    const STATUS_UNVERIFY = 0;
    const DEFAULT_GROUP_ID = 1;

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
     * @var string
     */
    protected $_eventPrefix = 'lof_marketplace_seller';

    /**
     * Name of the event object
     *
     * @var string
     */
    protected $_eventObject = 'seller';

    /**
     * @var \Magento\Customer\Model\Customer|mixed|object|null
     */
    protected $_sellerCustomer = null;

    /**
     * Seller constructor.
     *
     * @param Context $context
     * @param Registry $registry
     * @param ResourceModel\Seller|null $resource
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
     * @param ResourceModel\Seller|null
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
        ResourceModel\Seller $resource = null,
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

        parent::__construct($context, $registry, $resource, $resourceCollection, $data);
    }

    /**
     * @inheritDoc
     */
    public function _construct()
    {
        $this->_init(\Lof\MarketPlace\Model\ResourceModel\Seller::class);
    }

    /**
     * Retrieve sellers model with sellers data
     * @param array|null $sellerData
     * @return SellerInterface
     */
    public function getDataModel($sellersData = null)
    {
        $sellersData = $sellersData && is_array($sellersData) ? $sellersData : $this->getData();
        $sellersDataObject = $this->sellersDataFactory->create();
        $this->dataObjectHelper->populateWithArray(
            $sellersDataObject,
            $sellersData,
            SellerInterface::class
        );
        return $sellersDataObject;
    }

    /**
     * Prepare page's statuses.
     * Available event cms_page_get_available_statuses to customize statuses.
     *
     * @return array
     */
    public function getAvailableStatuses()
    {
        return [
            self::STATUS_ENABLED => __('Approved'),
            self::STATUS_DISABLED => __('Disapproved'),
            self::STATUS_PENDING => __('Pending')
        ];
    }

    /**
     * @return array
     */
    public function getAvailableVerifyStatuses()
    {
        return [self::STATUS_VERIFY => __('Yes'), self::STATUS_UNVERIFY => __('No')];
    }

    /**
     * Check if page identifier exist for specific store
     * return page id if page exists
     *
     * @param string $identifier
     * @param int $storeId
     * @return int
     */
    public function checkIdentifier($identifier, $storeId)
    {
        return $this->_getResource()->checkIdentifier($identifier, $storeId);
    }

    /**
     * @return array
     */
    public function getProductCollection()
    {
        $data = [];
        if ($sellerId = $this->getSellerId()){
            $collection = $this->_productCollectionFactory->create()->addAttributeToSelect('*');
            $collection->addAttributeToFilter("seller_id", $sellerId);
            $data = [];
            foreach ($collection as $_product) {
                $data[] = $_product->getData("entity_id");
            }
        }
        return $data;
    }

    /**
     * @return int
     */
    public function getSellerId()
    {
        return $this->getData("seller_id");
    }

    /**
     * @return int
     */
    public function getGetCurrentSellerId()
    {
        $seller_id = -1;
        $customerId = $this->customer ? $this->customer->getCustomerId() : 0;
        if (!$this->getId() && $customerId) {
            $collection = $this->getCollection()
                        ->addFieldToFilter('seller_id', $customerId);
            if ($collection->count()) {
                $seller_id = $collection->getFirstItem()->getData('seller_id');
            }
            $this->setData("seller_id", $seller_id);
        } else {
            $seller_id = $this->getData("seller_id");
        }
        return $seller_id;
    }

    /**
     * Get seller Group
     * @return mixed|array
     */
    public function getSellerGroup()
    {
        if (!isset($this->_seller_group)) {
            $this->_seller_group = $this->getResource()->getSellerGroup($this);
            $this->_seller_group = is_array($this->_seller_group) ? $this->_seller_group[0] : $this->_seller_group;
        }
        return $this->_seller_group;
    }

    /**
     * @param \Magento\Customer\Model\Customer $customer
     * @return $this
     * @throws LocalizedException
     */
    public function loadByCustomer(\Magento\Customer\Model\Customer $customer)
    {
        $this->_getResource()->loadByCustomer($this, $customer);
        return $this;
    }

    /**
     * @return string
     * @throws NoSuchEntityException
     */
    public function getUrl()
    {
        $url = $this->_storeManager->getStore()->getBaseUrl();
        $helper = $this->_sellerHelper;
        $urlPrefixConfig = $helper->getConfig('general_settings/url_prefix');
        $urlPrefix = '';
        if ($urlPrefixConfig) {
            $urlPrefix = $urlPrefixConfig . '/';
        }
        $urlSuffix = $helper->getConfig('general_settings/url_suffix');
        return $url . $urlPrefix . $this->getUrlKey() . $urlSuffix;
    }

    /**
     * @return bool|string
     * @throws NoSuchEntityException
     */
    public function getImageUrl()
    {
        $url = "";
        $image = $this->getImage();
        if ($image) {
            if ($this->isHttpUrl($image)) {
                $url = $image;
            } else {
                $url = $this->_storeManager->getStore()->getBaseUrl(\Magento\Framework\UrlInterface::URL_TYPE_MEDIA)
                . $image;
            }
        }
        return $url;
    }

    /**
     * Retrive thumbnail URL
     *
     * @return string
     */
    public function getThumbnailUrl()
    {
        $thumbnail = $this->getThumbnail();
        $url = "";
        if ($thumbnail) {
            if ($this->isHttpUrl($thumbnail)) {
                $url = $thumbnail;
            } else {
                $url = $this->_storeManager->getStore()->getBaseUrl(\Magento\Framework\UrlInterface::URL_TYPE_MEDIA)
                . $thumbnail;
            }
        } else {
            $url = $this->_storeManager->getStore()->getBaseUrl(\Magento\Framework\UrlInterface::URL_TYPE_MEDIA)
                . 'images/user.png';
        }
        return $url;
    }

    /**
     * {@inheritdoc}
     */
    public function getPublicProfile($sellerId, $message = "")
    {
        $seller = $this->load($sellerId);
        return $seller ? $seller->getDataModel() : null;
    }

    /**
     * @return \Magento\Customer\Model\Customer|mixed|object|null
     */
    public function getSellerCustomer()
    {
        $customerId = $this->getCustomerId();
        if ($customerId && !$this->_sellerCustomer) {
            $this->_sellerCustomer = $this->customerFactory->create()->load((int)$customerId);
        }
        return $this->_sellerCustomer;
    }

    /**
     * get seller tax vat
     *
     * @return string
     */
    public function getTaxvat()
    {
        $taxvat = $this->getData("taxvat");
        if (!$taxvat) {
            $customer = $this->getSellerCustomer();
            $taxvat = $customer ? $customer->getTaxvat() : "";
            $this->setData("taxvat", $taxvat);
        }
        return $taxvat;
    }

    /**
     * {@inheritdoc}
     */
    public function get($sellerId)
    {
        $seller = $this->load($sellerId);
        if (!$seller->getId()) {
            throw new NoSuchEntityException(__('Seller with id "%1" does not exist.', $sellerId));
        }
        return $seller->getDataModel();
    }

    /**
     * {@inheritdoc}
     */
    public function getList(
        \Magento\Framework\Api\SearchCriteriaInterface $criteria
    ) {
        $collection = $this->getCollection();

        $this->extensionAttributesJoinProcessor->process(
            $collection,
            SellerInterface::class
        );

        $this->collectionProcessor->process($criteria, $collection);

        $searchResults = $this->searchResultsFactory->create();
        $searchResults->setSearchCriteria($criteria);

        $items = [];
        foreach ($collection as $model) {
            $items[] = $model->getDataModel();
        }

        $searchResults->setItems($items);
        $searchResults->setTotalCount($collection->getSize());
        return $searchResults;
    }

    /**
     * Is http Url
     *
     * @param string $str
     * @return bool
     */
    protected function isHttpUrl($str)
    {
        if(!empty ($str) && ($urls = parse_url($str))) {
            return isset($urls['host']) && !empty($urls['host']) ? true : false;
        }
        return false;
    }
}
