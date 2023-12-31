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

namespace Lof\MarketPlace\Block\Seller\Product;

use Magento\Catalog\Api\CategoryRepositoryInterface;
use Magento\Eav\Model\Entity\Collection\AbstractCollection;

class ListProduct extends \Magento\Catalog\Block\Product\ListProduct
{
    /**
     * Default toolbar block name
     *
     * @var string
     */
    protected $_defaultToolbarBlock = \Magento\Catalog\Block\Product\ProductList\Toolbar::class;

    /**
     * Product Collection
     *
     * @var AbstractCollection
     */
    protected $_productCollection;

    /**
     * Catalog layer
     *
     * @var \Magento\Catalog\Model\Layer
     */
    protected $_catalogLayer;

    /**
     * @var \Magento\Framework\Data\Helper\PostHelper
     */
    protected $_postDataHelper;

    /**
     * @var \Magento\Framework\Url\Helper\Data
     */
    protected $urlHelper;

    /**
     * @var CategoryRepositoryInterface
     */
    protected $categoryRepository;

    /**
     * Catalog product visibility
     *
     * @var \Magento\Catalog\Model\Product\Visibility
     */
    protected $_catalogProductVisibility;

    /**
     * @var \Magento\Catalog\Model\ResourceModel\Product\CollectionFactory
     */
    protected $_productCollectionFactory;

    /**
     * @var \Lof\MarketPlace\Model\VacationFactory
     */
    protected $_vacationFactory;

    /**
     * @var \Lof\MarketPlace\Helper\DateTime
     */
    protected $_helperDateTime;

    /**
     * @var array
     */
    protected $_seller_vacations = [];

    /**
     * ListProduct constructor.
     *
     * @param \Magento\Catalog\Block\Product\Context $context
     * @param \Magento\Framework\Data\Helper\PostHelper $postDataHelper
     * @param \Magento\Catalog\Model\Layer\Resolver $layerResolver
     * @param CategoryRepositoryInterface $categoryRepository
     * @param \Magento\Framework\Url\Helper\Data $urlHelper
     * @param \Magento\Catalog\Model\ResourceModel\Product\CollectionFactory $productCollectionFactory
     * @param \Magento\Catalog\Model\Product\Visibility $catalogProductVisibility
     * @param \Lof\MarketPlace\Model\VacationFactory $vacationFactory
     * @param \Lof\MarketPlace\Helper\DateTime $helperDateTime
     * @param array $data
     * @SuppressWarnings(PHPMD.ExcessiveParameterList)
     */
    public function __construct(
        \Magento\Catalog\Block\Product\Context $context,
        \Magento\Framework\Data\Helper\PostHelper $postDataHelper,
        \Magento\Catalog\Model\Layer\Resolver $layerResolver,
        CategoryRepositoryInterface $categoryRepository,
        \Magento\Framework\Url\Helper\Data $urlHelper,
        \Magento\Catalog\Model\ResourceModel\Product\CollectionFactory $productCollectionFactory,
        \Magento\Catalog\Model\Product\Visibility $catalogProductVisibility,
        \Lof\MarketPlace\Model\VacationFactory $vacationFactory,
        \Lof\MarketPlace\Helper\DateTime $helperDateTime,
        array $data = []
    ) {
        $this->_productCollectionFactory = $productCollectionFactory;
        $this->_catalogProductVisibility = $catalogProductVisibility;
        $this->_catalogLayer = $layerResolver->get();
        $this->_postDataHelper = $postDataHelper;
        $this->categoryRepository = $categoryRepository;
        $this->urlHelper = $urlHelper;
        $this->_helperDateTime = $helperDateTime;
        $this->_vacationFactory = $vacationFactory;

        parent::__construct(
            $context,
            $postDataHelper,
            $layerResolver,
            $categoryRepository,
            $urlHelper,
            $data
        );
    }

    /**
     * @return mixed|null
     */
    public function getSeller()
    {
        return $this->_coreRegistry->registry('current_seller');
    }

    /**
     * @return \Magento\Catalog\Model\ResourceModel\Product\Collection|AbstractCollection
     */
    public function _getProductCollection()
    {
        if ($this->_productCollection === null) {
            $layer = $this->getLayer();
            $seller = $this->_coreRegistry->registry('current_seller');
            if ($seller) {
                $layer->setCurrentSeller($seller);
            }
            $sellerId = $seller->getId();
            $collection = $this->_productCollectionFactory->create();
            $collection->setVisibility($this->_catalogProductVisibility->getVisibleInCatalogIds())
                ->addAttributeToSelect('*')
                ->addAttributeToFilter('seller_id', (int)$sellerId)
                ->addAttributeToFilter('approval', 2);
            $this->_productCollection = $collection;
        }
        return $this->_productCollection;
    }

    /**
     * @param int $sellerId
     * @return Object
     */
    public function getSellerVacation($sellerId)
    {
        if (!isset($this->_seller_vacations[$sellerId])) {
            $vacation = $this->_vacationFactory->create()->load($sellerId, 'seller_id');
            $this->_seller_vacations[$sellerId] = $vacation;
        }
        return $this->_seller_vacations[$sellerId];
    }

    /**
     * @param int $sellerId
     * @return string
     */
    public function getVacationText($sellerId)
    {
        $text = "";
        $vacation = $this->getSellerVacation($sellerId);
        if ($vacation && (int)$vacation->getData("status") == 1) {
            $today = $this->_helperDateTime->getTimezoneDateTime();
            $fromDate = $this->_helperDateTime->getTimezoneDateTime($vacation->getData('from_date'));
            $toDate = $this->_helperDateTime->getTimezoneDateTime($vacation->getData('to_date'));
            if (strtotime($fromDate) <= $today && strtotime($toDate) > $today) {
                $text = $vacation->getData('text_add_cart');
            }
        }
        return $text;
    }
}
