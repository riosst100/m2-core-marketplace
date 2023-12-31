<?php
/**
 * Landofcoder
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Landofcoder.com license that is
 * available through the world-wide-web at this URL:
 * http://www.landofcoder.com/license-agreement.html
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade this extension to newer
 * version in the future.
 *
 * @category   Landofcoder
 * @package    Lofmp_FavoriteSeller
 * @copyright  Copyright (c) 2018 Landofcoder (http://www.landofcoder.com/)
 * @license    http://www.landofcoder.com/LICENSE-1.0.html
 */
namespace Lofmp\FavoriteSeller\Block\Product;

class View extends \Magento\Catalog\Block\Product\View
{
    /**
     * @var \Lof\MarketPlace\Model\Seller
     */
    protected $sellerCollection;

    /**
     * @var \Lof\MarketPlace\Helper\Data
     */
    protected $sellerHelper;

    /**
     * @var \Lof\MarketPlace\Model\Vacation
     */
    protected $vacation;

    /**
     * @var \Lofmp\FavoriteSeller\Model\ResourceModel\Subscription\CollectionFactory
     */
    protected $subscriptionCollectionFactory;

    /**
     * @var \Magento\Framework\App\ResourceConnection
     */
    protected $resource;

    /**
     * @var \Magento\Customer\Model\SessionFactory
     */
    protected $customerSessionFactory;

    /**
     * @param \Magento\Catalog\Block\Product\Context $context
     * @param \Magento\Framework\Url\EncoderInterface $urlEncoder
     * @param \Magento\Framework\Json\EncoderInterface $jsonEncoder
     * @param \Magento\Framework\Stdlib\StringUtils $string
     * @param \Magento\Catalog\Helper\Product $productHelper
     * @param \Magento\Catalog\Model\ProductTypes\ConfigInterface $productTypeConfig
     * @param \Magento\Framework\Locale\FormatInterface $localeFormat
     * @param \Magento\Customer\Model\SessionFactory $customerSessionFactory
     * @param \Magento\Catalog\Api\ProductRepositoryInterface $productRepository
     * @param \Magento\Framework\Pricing\PriceCurrencyInterface $priceCurrency
     * @param \Lof\MarketPlace\Helper\Data $sellerHelper
     * @param \Lof\MarketPlace\Model\Seller $sellerCollection
     * @param \Lof\MarketPlace\Model\Vacation $vacation
     * @param \Lofmp\FavoriteSeller\Model\ResourceModel\Subscription\CollectionFactory $subscriptionCollectionFactory
     * @param \Magento\Framework\App\ResourceConnection $resource
     * @param array $data
     */
    public function __construct(
        \Magento\Catalog\Block\Product\Context               $context,
        \Magento\Framework\Url\EncoderInterface              $urlEncoder,
        \Magento\Framework\Json\EncoderInterface             $jsonEncoder,
        \Magento\Framework\Stdlib\StringUtils                $string,
        \Magento\Catalog\Helper\Product                      $productHelper,
        \Magento\Catalog\Model\ProductTypes\ConfigInterface  $productTypeConfig,
        \Magento\Framework\Locale\FormatInterface            $localeFormat,
        \Magento\Customer\Model\SessionFactory               $customerSessionFactory,
        \Magento\Catalog\Api\ProductRepositoryInterface      $productRepository,
        \Magento\Framework\Pricing\PriceCurrencyInterface    $priceCurrency,
        \Lof\MarketPlace\Helper\Data                         $sellerHelper,
        \Lof\MarketPlace\Model\Seller                        $sellerCollection,
        \Lof\MarketPlace\Model\Vacation                      $vacation,
        \Lofmp\FavoriteSeller\Model\ResourceModel\Subscription\CollectionFactory $subscriptionCollectionFactory,
        \Magento\Framework\App\ResourceConnection            $resource,
        array $data = []
    ) {
        parent::__construct(
            $context,
            $urlEncoder,
            $jsonEncoder,
            $string,
            $productHelper,
            $productTypeConfig,
            $localeFormat,
            $customerSessionFactory->create(),
            $productRepository,
            $priceCurrency,
            $data
        );
        $this->sellerCollection = $sellerCollection;
        $this->sellerHelper = $sellerHelper;
        $this->vacation      = $vacation;
        $this->subscriptionCollectionFactory = $subscriptionCollectionFactory;
        $this->resource     = $resource;
        $this->customerSessionFactory = $customerSessionFactory;
    }

    /**
     * Retrieve current product model
     *
     * @return \Magento\Catalog\Model\Product
     */
    public function getProduct()
    {
        return $this->_coreRegistry->registry('current_product');
    }

    /**
     * @return int|null
     */
    public function getSellerId() {
        $product = $this->getProduct();
        $connection = $this->resource->getConnection();
        $table_name = $this->resource->getTableName('lof_marketplace_product');
        $sellerIds = $connection->fetchCol("SELECT seller_id FROM ".$table_name." WHERE product_id = ".(int)$product->getId());

        return count($sellerIds) == 0 ? null : $sellerIds[0];
    }

    /**
     * @return bool
     */
    public function checkCustomerLoggedIn(){
        return (bool)$this->customerSessionFactory->create()->isLoggedIn();
    }

    /**
     * @return int
     */
    public function getCustomerId()
    {
        return parent::getCustomerId();
    }

    /**
     * @return bool
     */
    public function isSubscribed(){
        $sellerId = $this->getSellerId() ? $this->getSellerId() : 0;
        $customerId = $this->getCustomerId();

        $subscriptionCollection = $this->subscriptionCollectionFactory->create();
        $subscriptionSet = $subscriptionCollection->addFieldToFilter('customer_id', $customerId)
                            ->addFieldToFilter('seller_id', $sellerId)
                            ->load();

        return $subscriptionSet->count() != 0 ? true : false;
    }

    /**
     * @return string|void
     */
    public function _toHtml(){
        if(!$this->sellerHelper->getConfig('product_view_page/enable_seller_info')) return;
        return parent::_toHtml();
    }
}
