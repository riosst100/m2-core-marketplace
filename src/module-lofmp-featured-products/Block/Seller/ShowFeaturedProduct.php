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
namespace Lofmp\FeaturedProducts\Block\Seller;

use Magento\Catalog\Api\CategoryRepositoryInterface;
use Magento\Catalog\Model\Product\Visibility;
use Magento\Catalog\Model\ResourceModel\Product\CollectionFactory;
use Magento\CatalogWidget\Model\Rule;
use Magento\Framework\App\Http\Context;
use Magento\Catalog\Block\Product\Context as ProductContext;
use Magento\Framework\Serialize\Serializer\Json;
use Magento\Framework\Url\EncoderInterface;
use Magento\Framework\View\LayoutFactory;
use Magento\Rule\Model\Condition\Sql\Builder;
use Magento\Widget\Helper\Conditions;
use Lofmp\FeaturedProducts\Helper\ConfigData;
use Magento\Framework\Stdlib\DateTime\DateTime;
use Magento\Framework\Stdlib\DateTime\TimezoneInterface;
use Lofmp\FeaturedProducts\Model\Product as SellerFeaturedProduct;

class ShowFeaturedProduct extends \Magento\CatalogWidget\Block\Product\ProductsList
{

    /**
     * @var \Lofmp\FeaturedProducts\Helper\ConfigData
     */
    protected $moduleConfigData;

    /**
     * @var Magento\Framework\Stdlib\DateTime\DateTime
     */
    protected $_dateTime;

    /**
     * @var TimezoneInterface
     */
    protected $_timezoneInterface;

    /**
     * @var SellerFeaturedProduct
     */
    protected $sellerFeaturedProduct;

    /**
     * @param ProductContext $context
     * @param CollectionFactory $productCollectionFactory
     * @param Visibility $catalogProductVisibility
     * @param Context $httpContext
     * @param Builder $sqlBuilder
     * @param Rule $rule
     * @param Conditions $conditionsHelper
     * @param CategoryRepositoryInterface $categoryRepository
     * @param ConfigData $moduleConfigData
     * @param DateTime $dateTime
     * @param TimezoneInterface $timezoneInterface
     * @param SellerFeaturedProduct $sellerFeaturedProduct
     * @param array $data
     * @param Json|null $json
     * @param LayoutFactory|null $layoutFactory
     * @param EncoderInterface|null $urlEncoder
     *
     * @SuppressWarnings(PHPMD.ExcessiveParameterList)
     */
    public function __construct(
        ProductContext $context,
        CollectionFactory $productCollectionFactory,
        Visibility $catalogProductVisibility,
        Context $httpContext,
        Builder $sqlBuilder,
        Rule $rule,
        Conditions $conditionsHelper,
        CategoryRepositoryInterface $categoryRepository,
        ConfigData $moduleConfigData,
        DateTime $dateTime,
        TimezoneInterface $timezoneInterface,
        SellerFeaturedProduct $sellerFeaturedProduct,
        array $data = [],
        Json $json = null,
        LayoutFactory $layoutFactory = null,
        EncoderInterface $urlEncoder = null
    ) {
        parent::__construct(
            $context,
            $productCollectionFactory,
            $catalogProductVisibility,
            $httpContext,
            $sqlBuilder,
            $rule,
            $conditionsHelper,
            $data,
            $json,
            $layoutFactory,
            $urlEncoder,
            $categoryRepository
        );
        $this->moduleConfigData = $moduleConfigData;
        $this->_dateTime = $dateTime;
        $this->_timezoneInterface = $timezoneInterface;
        $this->sellerFeaturedProduct = $sellerFeaturedProduct;
    }

    /**
     * Get date time
     *
     * @return \Magento\Framework\Stdlib\DateTime\DateTime
     */
    public function getDateTime()
    {
        return $this->_dateTime;
    }

    /**
     * Get timezone date time
     *
     * @param string $dateTime = "today"
     * @return string
     */
    public function getTimezoneDateTime($dateTime = "today")
    {
        if($dateTime === "today" || !$dateTime){
            $dateTime = $this->_dateTime->gmtDate();
        }

        $today = $this->_timezoneInterface
            ->date(
                new \DateTime($dateTime)
            )->format('Y-m-d H:i:s');
        return $today;
    }

    /**
     * @return \Magento\Catalog\Model\ResourceModel\Product\Collection
     */
    public function createCollection()
    {
        $sellerId = $this->getRequest()->getParam('seller_id');
        return $this->sellerFeaturedProduct->getCollection($sellerId);

        // $currentDate = $this->getTimezoneDateTime();

        // $productCollection = $this->productCollectionFactory->create();
        // $resource = $productCollection->getResource();
        // $productCollection->addAttributeToSelect('*')
        //     ->joinTable(
        //         ['featured_product' => $resource->getTable('lofmp_featuredproducts_product')],
        //         'product_id = entity_id',
        //         [
        //             'seller_id' => 'seller_id',
        //             'featured_from' => 'featured_from',
        //             'featured_to' => 'featured_to',
        //             'sort_order' => 'sort_order'
        //         ]
        //     )
        //     ->addFieldToFilter('seller_id', ['eq' => $sellerId])
        //     ->addFieldToFilter('featured_from', [
        //         ['lteq' => $currentDate],
        //         ['null' => true]
        //     ])
        //     ->addFieldToFilter('featured_to', [
        //         ['gteq' => $currentDate],
        //         ['null' => true]
        //     ])
        //     ->setOrder('sort_order', 'ASC');

        // return $productCollection;
    }

    /**
     * @return \Lofmp\FeaturedProducts\Helper\ConfigData
     */
    public function getModuleConfigData()
    {
        return $this->moduleConfigData;
    }
}
