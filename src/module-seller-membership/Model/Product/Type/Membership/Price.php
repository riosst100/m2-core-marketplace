<?php

namespace CoreMarketplace\SellerMembership\Model\Product\Type\Membership;

use Magento\Catalog\Model\Product;
use Magento\Customer\Api\GroupManagementInterface;
use Magento\Framework\Pricing\PriceCurrencyInterface;
use Magento\Store\Model\Store;
use Magento\Catalog\Api\Data\ProductTierPriceExtensionFactory;
use Magento\Framework\App\ObjectManager;
use Magento\Store\Api\Data\WebsiteInterface;

class Price extends \Lofmp\SellerMembership\Model\Product\Type\Membership\Price
{
    /**
     * Core event manager proxy
     *
     * @var \Magento\Framework\Event\ManagerInterface
     */
    protected $_eventManager;

    /**
     * @var \Magento\Customer\Model\Session
     */
    protected $_customerSession;

    /**
     * @var \Magento\Framework\Stdlib\DateTime\TimezoneInterface
     */
    protected $_localeDate;

    /**
     * @var \Magento\Store\Model\StoreManagerInterface
     */
    protected $_storeManager;

    /**
     * @var \Magento\CatalogRule\Model\ResourceModel\RuleFactory
     */
    protected $_ruleFactory;

    /**
     * @var PriceCurrencyInterface
     */
    protected $priceCurrency;

    /**
     * @var GroupManagementInterface
     */
    protected $_groupManagement;

    /**
     * @var \Magento\Catalog\Api\Data\ProductTierPriceInterfaceFactory
     */
    protected $tierPriceFactory;

    /**
     * @var \Magento\Framework\App\Config\ScopeConfigInterface
     */
    protected $config;

    /**
     * @var ProductTierPriceExtensionFactory
     */
    private $tierPriceExtensionFactory;

    /**
     * @var \Lofmp\SellerMembership\Helper\Data
     */
    protected $membershipHelper;

    /**
     * Constructor
     *
     * @param \Magento\CatalogRule\Model\ResourceModel\RuleFactory $ruleFactory
     * @param \Magento\Store\Model\StoreManagerInterface $storeManager
     * @param \Magento\Framework\Stdlib\DateTime\TimezoneInterface $localeDate
     * @param \Magento\Customer\Model\Session $customerSession
     * @param \Magento\Framework\Event\ManagerInterface $eventManager
     * @param PriceCurrencyInterface $priceCurrency
     * @param GroupManagementInterface $groupManagement
     * @param \Magento\Catalog\Api\Data\ProductTierPriceInterfaceFactory $tierPriceFactory
     * @param \Magento\Framework\App\Config\ScopeConfigInterface $config
     * @param \Lofmp\SellerMembership\Helper\Data $membershipHelper
     * @param ProductTierPriceExtensionFactory|null $tierPriceExtensionFactory
     * @SuppressWarnings(PHPMD.ExcessiveParameterList)
     */
    public function __construct(
        \Magento\CatalogRule\Model\ResourceModel\RuleFactory $ruleFactory,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        \Magento\Framework\Stdlib\DateTime\TimezoneInterface $localeDate,
        \Magento\Customer\Model\Session $customerSession,
        \Magento\Framework\Event\ManagerInterface $eventManager,
        PriceCurrencyInterface $priceCurrency,
        GroupManagementInterface $groupManagement,
        \Magento\Catalog\Api\Data\ProductTierPriceInterfaceFactory $tierPriceFactory,
        \Magento\Framework\App\Config\ScopeConfigInterface $config,
        \Lofmp\SellerMembership\Helper\Data $membershipHelper,
        ProductTierPriceExtensionFactory $tierPriceExtensionFactory = null
    ) {
        $this->_ruleFactory = $ruleFactory;
        $this->_storeManager = $storeManager;
        $this->_localeDate = $localeDate;
        $this->_customerSession = $customerSession;
        $this->_eventManager = $eventManager;
        $this->priceCurrency = $priceCurrency;
        $this->_groupManagement = $groupManagement;
        $this->tierPriceFactory = $tierPriceFactory;
        $this->config = $config;
        $this->membershipHelper = $membershipHelper;
        $this->tierPriceExtensionFactory = $tierPriceExtensionFactory ?: ObjectManager::getInstance()
            ->get(ProductTierPriceExtensionFactory::class);

        parent::__construct(
            $ruleFactory,
            $storeManager,
            $localeDate,
            $customerSession,
            $eventManager,
            $priceCurrency,
            $groupManagement,
            $tierPriceFactory,
            $config,
            $tierPriceExtensionFactory
        );
    }

    /**
     * Get product final price
     *
     * @param   float $qty
     * @param   \Magento\Catalog\Model\Product $product
     * @return  float
     */
    public function getFinalPrice($qty, $product)
    {
        return parent::getFinalPrice($qty, $product);
    }

    /**
     * {@inheritdoc}
     */
    public function getPrice($product)
    {
        $price = 0;

        $duration = $product->getData('seller_duration');

        if (!$duration) {
            $duration = $product->load($product->getId())
                            ->getData('seller_duration');
        }
        if ($duration && !is_array($duration)) {
            $duration = @json_decode($duration, true);
            $price = @current($duration);
            $price = $price['membership_price'];
        } elseif (is_array($duration)) {
            $duration = $this->membershipHelper->filterMembershipDurationByWebsite($duration);
            $price = @current($duration);
            $price = $price['membership_price'];
        }

        $product->setData('price', $price);

         return parent::getPrice($product);
    }

    /**
     * Get base price with apply Group, Tier, Special prises
     *
     * @param Product $product
     * @param float|null $qty
     *
     * @return float
     */
    public function getBasePrice($product, $qty = null)
    {
        $membership = $product->getCustomOption('seller_duration');

        if (!$membership) {
            return parent::getBasePrice($product, $qty);
        }

        $membership = @unserialize($membership->getValue());

        $price = isset($membership['membership_price']) ? $membership['membership_price'] : 0;

        return $price;
    }
}
