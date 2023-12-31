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

namespace Lof\MarketPlace\Block\Product;

/**
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 */
class View extends \Magento\Catalog\Block\Product\View
{
    /**
     * Group Collection
     */
    protected $_sellerCollection;

    /**
     * @var \Magento\Catalog\Helper\Category
     */
    protected $_sellerHelper;

    /**
     * @var \Magento\Framework\App\ResourceConnection
     */
    public $_resource;

    /**
     * @var \Lof\MarketPlace\Model\Vacation
     */
    protected $vacation;

    /**
     * @var \Lof\MarketPlace\Model\Orderitems
     */
    protected $orderitems;

    /**
     * @var \Lof\MarketPlace\Model\Rating
     */
    protected $rating;

    /**
     * @var \Lof\MarketPlace\Model\ResourceModel\Rating\CollectionFactory
     */
    protected $collectionRateFactory;

    /**
     * @var \Magento\Catalog\Model\ResourceModel\Product\CollectionFactory
     */
    protected $productCollection;

    /**
     * @var mixed|array
     */
    protected $_sellerRate = [];

    /**
     * View constructor.
     *
     * @param \Magento\Catalog\Block\Product\Context $context
     * @param \Magento\Framework\Url\EncoderInterface $urlEncoder
     * @param \Magento\Framework\Json\EncoderInterface $jsonEncoder
     * @param \Magento\Framework\Stdlib\StringUtils $string
     * @param \Magento\Catalog\Helper\Product $productHelper
     * @param \Magento\Catalog\Model\ProductTypes\ConfigInterface $productTypeConfig
     * @param \Magento\Framework\Locale\FormatInterface $localeFormat
     * @param \Magento\Customer\Model\Session $customerSession
     * @param \Magento\Catalog\Api\ProductRepositoryInterface $productRepository
     * @param \Magento\Framework\Pricing\PriceCurrencyInterface $priceCurrency
     * @param \Lof\MarketPlace\Model\Vacation $vacation
     * @param \Lof\MarketPlace\Helper\Data $sellerHelper
     * @param \Lof\MarketPlace\Model\Seller $sellerCollection
     * @param \Magento\Framework\App\ResourceConnection $resource
     * @param \Lof\MarketPlace\Model\Orderitems $orderitems
     * @param \Magento\Catalog\Model\ResourceModel\Product\CollectionFactory $productCollection
     * @param \Lof\MarketPlace\Model\ResourceModel\Rating\CollectionFactory $collectionRateFactory
     * @param array $data
     * @SuppressWarnings(PHPMD.ExcessiveParameterList)
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function __construct(
        \Magento\Catalog\Block\Product\Context $context,
        \Magento\Framework\Url\EncoderInterface $urlEncoder,
        \Magento\Framework\Json\EncoderInterface $jsonEncoder,
        \Magento\Framework\Stdlib\StringUtils $string,
        \Magento\Catalog\Helper\Product $productHelper,
        \Magento\Catalog\Model\ProductTypes\ConfigInterface $productTypeConfig,
        \Magento\Framework\Locale\FormatInterface $localeFormat,
        \Magento\Customer\Model\Session $customerSession,
        \Magento\Catalog\Api\ProductRepositoryInterface $productRepository,
        \Magento\Framework\Pricing\PriceCurrencyInterface $priceCurrency,
        \Lof\MarketPlace\Model\Vacation $vacation,
        \Lof\MarketPlace\Helper\Data $sellerHelper,
        \Lof\MarketPlace\Model\Seller $sellerCollection,
        \Magento\Framework\App\ResourceConnection $resource,
        \Lof\MarketPlace\Model\Orderitems $orderitems,
        \Magento\Catalog\Model\ResourceModel\Product\CollectionFactory $productCollection,
        \Lof\MarketPlace\Model\ResourceModel\Rating\CollectionFactory $collectionRateFactory,
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
            $customerSession,
            $productRepository,
            $priceCurrency
        );
        $this->_sellerCollection = $sellerCollection;
        $this->_sellerHelper = $sellerHelper;
        $this->vacation = $vacation;
        $this->_resource = $resource;
        $this->orderitems = $orderitems;
        $this->productCollection = $productCollection;
        $this->collectionRateFactory = $collectionRateFactory;
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
     *
     */
    public function collection()
    {
        $productCollection = $this->productCollection;
        $collection = $productCollection->create()
            ->addAttributeToSelect('*')
            ->load();
        $collection->getSelect()->join(
            ['lof_marketplace_seller'],
            'lof_marketplace_seller.seller_id = e.seller_id',
            []
        )->columns(['seller_name' => 'lof_marketplace_seller.name']);
        $sellerName = [];
        foreach ($collection->getData() as $value) {
            $sellerName[] = $value['seller_name'];
        }
    }

    /**
     * @return bool|\Magento\Framework\Data\Collection\AbstractDb|\Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection|null
     */
    public function getSellerCollection()
    {
        $sellerIds = $this->getSellerId();
        if ($sellerIds) {
            $collection = $this->_sellerCollection->getCollection()
                ->setOrder('position', 'ASC')
                ->addFieldToFilter('status', 1);
            $collection->getSelect()->where('seller_id IN (?)', $sellerIds);
            return $collection;
        }
        return false;
    }

    /**
     * @return int|array
     */
    public function getSellerId()
    {
        $product = $this->getProduct();
        $objectManager = \Magento\Framework\App\ObjectManager::getInstance();
        $sellerProduct = $objectManager->get(\Lof\MarketPlace\Model\SellerProduct::class)
            ->load($product->getId(), 'product_id');
        return $sellerProduct->getData() ? $sellerProduct->getSellerId() : 0;
    }

    /**
     * @return \Magento\Framework\Data\Collection\AbstractDb|\Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection|null
     */
    public function getVacation()
    {
        $vacation = $this->vacation->getCollection()
            ->addFieldToFilter('seller_id', $this->getSellerId())
            ->addFieldToFilter('status', 1);
        return $vacation;
    }

    /**
     * @return \Magento\Framework\Data\Collection\AbstractDb|\Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection
     */
    public function checkVacation()
    {
        $today = (new \DateTime())->format('Y-m-d');
        $vacation = $this->getVacation()
            ->addFieldToFilter('from_date', ['lteq' => $today])
            ->addFieldToFilter('to_date', ['gt' => $today]);
        return $vacation;
    }

    /**
     * @return string|void
     */
    public function _toHtml()
    {
        if (!$this->_sellerHelper->getConfig('product_view_page/enable_seller_info')) {
            return;
        }

        return parent::_toHtml();
    }

    /**
     * @return int
     */
    public function getTotalSales()
    {
        $total = 0;
        if ((int)$this->_sellerHelper->getConfig("general_settings/show_total_sales")) {
            $orderitems = $this->orderitems->getCollection()
                ->addFieldToFilter('seller_id', $this->getSellerId())
                ->addFieldToFilter('status', 'complete');
            foreach ($orderitems as $_orderitems) {
                $total = $total + $_orderitems->getProductQty();
            }
        }
        return $total;
    }

    /**
     * @return \Lof\MarketPlace\Model\ResourceModel\Rating\Collection
     */
    public function getRating()
    {
        return $this->collectionRateFactory->create()
            ->addFieldToFilter('seller_id', $this->getSellerId());
    }

    /**
     * @return array
     * @SuppressWarnings(PHPMD.CyclomaticComplexity)
     * @SuppressWarnings(PHPMD.NPathComplexity)
     */
    public function getRate()
    {
        $sellerId = $this->getSellerId();
        if ($sellerId && !isset($this->_sellerRate[$sellerId])) {
            $count = $totalRate = 0;
            $rate1 = $rate2 = $rate3 = $rate4 = $rate5 = 0;
            foreach ($this->getRating() as $rating) {
                if ($rating->getData('rate1') > 0) {
                    $count++;
                    $totalRate = $totalRate + $rating->getData('rate1');
                    if ($rating->getData('rate1') == 1) {
                        $rate1++;
                    } elseif ($rating->getData('rate1') == 2) {
                        $rate2++;
                    } elseif ($rating->getData('rate1') == 3) {
                        $rate3++;
                    } elseif ($rating->getData('rate1') == 4) {
                        $rate4++;
                    } elseif ($rating->getData('rate1') == 5) {
                        $rate5++;
                    }
                }
                if ($rating->getData('rate2') > 0) {
                    $count++;
                    $totalRate = $totalRate + $rating->getData('rate2');
                    if ($rating->getData('rate2') == 1) {
                        $rate1++;
                    } elseif ($rating->getData('rate2') == 2) {
                        $rate2++;
                    } elseif ($rating->getData('rate2') == 3) {
                        $rate3++;
                    } elseif ($rating->getData('rate2') == 4) {
                        $rate4++;
                    } elseif ($rating->getData('rate2') == 5) {
                        $rate5++;
                    }
                }
                if ($rating->getData('rate3') > 0) {
                    $count++;
                    $totalRate = $totalRate + $rating->getData('rate3');
                    if ($rating->getData('rate3') == 1) {
                        $rate1++;
                    } elseif ($rating->getData('rate3') == 2) {
                        $rate2++;
                    } elseif ($rating->getData('rate3') == 3) {
                        $rate3++;
                    } elseif ($rating->getData('rate3') == 4) {
                        $rate4++;
                    } elseif ($rating->getData('rate3') == 5) {
                        $rate5++;
                    }
                }
            }
            $data = [];
            if ($count > 0) {
                $average = ($totalRate / $count);
            } else {
                $average = 0;
            }
            $data['count'] = $count;
            $data['total_rate'] = $totalRate;
            $data['average'] = $average;
            $data['rate'] = [];
            $data['rate'][1] = $rate1;
            $data['rate'][2] = $rate2;
            $data['rate'][3] = $rate3;
            $data['rate'][4] = $rate4;
            $data['rate'][5] = $rate5;
            $this->_sellerRate[$sellerId] = $data;
        }
        return $this->_sellerRate[$sellerId];
    }
}
